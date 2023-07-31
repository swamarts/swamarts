@if ($customers->count() > 0)
    <table class="table table-box pml-table mt-2"
        current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}">
        @foreach ($customers as $key => $item)
            <tr>
                <td width="1%">
                    <img width="50" class="rounded-circle me-2"
                        src="{{ !empty($item) ? $item->getProfileImageUrl() : '' }}" alt="">
                </td>
                <td>
                    <h5 class="m-0 text-bold">
                        <a class="kq_search d-block"
                            href="{{ action('AccountController@editUser', $item->uid) }}">{{ !empty($item) ? $item->displayName() : '' }}</a>
                    </h5>
                    <span class="text-muted kq_search">{{ !empty($item) ? $item->email : '' }}</span><br>
                </td>
                <td>
                    <span class="text-muted2">{{ trans('messages.created_at') }} : {{ $item->created_at }}</span>
                </td>
                <td>
                    <a href="{{ action('AccountController@statusUser', $item->uid) }}">
                        <span class="text-muted2 list-status pull-left">
                            <span class="label label-flat bg-{{ $item->activated == 1 ? 'active' : 'inactive' }}">
                                {{ trans('messages.user_status_' . ($item->activated == 1 ? 'active' : 'inactive')) }}
                            </span>
                        </span>
                    </a>
                </td>
                <td class="text-end">
                    {{-- @can('update', $item->id) --}}
                        <a href="{{ action('AccountController@editUser', $item->uid) }}" data-popup="tooltip"
                            title="{{ trans('messages.edit') }}" role="button" class="btn btn-secondary btn-icon"><span
                                class="material-symbols-rounded">
                                edit
                            </span></a>
                    {{-- @endcan --}}
                    {{-- @if (Auth::user()->can('delete', $item)) --}}
                        <div class="btn-group">
                            <button role="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown"></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item list-action-single" link-method="Delete"
                                        link-confirm="{{ trans('messages.delete_users_confirm') }}"
                                        href="{{ action('AccountController@deleteUser', ['uids' => $item->uid]) }}">
                                        <span class="material-symbols-rounded">
                                            delete_outline
                                        </span> {{ trans('messages.delete') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    {{-- @endcan --}}
            </td>
        </tr>
    @endforeach
</table>
@include('elements/_per_page_select', ['items' => $customers])


<script>
    var assignPlan;
    $(function() {
        $('.assign_plan_button').click(function(e) {
            e.preventDefault();

            var src = $(this).attr('href');
            assignPlan = new Popup({
                url: src
            });

            assignPlan.load();
        });
    });
</script>
@elseif (!empty(request()->keyword))
<div class="empty-list">
    <span class="material-symbols-rounded">
        people_outline
    </span>
    <span class="line-1">
        {{ trans('messages.no_search_result') }}
    </span>
</div>
@else
<div class="empty-list">
    <span class="material-symbols-rounded">
        people_outline
    </span>
    <span class="line-1">
        {{ trans('messages.customer_empty_line_1') }}
    </span>
</div>
@endif
