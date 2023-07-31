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
            <li class="breadcrumb-item"><a href="{{ action('AccountController@users') }}">{{ trans('messages.user') }}</a></li>
            <li class="breadcrumb-item active">{{ trans('messages.create') }}</li>
        </ul>
    </div>

@endsection

@section('content')
    <form enctype="multipart/form-data" action="{{ action('AccountController@storeUser') }}" method="POST"
        class="form-validate-jqueryz">
        {{ csrf_field() }}
        @include('account.users._form')
	<form>
@endsection
