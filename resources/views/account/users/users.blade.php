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
            <li class="breadcrumb-item active">{{ trans('messages.users') }}</li>
        </ul>
    </div>

@endsection

@section('content')
    @include('account._menu', [
        'menu' => 'users',
    ])
    {{-- {{ action('AccountController@sort') }} --}}
    <div class="listing-form" sort-url=""
        data-url="{{ action('AccountController@listing') }}" per-page="{{ Acelle\Model\Customer::$itemsPerPage }}">
        <div class="d-flex top-list-controls top-sticky-content">
            <div class="me-auto">
                @if ($customers->count() >= 0)
                    <div class="filter-box">
                        <span class="filter-group">
                            <span class="title text-semibold text-muted">{{ trans('messages.sort_by') }}</span>
                            <select class="select" name="sort_order">
                                <option value="users.created_at">{{ trans('messages.created_at') }}</option>
                                <option value="users.updated_at">{{ trans('messages.updated_at') }}</option>
                            </select>
                            <input type="hidden" name="sort_direction" value="desc" />
                            <button type="button" class="btn btn-xs sort-direction" data-popup="tooltip"
                                title="{{ trans('messages.change_sort_direction') }}" role="button" class="btn btn-xs">
                                <span class="material-symbols-rounded desc">
                                    sort
                                </span>
                            </button>
                        </span>
                        <span class="text-nowrap">
                            <input type="text" name="keyword" class="form-control search"
                                value="{{ request()->keyword }}" placeholder="{{ trans('messages.type_to_search') }}" />
                            <span class="material-symbols-rounded">
                                search
                            </span>
                        </span>
                    </div>
                @endif
            </div>  
            
            {{--@can('create', new Acelle\Model\User())--}}
                <div class="text-end">
                    <a href="{{ action('AccountController@create') }}" role="button" class="btn btn-secondary">
                        <span class="material-symbols-rounded">
                            add
                        </span> {{ trans('messages.add_users') }}
                    </a>
                </div>
            {{--@endcan--}}
        </div>

        <div class="pml-table-container">



        </div>
    </div>

    <script>
        var assignPlanModal = new IframeModal();
    </script>

    <script>
        var CustomersIndex = {
            getList: function() {
                return makeList({
                    url: '{{ action('AccountController@listing') }}',
                    container: $('.listing-form'),
                    content: $('.pml-table-container')
                });
            }
        };

        $(document).ready(function() {
            CustomersIndex.getList().load();
        });
    </script>

@endsection
