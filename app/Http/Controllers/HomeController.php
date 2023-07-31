<?php

namespace Acelle\Http\Controllers;

use Acelle\Model\Currency;
use Acelle\Model\Plan;
use Illuminate\Http\Request;
use Acelle\Model\Subscription;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        event(new \Acelle\Events\UserUpdated($request->user()->customer));
        $currentTimezone = $request->user()->customer->getTimezone();

        // Last month
        $customer = $request->user()->customer;
        $sendingCreditsUsed = $customer->getCurrentActiveSubscription()->getCreditsUsedDuringPlanCycle('send'); // all time sending credits used
        $sendingCreditsLimit = $customer->getCurrentActiveSubscription()->getCreditsLimit('send');
        $sendingCreditsUsedPercentage = $customer->getCurrentActiveSubscription()->getCreditsUsedPercentageDuringPlanCycle('send');

        if (config('app.cartpaye')) {
            return view('dashboard.cartpaye');
        } else {
            return view('dashboard', [
                'sendingCreditsUsed' => $sendingCreditsUsed,
                'sendingCreditsUsedPercentage' => $sendingCreditsUsedPercentage,
                'sendingCreditsLimit' => $sendingCreditsLimit,
                'currentTimezone' => $currentTimezone
            ]);
        }
    }

    public function planEmbed()
    {
        return view('plan_embed', [
            'plans' => Plan::getAvailablePlans(),
            'subscription' => '',
            'currency' => Currency::where('status', 'active')->get()
        ]);
    }
}
