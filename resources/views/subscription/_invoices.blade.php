<div class="sub-section">
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <h2 class="text-semibold">{{ trans('messages.invoices_logs') }}</h2>
            <p>{{ trans('messages.subscription.logs.intro') }}</p>

            <ul class="nav nav-tabs nav-underline mb-1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="javascript:;" data-bs-toggle="tab" data-bs-target="#nav-invoices">
                        {{ trans('messages.invoices') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#nav-transactions" data-bs-toggle="tab">
                        {{ trans('messages.transactions') }}
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link" href="#logs" data-bs-toggle="tab">{{ trans('messages.subscription.logs') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="#ipaddress" data-bs-toggle="tab">{{ trans('messages.ip_address') }}</a></li>
            </ul>

            <div class="tab-content">
                <div id="logs" class="tab-pane fade">
                    <table class="table table-box pml-table table-log mt-10">
                        <tr>
                            <th width="200px">{{ trans('messages.subscription.log.created_at') }}</th>
                            <th>{{ trans('messages.subscription.log.message') }}</th>
                        </tr>
                        @forelse ($subscription->getLogs() as $key => $log)
                            <tr>
                                <td>
                                    <span class="no-margin kq_search">
                                        {{ Auth::user()->customer->formatDateTime($log->created_at, 'datetime_full') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="no-margin kq_search">
                                        {{-- {!! trans('messages.subscription.log.' . $log->type, $log->getData()) !!} old code --}}
                                        {!! $log->renderLog() !!}
                                    </span>
                                </td>                                
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="5">
                                    {{ trans('messages.subscription.logs.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </table>
                </div>
                <div id="nav-transactions" class="tab-pane fade">
                    <table class="table table-box pml-table table-log mt-10">
                        <tr>
                            <th width="200px">{{ trans('messages.created_at') }}</th>
                            <th>{{ trans('messages.message') }}</th>
                            <th>{{ trans('messages.transaction.amount') }}</th>
                            <th>{{ trans('messages.transaction.method') }}</th>
                            <th>{{ trans('messages.status') }}</th>
                        </tr>
                        @forelse ($subscription->transactions()->get() as $key => $transaction)
                            <tr>
                                <td>
                                    <span class="no-margin kq_search">
                                        {{ Auth::user()->customer->formatDateTime($transaction->created_at, 'datetime_full') }}
                                    </span>
                                </td> 
                                <td>
                                    <span class="no-margin kq_search">
                                        {!! trans('messages.transaction_for_invoice', [
                                            'uid' => $transaction->invoice->uid
                                        ]) !!}
                                    </span>
                                </td> 
                                <td>
                                    <span class="no-margin kq_search">
                                    {!! $transaction->invoice->formattedTotal() !!}
                                    </span>
                                </td> 
                                <td>
                                    <span class="no-margin kq_search" style="text-transform: capitalize;">
                                        {{ $transaction->method }}
                                        {{-- @if (Lang::has('messages.transaction.method.' . $transaction->method))
                                            {{ trans('messages.transaction.method.' . $transaction->method) }}
                                        @else
                                            {{ $transaction->method }}
                                        @endif old code--}}
                                    </span>
                                </td> 
                                <td>
                                    <span class="no-margin kq_search">
                                        <span {!! $transaction->error ? 'title="'.strip_tags($transaction->error).'"' : '' !!} class="xtooltip label label-{{ $transaction->status }}" style="white-space: nowrap;">
                                            {{ trans('messages.transaction.' . $transaction->status) }}
                                        </span>
                                    </span>
                                </td>                                
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="5">
                                    {{ trans('messages.subscription.logs.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </table>
                </div>
                <div id="nav-invoices" class="tab-pane fade in show active">
                    <table class="table table-box pml-table table-log mt-10">
                        <tr>
                            <th width="130px">{{ trans('messages.invoice.created_at') }}</th>
                            <th>{{ trans('messages.invoice.title') }}</th>
                            <th>{{ trans('messages.invoice.amount') }}</th>
                            <th>{{ trans('messages.invoice.status') }}</th>
                            <th>{{ trans('messages.invoice.action') }}</th>
                        </tr>
                        @forelse ($subscription->invoices()->orderBy('created_at', 'desc')->get() as $key => $invoice)
                            @php
                                $billInfo = $invoice->getBillingInfo();
                            @endphp
                            <tr>
                                <td>
                                    <span class="no-margin kq_search">
                                        {{ Auth::user()->customer->formatDateTime($invoice->created_at, 'datetime_full') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="no-margin kq_search font-weight-semibold">
                                        {!! $billInfo['title'] !!}
                                    </span>
                                    <div class="text-muted">
                                        {!! $billInfo['description'] !!}
                                    </div>
                                </td>
                                <td>
                                    <span class="no-margin kq_search">
                                        {{ $billInfo['total'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="no-margin kq_search">
                                        <span class="label bg-{{ $invoice->status }}" style="white-space: nowrap;">
                                            {{ trans('messages.invoice.status.' . $invoice->status) }}
                                        </span>
                                    </span>
                                </td>
                                <td>
                                    @if ($invoice->isPaid())
                                        <a class="btn btn-secondary text-nowrap" target="_blank" href="{{ action('InvoiceController@download', [
                                            'uid' => $invoice->uid,
                                        ]) }}">
                                            <i class="material-symbols-rounded me-1">download</i>{{ trans('messages.download') }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="5">
                                    {{ trans('messages.subscription.logs.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </table>
                </div>
                <div id="ipaddress" class="tab-pane fade">
                    <table class="table table-box pml-table table-log mt-10">
                        <tr>
                            <th width="200px">{{ trans('messages.subscription.log.created_at') }}</th>
                            <th>{{ trans('messages.subscription.log.message') }}</th>
                            <th>{{ trans('messages.invoice.amount') }}</th>
                            <th>{{ trans('messages.invoice.status') }}</th>
                        </tr>
                        @forelse ($ipaddresslog as $key => $invoice)
                            <tr>
                                <td>
                                    <span class="no-margin kq_search">
                                        {{ Auth::user()->customer->formatDateTime($invoice->created_at, 'datetime_full') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="no-margin kq_search font-weight-semibold">
                                        {!! $invoice->title !!}
                                    </span>
                                    <div class="text-muted">
                                        {!! $invoice->description !!}
                                    </div>
                                </td> 
                                <td>
                                    <span class="no-margin kq_search">
                                        @foreach ($invoice->invoiceItems as $item)
                                        {{Acelle\Library\Tool::format_price($item->amount, $currency['format']['format'])}}
                                        @endforeach
                                    </span>
                                </td>
                                <td>
                                    <span class="no-margin kq_search">
                                        <span class="label bg-{{ $invoice->status }}" style="white-space: nowrap;">
                                            {{ trans('messages.invoice.status.' . $invoice->status) }}
                                        </span>
                                    </span>
                                </td>                               
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="5">
                                    {{ trans('messages.subscription.logs.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>


            
        </div>
    </div>
</div>