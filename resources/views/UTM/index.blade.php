@extends('layouts.core.frontend', [
    'menu' => 'users',
])

@section('title', trans('messages.users'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li class="breadcrumb-item"><a href="{{ action('HomeController@index') }}">{{ trans('messages.home') }}</a></li>
            <li class="breadcrumb-item active">{{ trans('messages.utm') }}</li>
        </ul>
    </div>

@endsection

@section('content')
@include('account._menu', [
    'menu' => 'UTM',
])
    <form enctype="multipart/form-data" action="{{ action('GoogleUTMController@store') }}" method="POST"
        class="form-validate-jqueryz">
        {{ csrf_field() }}
        <div class="row justify-content-center">						
            <div class="col-md-6">
                <div class="">							
                    @include('helpers.form_control', ['type' => 'text', 'name' => 'utm_campaign', 'label' => trans('messages.utm_campaign'),'value' => $googleUTM->utm_campaign ?? old('utm_campaign'), 'help_class' => 'googleUTM', 'rules' => $googleUTM->rules()])
                </div>
            </div>
            <div class="col-md-6">
                <div class="">
                    @include('helpers.form_control', ['type' => 'text', 'name' => 'utm_source','label' => trans('messages.utm_source'), 'value' => $googleUTM->utm_source ?? old('utm_source'), 'help_class' => 'googleUTM', 'rules' => $googleUTM->rules()])
                </div>
            </div>
            <div class="col-md-6">
                <div class="">
                    @include('helpers.form_control', [
                        'type' => 'text',
                        'name' => 'utm_medium',
                        'label' => trans('messages.utm_medium'),
                        'value' => $googleUTM->utm_medium ?? old('utm_medium'),
                        'help_class' => 'googleUTM',
                        'rules' => $googleUTM->rules()
                    ])
                </div>
            </div>
            <div class="col-md-6 form-group">
                <label for="">Status</label>
                <select class="select form-control" name="status">
                    <option {{$googleUTM->status == 1 ? 'active' : ''}} value="1">{{ trans('messages.active') }}</option>
                    <option {{$googleUTM->status == 0 ? 'active' : ''}} value="0">{{ trans('messages.inactive') }}</option>
                </select>
            </div>
        </div>
        <hr />
        <div class="text-end">
            <button class="btn btn-secondary"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
        </div>
	<form>
@endsection
