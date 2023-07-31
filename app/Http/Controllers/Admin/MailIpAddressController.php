<?php

namespace Acelle\Http\Controllers\Admin;

use Acelle\Model\MailIpAddress;
use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;
use Acelle\Model\Customer;
use Acelle\Model\SendingServer;

class MailIpAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ipaddress = MailIpAddress::search($request);

        return view('admin.ipaddress.index', [
            'ipaddress' => $ipaddress,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $ipaddress = new MailIpAddress();
        $ipaddress->status = 'active';

        // authorize
        // if (\Gate::denies('create', $currency)) {
        //     return $this->notAuthorized();
        // }

        if (!empty($request->old())) {
            $ipaddress->fill($request->old());
        }
        
        return view('admin.ipaddress.create', [
            'ipaddress' => $ipaddress,
            'sendingserver' => SendingServer::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $ipaddress = new \Acelle\Model\MailIpAddress();

        // // authorize
        // if (\Gate::denies('create', $ipaddress)) {
        //     return $this->notAuthorized();
        // }

        // save posted data
        if ($request->isMethod('post')) {
            $rules = $ipaddress->rules();

            $this->validate($request, $rules);

            // Save current ipaddress info
            $ipaddress->fill($request->all());
            $ipaddress->status = 'open';

            if ($ipaddress->save()) {
                $request->session()->flash('alert-success', trans('messages.ipaddress.created'));
                return redirect()->action('Admin\MailIpAddressController@index');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Acelle\Model\MailIpAddress  $mailIpAddress
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $ipaddress)
    {
        // Authorization
        // if (!$request->user()->customer->can('changePlan', $subscription)) {
        //     return $this->notAuthorized();
        // }
        return view('admin.ipaddress.show', [
            'items' => MailIpAddress::where('sending_server_id', $ipaddress)->with('sendingservers', 'user')->orderBy('user_id', 'desc')->paginate(10),
            'sending_server' => SendingServer::whereId($ipaddress)->first()->name
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Acelle\Model\MailIpAddress  $mailIpAddress
     * @return \Illuminate\Http\Response
     */

    public function edit(Request $request, $id)
    {
        $ipaddress = MailIpAddress::findByUid($id);

        // // authorize
        // if (\Gate::denies('update', $ipaddress)) {
        //     return $this->notAuthorized();
        // }

        if (!empty($request->old())) {
            $ipaddress->fill($request->old());
        }

        return view('admin.ipaddress.edit', [
            'ipaddress' => $ipaddress,
            'sendingserver' => SendingServer::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Acelle\Model\MailIpAddress  $mailIpAddress
     * @return \Illuminate\Http\Response
     */
    
    public function update(Request $request, $id)
    {
        $ipaddress = MailIpAddress::findByUid($id);

        // Prenvent save from demo mod
        if (config('app.demo')) {
            return view('somethingWentWrong', ['message' => trans('messages.operation_not_allowed_in_demo')]);
        }

        // authorize
        // if (\Gate::denies('update', $currency)) {
        //     return $this->notAuthorized();
        // }

        // save posted data
        if ($request->isMethod('patch')) {
            $rules = $ipaddress->rules();

            $this->validate($request, $rules);

            // Save currency
            $ipaddress->fill($request->all());

            if ($ipaddress->save()) {
                $request->session()->flash('alert-success', trans('messages.ip_address.updated'));
                return redirect()->action('Admin\MailIpAddressController@index');
            }
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \Acelle\Model\MailIpAddress  $mailIpAddress
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $ipaddress)
    {
        MailIpAddress::findByUid($ipaddress)->delete();
        $request->session()->flash('alert-success', trans('messages.ip_address.delete'));
        return back();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        $items = \Acelle\Model\SendingServer::search($request->keyword)
        ->filter($request)
        ->with('ipaddress')
        ->withCount('ipaddress')
        ->orderBy($request->sort_order, $request->sort_direction ? $request->sort_direction : 'asc')
        ->paginate($request->per_page);
        return view('admin.ipaddress._list', [
            'items' => $items,
        ]);
    }

    public function assignIp($user_id){
        $data = MailIpAddress::where('user_id', $user_id)->where('status', 'closed')->get();
        if(count($data) == 0){
            $subscription = Request()->user()->customer->getNewOrActiveSubscription();
            // if ($customer->useOwnSendingServer()) {
                foreach ($subscription->plan->plansSendingServers as $key => $serverInfo) {
                    if($serverInfo->is_primary){
                        $emptyIpAddress = MailIpAddress::where('sending_server_id', $serverInfo->sending_server_id)->whereNull('user_id')->first();
                    }
                }
                if (empty($emptyIpAddress)) {
                    return response("Please add ip addess on sending server");
                }
                $emptyIpAddress->update([
                    'user_id' => $user_id,
                    'status' => 'closed'
                 ]);
                return response("Ip assign successfully");
            // }
        }else{
            return response("you Already have Ip");
        }
    }
}
