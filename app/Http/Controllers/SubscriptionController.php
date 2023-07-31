<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as LaravelLog;
use Acelle\Model\Subscription;
use Acelle\Model\Setting;
use Acelle\Model\Plan;
use Acelle\Model\Invoice;
use Acelle\Cashier\Cashier;
use Acelle\Cashier\Services\StripeGatewayService;
use Carbon\Carbon;
use Acelle\Model\SubscriptionLog;
use Acelle\Library\Facades\Billing;
use Acelle\Library\Facades\SubscriptionFacade;
use Acelle\Model\Currency;
use Acelle\Model\InvoiceItem;
use Acelle\Model\MailIpAddress;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        // cronjob check
        SubscriptionFacade::checkExpiration();
        SubscriptionFacade::createRenewInvoice();
        $customers = \Acelle\Model\Customer::all();
        foreach ($customers as $customer) {
            SubscriptionFacade::checkAndAutoPayRenewInvoiceByCustomer($customer);
        }

        // init
        $customer = $request->user()->customer;
        $subscription = $customer->getNewOrActiveSubscription();

        // 1. HAVE NOT HAD NEW/ACTIVE SUBSCRIPTION YET
        if (!$subscription) {
            // User chưa có subscription sẽ được chuyển qua chọn plan
            return redirect()->action('SubscriptionController@selectPlan');
        }

        // 2. IF PLAN NOT ACTIVE
        if (!$subscription->plan->isActive()) {
            return response()->view('errors.general', [ 'message' => __('messages.subscription.error.plan-not-active', [ 'name' => $subscription->plan->name]) ]);
        }

        // 3. SUBSCRIPTION IS NEW
        if ($subscription->isNew()) {
            $invoice = $subscription->getItsOnlyUnpaidInitInvoice();

            return redirect()->action('SubscriptionController@payment', [
                'invoice_uid' => $invoice->uid,
            ]);
        }
        if (!empty($subscription->user_plan)) {
            $currency = [
                'amount' => $subscription->priceConverter(json_decode($subscription->user_plan)),
                'format' => Currency::where('code', json_decode($subscription->user_plan)->currency)->first(),
            ];
        }else{
            $tempCurrency = Currency::where('code', 'USD')->first();
            $currency = [
                'amount' => $tempCurrency->usd_value,
                'format' => $tempCurrency,
            ];
        }
        // 3. SUBSCRIPTION IS ACTIVE, SHOW DETAILS PAGE
        return view('subscription.index', [
            'subscription' => $subscription,
            'plan' => $subscription->plan,
            'currency' => $currency,
            'ipaddress' => MailIpAddress::where('user_id', auth()->id())->first(),
            'ipaddresslog' => Invoice::where('type', 'ip_address_new')->with('invoiceItems')->get()
        ]);
    }

    public function selectPlan(Request $request)
    {
        // init
        $customer = $request->user()->customer;
        $subscription = $customer->getNewOrActiveSubscription();

        return view('subscription.selectPlan', [
            'plans' => Plan::getAvailablePlans(),
            'subscription' => $subscription,
            'getLastCancelledOrEndedSubscription' => $customer->getLastCancelledOrEndedSubscription(),
            'currency' => Currency::where('status', 'active')->get()
        ]);
    }

    public function init(Request $request)
    {
        // Get current customer
        $customer = $request->user()->customer;
        $subscription = $customer->subscription;
        $plan = Plan::findByUid($request->plan_uid);

        // try to save old invoice billing info
        if ($subscription &&
            !$subscription->isEnded() &&
            $subscription->getUnpaidInvoice()
        ) {
            $oldInvoice = $subscription->getUnpaidInvoice();

            $oldBillingInfo = [
                'billing_first_name' => $oldInvoice->billing_first_name,
                'billing_last_name' => $oldInvoice->billing_last_name,
                'billing_address' => $oldInvoice->billing_address,
                'billing_email' => $oldInvoice->billing_email,
                'billing_phone' => $oldInvoice->billing_phone,
                'billing_country_id' => $oldInvoice->billing_country_id,
            ];
        }

        // create new subscription
        $subscription = $customer->subscription;
        $subscription = $customer->assignPlan($plan,$request);

        // create init invoice
        if (!$subscription->invoices()->new()->count()) {
            $invoice = $subscription->createInitInvoice($request);
        }

        // copy old billing info
        if (isset($oldBillingInfo)) {
            $invoice = $subscription->getUnpaidInvoice();

            $invoice->fill($oldBillingInfo);
            $invoice->save();
        }

        // Check if subscriotion is new
        return redirect()->action('SubscriptionController@billingInformation');
    }

    public function assignPlan(Request $request)
    {
        $customer = $request->user()->customer;
        $plan = Plan::findByUid($request->plan_uid);

        // already has subscription
        if ($customer->getCurrentActiveSubscription()) {
            throw new \Exception('Customer already has active subscription!');
        }

        // subscription hiện đang new. Customer muốn thay đổi plan khác?
        // delete luôn subscription
        if ($customer->getCurrentNewSubscription()) {
            $customer->getCurrentNewSubscription()->deleteAndCleanup();
        }

        // assign plan
        $customer->assignPlan($plan);

        // Check if subscriotion is new
        return redirect()->action('SubscriptionController@billingInformation');
    }

    public function payment(Request $request)
    {
        // Get current customer
        $customer = $request->user()->customer;

        // get unpaid invoice
        $invoice = $customer->invoices()->unpaid()->where('uid', '=', $request->invoice_uid)->first();

        // no unpaid invoice found
        if (!$invoice) {
            // throw new \Exception('Can not find unpaid invoice with id:' . $request->invoice_uid);
            // just redirect to index
            return redirect()->action('SubscriptionController@index');
        }

        // nếu đang có pending transaction thì luôn show màn hình pending
        if ($invoice->getPendingTransaction()) {
            return view('subscription.pending', [
                'invoice' => $invoice,
            ]);
        }

        // luôn luôn require billing information
        if (!$invoice->hasBillingInformation()) {
            return redirect()->action('SubscriptionController@billingInformation');
        }

        return view('subscription.payment', [
            'invoice' => $invoice,
        ]);
    }

    public function confirmFree(Request $request)
    {
        // get invoice
        $invoice = $request->user()->customer->invoices()->where('uid', '=', $request->invoice_uid)->first();

        if (!$invoice->isFree()) {
            throw new \Exception('Invoice is not free!');
        }

        if ($request->payment_method) {
            $request->user()->customer->updatePaymentMethod([
                'method' => $request->payment_method,
            ]);
        }

        $invoice->confirmWithoutPayment();

        $request->session()->flash('alert-success', trans('messages.invoice.confirmed'));
        return redirect()->action('SubscriptionController@index');
    }

    public function cancelInvoice(Request $request, $uid)
    {
        $invoice = \Acelle\Model\Invoice::findByUid($uid);

        // return to select plan if sub is NEW
        if ($request->user()->customer->getNewSubscription()) {
            return redirect()->action('SubscriptionController@selectPlan');
        }

        if (!$request->user()->customer->can('delete', $invoice)) {
            return $this->notAuthorized();
        }

        $invoice->cancel();

        // Redirect to my subscription page
        $request->session()->flash('alert-success', trans('messages.invoice.cancelled'));
        return redirect()->action('SubscriptionController@index');
    }

    public function checkout(Request $request)
    {
        $customer = $request->user()->customer;
        $invoice = $customer->invoices()->unpaid()->where('uid', '=', $request->invoice_uid)->first();

        // no unpaid invoice found
        if (!$invoice) {
            throw new \Exception('Customer subscription does not have any unpaid invoice!');
        }

        // Luôn đặt payment method mặc định cho customer là lần chọn payment gần nhất
        $request->user()->customer->updatePaymentMethod([
            'method' => $request->payment_method,
        ]);

        // Bỏ qua việc nhập card information khi subscribe plan with trial
        if (\Acelle\Model\Setting::get('not_require_card_for_trial') == 'yes' && $invoice->isInitInvoiceWithTrial()) {
            $invoice->checkout($customer->getPreferredPaymentGateway(), function () {
                return new \Acelle\Cashier\Library\TransactionVerificationResult(\Acelle\Cashier\Library\TransactionVerificationResult::RESULT_DONE);
            });

            return redirect()->action('SubscriptionController@index');
        }

        // redirect to service checkout
        return redirect()->away($customer->getPreferredPaymentGateway()->getCheckoutUrl($invoice));
    }

    public function checkoutIp(Request $request)
    {
        $customer = $request->user()->customer;
        $subscription = $customer->getNewOrActiveSubscription();
        // if ($customer->useOwnSendingServer()) {
            foreach ($subscription->plan->plansSendingServers as $serverInfo) {
                if($serverInfo->is_primary){
                    $emptyIpAddress = MailIpAddress::where('sending_server_id', $serverInfo->sending_server_id)->whereNull('user_id')->first();
                }
            }
        // }
        if (empty($emptyIpAddress)) {
            $request->session()->flash('alert-danger', trans('messages.not_available'));
            return back();
        }
        if ($subscription->plan->frequency_unit == 'year') {
            $amount = $emptyIpAddress->price_yearly;
        }elseif ($subscription->plan->frequency_unit == 'month') {
            $amount = ($emptyIpAddress->price_monthly/30)*now()->diffInDays($subscription->current_period_ends_at);
        }else{
            $amount = $emptyIpAddress->price_monthly/30;
        }

        $invoice = Invoice::where(['customer_id' => $request->user()->customer->id, 'type' => 'ip_address_new'])->first();
        if(empty($invoice)){
            $invoice = Invoice::createInvoice('ip_address_new', 'Ip address', 'Ip address purchase on '.date('m/d/Y'),
                $request->user()->customer->id,
                Currency::where('code', json_decode($subscription->user_plan)->currency)->first()->id,
                $customer->getDefaultBillingAddress(),
                [
                    new InvoiceItem([
                        'item_id' => $subscription->id,
                        'item_type' => get_class($this),
                        'amount' => $amount,
                        'title' => 'Ip Address',
                        'description' => 'Ip address purchase'. $emptyIpAddress->ip_address,
                    ]),
                ],[
                    'ipaddress' => $emptyIpAddress->ip_address,
                ]);
        }
        $request->user()->customer->updatePaymentMethod([
            'method' => request()->user()->customer->getPreferredPaymentGateway()->getType(),
        ]);
        return redirect()->away($customer->getPreferredPaymentGateway()->getCheckoutUrl($invoice));
    }

    public function billingInformation(Request $request)
    {
        $customer = $request->user()->customer;
        $subscription = $customer->getNewOrActiveSubscription();
        $invoice = $subscription->getUnpaidInvoice();
        $billingAddress = $customer->getDefaultBillingAddress();
        // Save posted data
        if ($request->isMethod('post')) {
            $validator = $invoice->updateBillingInformation($request->all());

            // redirect if fails
            if ($validator->fails()) {
                return response()->view('subscription.billingInformation', [
                    'invoice' => $invoice,
                    'billingAddress' => $billingAddress,
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Khúc này customer cập nhật thông tin billing information cho lần tiếp theo
            $customer->updateBillingInformationFromInvoice($invoice);

            $request->session()->flash('alert-success', trans('messages.billing_address.updated'));

            // return to subscription
            return redirect()->action('SubscriptionController@payment', [
                'invoice_uid' => $invoice->uid,
            ]);
        }
        return view('subscription.billingInformation', [
            'invoice' => $invoice,
            'billingAddress' => $billingAddress,
        ]);
    }

    /**
     * Change plan.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     **/
    public function changePlan(Request $request)
    {
        $customer = $request->user()->customer;
        $subscription = $customer->getCurrentActiveSubscription();
        $gateway = $customer->getPreferredPaymentGateway();
        $plans = Plan::getAvailablePlans();

        // Authorization
        if (!$request->user()->customer->can('changePlan', $subscription)) {
            return $this->notAuthorized();
        }

        //
        if ($request->isMethod('post')) {
            $newPlan = Plan::findByUid($request->plan_uid);

            try {
                $changePlanInvoice = null;

                \DB::transaction(function () use ($subscription, $newPlan, &$changePlanInvoice) {
                    // set invoice as pending
                    $changePlanInvoice = $subscription->createChangePlanInvoice($newPlan);

                    // Log
                    SubscriptionFacade::log($subscription, SubscriptionLog::TYPE_CHANGE_PLAN_INVOICE, $changePlanInvoice->uid, [
                        'plan' => $subscription->getPlanName(),
                        'new_plan' => $newPlan->name,
                        'amount' => $changePlanInvoice->formattedTotal(),
                    ]);
                });

                // return to subscription
                return redirect()->action('SubscriptionController@payment', [
                    'invoice_uid' => $changePlanInvoice->uid,
                ]);
            } catch (\Exception $e) {
                $request->session()->flash('alert-error', $e->getMessage());
                return redirect()->action('SubscriptionController@index');
            }
        }

        return view('subscription.change_plan', [
            'subscription' => $subscription,
            'gateway' => $gateway,
            'plans' => $plans,
            'changePlan' => true,
            'currency' => Currency::where('status', 'active')->get()
        ]);
    }

    /**
     * Cancel subscription at the end of current period.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function disableRecurring(Request $request)
    {
        if (isSiteDemo()) {
            return response()->json(["message" => trans('messages.operation_not_allowed_in_demo')], 404);
        }

        $customer = $request->user()->customer;
        $subscription = $customer->getNewOrActiveSubscription();

        if ($request->user()->customer->can('disableRecurring', $subscription)) {
            $subscription->disableRecurring();
        }

        // Redirect to my subscription page
        $request->session()->flash('alert-success', trans('messages.subscription.disabled_recurring'));
        return redirect()->action('SubscriptionController@index');
    }


    /**
     * Cancel subscription at the end of current period.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function enableRecurring(Request $request)
    {
        $customer = $request->user()->customer;
        $subscription = $customer->getNewOrActiveSubscription();

        if ($request->user()->customer->can('enableRecurring', $subscription)) {
            $subscription->enableRecurring();
        }

        // Redirect to my subscription page
        $request->session()->flash('alert-success', trans('messages.subscription.enabled_recurring'));
        return redirect()->action('SubscriptionController@index');
    }

    /**
     * Cancel now subscription at the end of current period.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelNow(Request $request)
    {
        if (isSiteDemo()) {
            return response()->json(["message" => trans('messages.operation_not_allowed_in_demo')], 404);
        }

        $customer = $request->user()->customer;
        $subscription = $customer->getNewOrActiveSubscription();

        if ($request->user()->customer->can('cancelNow', $subscription)) {
            $subscription->cancelNow();
        }

        // Redirect to my subscription page
        $request->session()->flash('alert-success', trans('messages.subscription.cancelled_now'));
        return redirect()->action('SubscriptionController@index');
    }

    public function cancelIpNow(Request $request, MailIpAddress $ip)
    {
        if (isSiteDemo()) {
            return response()->json(["message" => trans('messages.operation_not_allowed_in_demo')], 404);
        }

        // if ($request->user()->customer->can('cancelNow', $subscription)) {
        $ip->update([
            'status'  => 'cancel'
        ]);
        // }

        // Redirect to my subscription page
        $request->session()->flash('alert-success', trans('messages.subscription.cancelled_now'));
        return redirect()->action('SubscriptionController@index');
    }

    public function orderBox(Request $request)
    {
        $customer = $request->user()->customer;
        // $subscription = $customer->subscription;
        $subscription = $customer->getNewOrActiveSubscription();

        // // choose a plan
        // if ($request->plan_uid) {
        //     $plan = Plan::findByUid($request->plan_uid);

        //     return view('subscription.orderBox', [
        //         'subscription' => $subscription,
        //         'bill' => [
        //             'title' => trans('messages.subscription.your_order'),
        //             'description' => trans('messages.subscription.your_order.desc', [
        //                 'plan' => $plan->name,
        //             ]),
        //             'bill' => [
        //                 [
        //                     'title' => $plan->name,
        //                     'description' => view('plans._bill_desc', ['plan' => $plan]),
        //                     'price' => format_price($plan->price, $plan->currency->format),
        //                     'tax' => format_price($plan->getTax(), $plan->currency->format),
        //                     'discount' => format_price(0, $plan->currency->format),
        //                 ]
        //             ],
        //             'charge_info' => trans('messages.bill.charge_now'),
        //             'total' => format_price($plan->total(), $plan->currency->format),
        //             'pending' => false,
        //             'invoice_uid' => '',
        //             'type' => \Acelle\Model\Invoice::TYPE_NEW_SUBSCRIPTION,
        //             'plan' => $plan,
        //         ],
        //     ]);
        // }

        // get unpaid invoice
        $invoice = $customer->invoices()->unpaid()->where('uid', '=', $request->invoice_uid)->first();

        // gateway fee
        if ($request->payment_method) {
            $gateway = Billing::getGateway($request->payment_method);

            // update invoice fee if trial and gatewaye need minimal fee for auto billing
            $invoice->updatePaymentServiceFee($gateway);
        }

        return view('subscription.orderBox', [
            'subscription' => $subscription,
            'bill' => $invoice->getBillingInfo(),
            'invoice' => $invoice,
        ]);
    }
}
