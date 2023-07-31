<?php

namespace Acelle\Http\Controllers;

use Acelle\Library\ExtendedSwiftMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as LaravelLog;
use Acelle\Library\Facades\Billing;
use Acelle\Model\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AccountController extends Controller
{
    /**
     * Update user profile.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     **/
    public function profile(Request $request)
    {
        // Get current user
        $user = $request->user();
        $customer = $user->customer;
        $customer->getColorScheme();

        // Authorize
        if (!$request->user()->customer->can('profile', $customer)) {
            return $this->notAuthorized();
        }

        // Save posted data
        if ($request->isMethod('post')) {
            $this->validate($request, $user->rules());

            // Update user account for customer
            $user->fill($request->all());
            // Update password
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }

            $user->save();

            // Save current user info
            $customer->fill($request->all());

            // Upload and save image
            if ($request->hasFile('image')) {
                if ($request->file('image')->isValid()) {
                    // Remove old images
                    $user->uploadProfileImage($request->file('image'));
                }
            }

            // Remove image
            if ($request->_remove_image == 'true') {
                $user->removeProfileImage();
            }

            if ($customer->save()) {
                $request->session()->flash('alert-success', trans('messages.profile.updated'));
            }

            return redirect()->action('AccountController@profile');
        }

        if (!empty($request->old())) {
            $customer->fill($request->old());
            // User info
            $customer->user->fill($request->old());
        }

        return view('account.profile', [
            'customer' => $customer,
            'user' => $request->user(),
        ]);
    }

    /**
     * Update customer contact information.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     **/
    public function contact(Request $request)
    {
        // Get current user
        $customer = $request->user()->customer;
        $contact = $customer->getContact();

        // Create new company if null
        if (!$contact) {
            $contact = new \Acelle\Model\Contact();
        }

        // save posted data
        if ($request->isMethod('post')) {
            // Prenvent save from demo mod
            if (config('app.demo')) {
                return view('somethingWentWrong', ['message' => trans('messages.operation_not_allowed_in_demo')]);
            }

            $this->validate($request, \Acelle\Model\Contact::$rules);

            $contact->fill($request->all());

            // Save current user info
            if ($contact->save()) {
                $customer->contact_id = $contact->id;
                $customer->save();
                $request->session()->flash('alert-success', trans('messages.customer_contact.updated'));
            }
        }

        return view('account.contact', [
            'customer' => $customer,
            'contact' => $contact->fill($request->old()),
        ]);
    }

    /**
     * User logs.
     */
    public function logs(Request $request)
    {
        $logs = $request->user()->customer->logs;

        return view('account.logs', [
            'logs' => $logs,
        ]);
    }

    /**
     * Logs list.
     */
    public function logsListing(Request $request)
    {
        $logs = \Acelle\Model\Log::search($request)->paginate($request->per_page);

        return view('account.logs_listing', [
            'logs' => $logs,
        ]);
    }

    /**
     * Quta logs.
     */
    public function quotaLog(Request $request)
    {
        return view('account.quota_log');
    }

    /**
     * Quta logs 2.
     */
    public function quotaLog2(Request $request)
    {
        return view('account.quota_log_2');
    }

    /**
     * Api token.
     */
    public function api(Request $request)
    {
        // if(!Auth::user()->customer_admin_id){
        //     return view('account.api');
        // }else{
        //     return $this->notAuthorized();
        // } old code
        return view('account.api');
    }

    /**
     * Renew api token.
     */
    public function renewToken(Request $request)
    {
        $user = $request->user();

        $user->api_token = str_random(60);
        $user->save();

        // Redirect to my lists page
        $request->session()->flash('alert-success', trans('messages.user_api.renewed'));

        return redirect()->action('AccountController@api');
    }

    /**
     * Billing.
     */
    public function billing(Request $request)
    {
        return view('account.billing', [
            'customer' => $request->user()->customer,
            'user' => $request->user(),
        ]);
    }

    /**
     * Edit billing address.
     */
    public function editBillingAddress(Request $request)
    {
        $customer = $request->user()->customer;
        $billingAddress = $customer->getDefaultBillingAddress();

        // has no address yet
        if (!$billingAddress) {
            $billingAddress = $customer->newBillingAddress();
        }

        // copy from contacy
        if ($request->same_as_contact == 'true') {
            $billingAddress->copyFromContact();
        }

        // Save posted data
        if ($request->isMethod('post')) {
            list($validator, $billingAddress) = $billingAddress->updateAll($request);

            // redirect if fails
            if ($validator->fails()) {
                return response()->view('account.editBillingAddress', [
                    'billingAddress' => $billingAddress,
                    'errors' => $validator->errors(),
                ], 400);
            }

            $request->session()->flash('alert-success', trans('messages.billing_address.updated'));

            return;
        }

        return view('account.editBillingAddress', [
            'billingAddress' => $billingAddress,
        ]);
    }

    /**
     * Remove payment method
     */
    public function removePaymentMethod(Request $request)
    {
        $customer = $request->user()->customer;

        $customer->removePaymentMethod();
    }

    /**
     * Edit payment method
     */
    public function editPaymentMethod(Request $request)
    {
        // Save posted data
        if ($request->isMethod('post')) {
            if (!Billing::isGatewayRegistered($request->payment_method)) {
                throw new \Exception('Gateway for ' . $request->payment_method . ' is not registered!');
            }

            $gateway = Billing::getGateway($request->payment_method);

            $request->user()->customer->updatePaymentMethod([
                'method' => $request->payment_method,
            ]);

            if ($gateway->supportsAutoBilling()) {
                return redirect()->away($gateway->getAutoBillingDataUpdateUrl($request->return_url));
            }

            return redirect()->away($request->return_url);
        }

        return view('account.editPaymentMethod', [
            'redirect' => $request->redirect ? $request->redirect : action('AccountController@billing'),
        ]);
    }

    public function leftbarState(Request $request)
    {
        $request->session()->put('customer-leftbar-state', $request->state);
    }

    public function wizardColorScheme(Request $request)
    {
        $customer = $request->user()->customer;

        // Save color scheme
        if ($request->isMethod('post')) {
            $customer->color_scheme = $request->color_scheme;
            $customer->theme_mode = $request->theme_mode;
            $customer->save();

            return view('account.wizardMenuLayout');
        }

        return view('account.wizardColorScheme');
    }

    public function wizardMenuLayout(Request $request)
    {
        $customer = $request->user()->customer;

        // Save color scheme
        if ($request->isMethod('post')) {
            $customer->menu_layout = $request->menu_layout;
            $customer->save();
            return;
        }

        return view('account.wizardMenuLayout');
    }

    public function activity(Request $request)
    {
        $currentTimezone = $request->user()->customer->getTimezone();
        return view('account.activity', [
            'currentTimezone' => $currentTimezone
        ]);
    }

    public function saveAutoThemeMode(Request $request)
    {
        $request->session()->put('customer-auto-theme-mode', $request->theme_mode);
    }

    public function changeThemeMode(Request $request)
    {
        $customer = $request->user()->customer;

        // Save color scheme
        if ($request->isMethod('post')) {
            $customer->theme_mode = $request->theme_mode;
            $customer->save();
        }
    }

    public function users(Request $request)
    {
        // authorize
        // if (Gate::denies('read', new \Acelle\Model\User())) {
        //     return $this->notAuthorized();
        // }

        // If admin can view all customer
        // if (!$request->user()->admin->can("readAll", new \Acelle\Model\User())) {
        //     $request->merge(array("admin_id" => $request->user()->admin->id));
        // }

        $customers = \Acelle\Model\User::where('customer_admin_id', Auth::user()->id)->get();

        return view('account.users.users', [
            'customers' => $customers,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        // authorize
        // if (Gate::denies('read', new \Acelle\Model\User())) {
        //     return $this->notAuthorized();
        // }

        // If admin can view all customer
        // if (!$request->user()->admin->can("readAll", new \Acelle\Model\User())) {
        //     $request->merge(array("admin_id" => $request->user()->admin->id));
        // }
        $customers = \Acelle\Model\User::where('customer_admin_id', Auth::user()->id)
            ->orderBy($request->sort_order, $request->sort_direction ? $request->sort_direction : 'asc')
            ->paginate($request->per_page);
        return view('account.users._list', [
            'customers' => $customers,
        ]);
    }

    public function create(Request $request)
    {
        $user = new \Acelle\Model\User();
        // authorize
        // if (Gate::denies('create', $user)) {
        //     return $this->notAuthorized();
        // }
        // validate for plan for tier;
        if ($request->user()->customer->getCurrentActiveSubscription()->plan->isFree()) {
            $request->session()->flash('alert-warning', trans('messages.no_more_item'));
            return redirect()->action('AccountController@users');
        }
        if(!empty(json_decode($request->user()->customer->getCurrentActiveSubscription()->user_plan)->users_max)){
            if(json_decode($request->user()->customer->getCurrentActiveSubscription()->user_plan)->users_max <= $user->where('customer_admin_id', auth()->user()->id)->count()){
                $request->session()->flash('alert-warning', trans('messages.no_more_item'));
                return redirect()->action('AccountController@users');
            } 
        }
        return view('account.users.create', [
            'customer' => $user,
        ]);
    }

    public function storeUser(Request $request)
    {
        $user = new \Acelle\Model\User();
        // authorize
        // if (Gate::denies('create', $user)) {
        //     return $this->notAuthorized();
        // }

        // save posted data
        if ($request->isMethod('post')) {
            $password = str_random(10);
            $request->request->add(['password' => $password, 'password_confirmation' => $password, 'customer_admin_id' => Auth::user()->id]);
            $user->fill($request->all());
            $user->activated = true;

            $this->validate($request, $user->rules());

            // Update password
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->customer_admin_id = Auth::user()->id;
            $user->customer_id = Auth::user()->customer_id;
            $user->save();
            // Upload and save image
            if ($request->hasFile('image')) {
                if ($request->file('image')->isValid()) {
                    // Remove old images
                    $user->uploadProfileImage($request->file('image'));
                }
            }

            // Remove image
            if ($request->_remove_image == 'true') {
                $user->removeProfileImage();
            }

            $layout = \Acelle\Model\Layout::where('alias', 'password_send_by_customer_admin')->first();
            $layout->content = str_replace('{USER_PASSWORD}', $password, $layout->content);
            $layout->content = str_replace('{USER_EMAIL}', $request->email, $layout->content);
            $layout->content = str_replace('{USER_NAME}', $request->name, $layout->content);
    
            $name = is_null($request->name) ? trans('messages.to_email_name') : $request->name;
    
            // build the message
            $message = new ExtendedSwiftMessage();
            $message->setEncoder(new \Swift_Mime_ContentEncoder_PlainContentEncoder('8bit'));
            $message->setContentType('text/html; charset=utf-8');
    
            $message->setSubject($layout->subject);
            $message->setTo([$request->email => $name]);
            $message->setReplyTo(Setting::get('mail.reply_to'));
            $message->addPart($layout->content, 'text/html');
    
            $mailer = App::make('xmailer');
            $result = $mailer->sendWithDefaultFromAddress($message);
    
            if (array_key_exists('error', $result)) {
                throw new \Exception($result['error']);
            }

            $request->session()->flash('alert-success', trans('messages.customer.created'));

            return redirect()->action('AccountController@users');
        }
    }

    public function editUser(Request $request, $id)
    {
        $customer = \Acelle\Model\User::findByUid($id);
        event(new \Acelle\Events\UserUpdated($customer));

        // 
        if (!empty($request->old())) {
            $customer->fill($request->old());
        }

        return view('account.users.edit', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $id)
    {
        $user = \Acelle\Model\User::findByUid($id);

        // Prenvent save from demo mod
        if (config('app.demo')) {
            return view('somethingWentWrong', ['message' => trans('messages.operation_not_allowed_in_demo')]);
        }

        // save posted data
        if ($request->isMethod('patch')) {
            // Prenvent save from demo mod
            if (config('app.demo')) {
                return view('somethingWentWrong', ['message' => trans('messages.operation_not_allowed_in_demo')]);
            }

            $user->fill($request->all());

            $this->validate($request, $user->rules());

            // Update password
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            // Upload and save image
            if ($request->hasFile('image')) {
                if ($request->file('image')->isValid()) {
                    // Remove old images
                    $user->uploadProfileImage($request->file('image'));
                }
            }

            // Remove image
            if ($request->_remove_image == 'true') {
                $user->removeProfileImage();
            }

            $request->session()->flash('alert-success', trans('messages.customer.updated'));
            return redirect()->action('AccountController@users');
        }
    }

    public function deleteUser(Request $request)
    {
        if (isSiteDemo()) {
            return response()->json(["message" => trans('messages.operation_not_allowed_in_demo')], 404);
        }

        $user = \Acelle\Model\User::whereIn(
            'uid',
            is_array($request->uids) ? $request->uids : explode(',', $request->uids)
        );

        $user->delete();
        // Redirect to my lists page
        echo trans('messages.customers.deleted');
    }
    
    public function statusUser($id)
    {
        $user = \Acelle\Model\User::findByUid($id);
        if ($user->activated) {
            $user->activated = 0;
        }else {
            $user->activated = 1;
        }
        $user->save();

        Request()->session()->flash('alert-success', trans('messages.customer.updated'));
        return redirect()->action('AccountController@users');
    }

}
