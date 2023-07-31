@extends('layouts.core.backend', [
	'menu' => 'ipaddress',
])

@section('title', $ipaddress->ip_address)

@section('page_header')
	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li class="breadcrumb-item"><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ action("Admin\MailIpAddressController@index") }}">{{ trans('messages.ip_address') }}</a></li>
			<li class="breadcrumb-item active">{{ trans('messages.update') }}</li>
		</ul>
		<h1>
			<span class="text-semibold"><span class="material-symbols-rounded">person_outline</span> {{ $ipaddress->ip_address }}</span>
		</h1>
	</div>
@endsection

@section('content')
	<form enctype="multipart/form-data" action="{{ action('Admin\MailIpAddressController@update', $ipaddress->uid) }}" method="POST" class="form-validate-jqueryx">
		{{ csrf_field() }}
		<input type="hidden" name="_method" value="PATCH">
		@include('admin.ipaddress._form')
	<form>
@endsection