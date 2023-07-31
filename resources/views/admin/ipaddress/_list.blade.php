@if ($items->count() > 0)
    <table class="table table-box pml-table mt-2" current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}">
        @foreach ($items as $key => $item)
            <tr>
                {{-- <td width="1%">
                    <div class="text-nowrap">
                        <div class="checkbox inline me-1">
                            <label>
                                <input type="checkbox" class="node styled"
                                    name="uids[]"
                                    value="{{ $item->uid }}"
                                />
                            </label>
                        </div>
                    </div>
                </td> --}}
                <td width="1%">
                    @php
                        $server = $item->mapType();
                    @endphp

                    @if ($server->isExtended())
                        <span class="mc-server-avatar shadow-sm rounded server-avatar" style="background: url({{ $server->getIconUrl() }}) top left/36px 36px no-repeat transparent;">
                            <span class="material-symbols-rounded"></span>
                        </span>
                    @else
                        <span class="server-avatar shadow-sm rounded server-avatar-{{ $item->type }} mr-0">
                            <span class="material-symbols-rounded"></span>
                        </span>
                    @endif
                </td>
                <td>
                    @if ($server->isExtended())
                        <h5 class="m-0 text-bold">
                            <a class="kq_search d-block" href="{{ $server->getEditUrl() }}">{{ $item->name }}</a>
                        </h5>
                    @else
                        <h5 class="m-0 text-bold">
                            <a class="kq_search d-block" href="{{ action('Admin\MailIpAddressController@show', $item->id) }}">{{ $item->name }}</a>
                        </h5>
                    @endif
                    <span class="text-muted">{{ trans('messages.created_at') }}: {{ Auth::user()->admin->formatDateTime($item->created_at, 'datetime_full') }}</span>
                </td>
                {{-- <td>
                    <div class="single-stat-box pull-left ml-20">
                        @if ($server->isExtended())
                            <span class="no-margin stat-num kq_search">{{ $server->getTypeName() }}</span>
                        @else
                            <span class="no-margin stat-num kq_search">{{ trans('messages.' . $item->type) }}</span>
                        @endif
                        <br />
                        <span class="text-muted">{{ trans('messages.type') }}</span>
                        
                    </div>
                </td> --}}
                {{-- <td>
                    @if (!empty($item->host))
                        <div class="single-stat-box pull-left ml-20">
                            <span title="{{ $item->host }}" class="no-margin stat-num kq_search domain-truncate">{{ $item->host }}</span>
                            <br />
                            <span class="text-muted">{{ trans('messages.host') }}</span>
                        </div>
                    @elseif (!empty($item->aws_region))
                        <div class="single-stat-box pull-left ml-20">
                            <span title="{{ $item->aws_region }}" class="no-margin stat-num kq_search domain-truncate">{{ $item->aws_region }}</span>
                            <br />
                            <span class="text-muted">{{ trans('messages.aws_region') }}</span>
                        </div>
                    @elseif (!empty($item->domain))
                        <div class="single-stat-box pull-left ml-20">
                            <span title="{{ $item->domain }}" class="no-margin stat-num kq_search domain-truncate">{{ $item->domain }}</span>
                            <br />
                            <span class="text-muted">{{ trans('messages.domain') }}</span>
                        </div>
                    @endif
                </td> --}}
                <td>
                    <div class="single-stat-box pull-left ml-20">
                        <span class="text-muted"><strong>{{$item->ipaddress_count}} </strong>{{trans('messages.ip_address')}}</span>
                    </div>
                </td>
                <td>
                    <span class="text-muted2 list-status pull-left">
                        <span class="label label-flat bg-{{ $item->status }}">{{ trans('messages.sending_server_status_' . $item->status) }}</span>
                    </span>
                </td>
            </tr>
        @endforeach
    </table>
    @include('elements/_per_page_select')
    
@elseif (!empty(request()->keyword) || !empty(request()->filters["type"]))
    <div class="empty-list">
        <span class="material-symbols-rounded">dns</span>
        <span class="line-1">
            {{ trans('messages.no_search_result') }}
        </span>
    </div>
@else
    <div class="empty-list">
        <span class="material-symbols-rounded">dns</span>
        <span class="line-1">
            {{ trans('messages.sending_server_empty_line_1') }}
        </span>
    </div>
@endif


<script>
    var ipaddressPop;

    $(function() {
        $('.show_ipaddress_button').click(function(e) {
            e.preventDefault();

            var src = $(this).attr('href');

            ipaddressPop = new Popup();
            ipaddressPop.load(src);
        });
    });
</script>

