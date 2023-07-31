@extends('layouts.core.backend', [
	'menu' => 'ipaddress',
])

@section('title', trans('messages.ip_title'))

@section('page_header')
	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li class="breadcrumb-item"><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ action("Admin\MailIpAddressController@index") }}">{{ trans('messages.ip_address') }}</a></li>
			<li class="breadcrumb-item"><a href="#">{{ trans('messages.rss.show_msg') }}</a></li>
		</ul>
		<h1>
			<span class="text-semibold"><span class="material-symbols-rounded">format_list_bulleted</span> {{ trans('messages.ip_address') }} of {{$sending_server}}</span>
		</h1>
	</div>
@endsection

@section('content')
    <div class="">
        <div class="row d-flex justify-content-center">
            <div class="col-md-10">
                <div class="sub-section">
			{{-- @can('create', new Acelle\Model\Currency()) --}}
				<div class="text-end">
					<a href="{{ action("Admin\MailIpAddressController@create") }}" role="button" class="btn btn-secondary">
						<span class="material-symbols-rounded">add</span> {{ trans('messages.source.add_new')}} {{ trans('messages.ip_address') }}
					</a>
				</div>
			{{-- @endcan --}}
                @if (count($items) > 0)
                    <table class="table table-box pml-table mt-2" current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}">
                        <tr>
                            <th>#</th>
                            <th>{{trans('messages.name')}}</th>
                            <th>{{trans('messages.ip_address')}}</th>
                            <th>{{trans('messages.monthly')}} Price</th>
                            <th>{{trans('messages.yearly')}} Price</th>
                            <th>{{trans('messages.yearly')}} Status</th>
                            <th>{{trans('messages.action')}}</th>
                        </tr>
                        @foreach ($items as $key => $item)
                            <tr>
                                <td width="1%">
                                    <div class="text-nowrap">
                                        {{-- <div class="checkbox inline me-1">
                                            <label>
                                                <input type="checkbox" class="node styled"
                                                    name="uids[]"
                                                    value="{{ $item->uid }}"
                                                />
                                            </label>
                                        </div> --}}
                                        {{$key+1}}
                                    </div>
                                </td>
                                <td class="ml-3">
                                {{empty($item->user) ? trans('messages.ip_not_assign') : $item->user->displayName()}}
                                </td>
                                <td class="ml-3">
                                    {{$item->ip_address}}
                                    </td>
                                <td class="ml-3">
                                    {{$item->price_monthly}}
                                </td>
                                <td class="ml-3">
                                    {{$item->price_yearly}}
                                </td>
                                <td class="ml-3">
                                    <span class="text-capitalize">{{$item->status}}</span>
                                </td>
                                <td width="20%">
                                    <a class="btn btn-secondary btn-icon" link-method="DELETE" link-confirm="{{ trans('messages.ip_address.delete.confirm', ['name' => $item->ip_address]) }}" href="{{ action('Admin\MailIpAddressController@destroy',$item->uid) }}" title="{{ trans('messages.delete') }}" class="">
                                        <span class="material-symbols-rounded">delete</span> {{ trans('messages.delete') }}
                                    </a>
                                    {{-- <a class="dropdown-item" link-confirm="{{ trans('messages.delete_email_verification_servers_confirm') }}" href="{{ action('Admin\EmailVerificationServerController@delete') }}"><span class="material-symbols-rounded">
                                        delete_outline
                                        </span> {{ trans('messages.delete') }}</a> --}}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    @include('elements/_per_page_select')
                @else
                    <div class="empty-list">
                        <span class="material-symbols-rounded">dns</span>
                        <span class="line-1">
                            {{ trans('messages.ip_empty') }}
                        </span>
                    </div>
                @endif

                </div>
            </div>
        </div>
    </div>

    <script>

    </script>
@endsection