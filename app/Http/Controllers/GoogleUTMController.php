<?php

namespace Acelle\Http\Controllers;

use Acelle\GoogleUTM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleUTMController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GoogleUTM $googleUTM)
    {
        if(!empty(GoogleUTM::whereUserId(Auth::user()->id)->first())){
            $googleUTM = GoogleUTM::whereUserId(Auth::user()->id)->first();
        }
        return view('UTM.index', compact('googleUTM'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userid = Auth::user()->id;
        $googleUTM = GoogleUTM::whereUserId($userid)->first();
        $data = $request->validate([
            'utm_campaign' => 'required|string',
            'utm_source' => 'required|string',
            'utm_medium' =>  'required|string',
            'status' =>  'required|string',
        ]);
        $data['user_id'] = $userid;

        if (empty($googleUTM)) {
            GoogleUTM::create($data);
        }else {
            $googleUTM->update($data);
        }
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \Acelle\GoogleUTM  $googleUTM
     * @return \Illuminate\Http\Response
     */
    public function show(GoogleUTM $googleUTM)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Acelle\GoogleUTM  $googleUTM
     * @return \Illuminate\Http\Response
     */
    public function edit(GoogleUTM $googleUTM)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Acelle\GoogleUTM  $googleUTM
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GoogleUTM $googleUTM)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Acelle\GoogleUTM  $googleUTM
     * @return \Illuminate\Http\Response
     */
    public function destroy(GoogleUTM $googleUTM)
    {
        //
    }
}
