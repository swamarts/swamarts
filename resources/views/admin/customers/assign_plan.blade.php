@extends('layouts.popup.full')

@section('title')
    {{ trans('messages.billing_information') }}
@endsection
<style>
    body.mode-dark .new-price-item .price .p-amount {
        color: rgb(176 176 176);
    }

    body.mode-dark .new-price-item .time-box {
        background-color: rgb(242 242 242);
    }

    body.mode-dark .btn-secondary:hover {
        background-color: rgb(79 79 79);
        color: #fff;
    }

    .btn:not(.btn-link):hover {
        box-shadow: 0 .125rem .35rem rgba(0, 0, 0, .1) !important;
    }

    body.mode-dark .btn-secondary {
        background-color: rgb(92 92 92);
        border-color: rgba(255, 255, 255, 0.0);
        color: rgb(255 255 255 / 100%);
    }
</style>
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="sub-section">

                <h2>{{ trans('messages.subscription.select_a_plan') }}</h2>
                <p>{{ trans('messages.subscription.change_plan.select_below') }}</p>

                @if (empty($plans))
                    <div class="row">
                        <div class="col-md-6">
                            @include('elements._notification', [
                                'level' => 'danger',
                                'message' => trans('messages.plan.no_available_plan'),
                            ])
                        </div>
                    </div>
                @else
                    <div class="price-box price-selectable">
                        <div class="price-table">
                            <link rel="stylesheet" type="text/css" href="{{ env('APP_URL') }}/core/style.css">
                            <script>
                                planTier = [];
                                oldcurrencyPrice = [];
                                country_code = 'USD';
                                price_code = '$';
                            </script>
                            @if (empty($plans))
                                <div class="row">
                                    <div class="col-md-6">
                                        @include('elements._notification', [
                                            'level' => 'danger',
                                            'message' => trans('messages.plan.no_available_plan'),
                                        ])
                                    </div>
                                </div>
                            @else
                                <ul class="nav nav-tabs nav-fill justify-content-center" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                            data-bs-target="#home" type="button" role="tab" aria-controls="home"
                                            aria-selected="true">
                                            Yearly Billing
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab"
                                            data-bs-target="#profile" type="button" role="tab" aria-controls="profile"
                                            aria-selected="false">
                                            Monthly Billing
                                        </button>
                                    </li>
                                    {{-- <li class="nav-item" role="presentation">
                                        <select class="nav-link me-3" name="currency" id="currency_convert">
                                            @foreach ($currency as $item)
                                                <option value="{{$item->code}}" data-localisation_factor="{{$item->localisation_factor}}" {{(!empty($subscription->user_plan) && json_decode($subscription->user_plan,true)['currency']) == $item->code ? 'selected' : ''}} data-symbal="{{str_replace('{PRICE}', '', $item->format)}}">{{$item->code}} ({{$item->name}})</option>
                                            @endforeach
                                        </select>
                                    </li> --}}
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home" role="tabpanel"
                                        aria-labelledby="home-tab">
                                        <div class="new-price-box" style="margin-right: -30px">
                                            <div class="d-flex justify-content-center planSec">
                                                @foreach ($plans as $key => $plan)
                                                    @if ($plan->displayFrequencyTime() == 'year')
                                                        <div data-url="{{ action('SubscriptionController@orderBox', ['plan_uid' => $plan->uid]) }}"
                                                            class="new-price-item mb-3 d-inline-block plan-item"
                                                            style="width: calc(33% - 20px)">
                                                            <div class="mt-2">
                                                                <label
                                                                    class="plan-title fs-5 fw-600 mt-0">{{ $plan->name }}</label>
                                                            </div>
                                                            <div style="height:75px">
                                                                <p>{{ $plan->description }}</p>
                                                            </div>
                                                            <div style="height: 250px">
                                                                <div class="price" id="comperePrice{{ $plan->uid }}">
                                                                    @if (!$plan->isFree() && is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                                                        {!! format_price(json_decode($plan->tier)[0]->price / 12, $plan->currency->format, true) !!}<span
                                                                            class="p-currency-code">{{ $plan->currency->code }}</span>
                                                                    @else
                                                                        @if (!$plan->isFree())
                                                                            {!! format_price($plan->price / 12, $plan->currency->format, true) !!}<span
                                                                                class="p-currency-code">{{ $plan->currency->code }}</span>
                                                                        @else
                                                                            <div class=""></div>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                                <div class="d-flex">
                                                                    <h5
                                                                        class="mr-2 mt-1 {{ $plan->isFree() ? 'd-none' : '' }}">
                                                                        Billed yearly as</h5>
                                                                    <div class="h4 {{ $plan->isFree() ? 'price' : '' }}"
                                                                        id="newPrice{{ $plan->uid }}">
                                                                        @if (is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                                                            {!! format_price(json_decode($plan->tier)[0]->price, $plan->currency->format, true) !!}<span
                                                                                class="p-currency-code">{{ $plan->currency->code }}</span>
                                                                        @else
                                                                            {!! format_price($plan->price, $plan->currency->format, true) !!}<span
                                                                                class="p-currency-code">{{ $plan->currency->code }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                @if (is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                                                    <div id="rangeSilder{{ $plan->uid }}">

                                                                    </div>
                                                                @endif
                                                                <script>
                                                                    planTier['{{ $plan->uid }}'] = [{
                                                                        'price': '{{ $plan->price }}'
                                                                    }]
                                                                    oldcurrencyPrice['{{ $plan->uid }}'] = [{
                                                                        'price': '{{ $plan->price }}'
                                                                    }]
                                                                </script>
                                                                @if (!$plan->isFree() && is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                                                    <script>
                                                                        var credits = 0;
                                                                        planTier['{{ $plan->uid }}'] = JSON.parse('{!! $plan->tier !!}');
                                                                        oldcurrencyPrice['{{ $plan->uid }}'] = JSON.parse('{!! $plan->tier !!}');
                                                                        html = `
                                                                    <h4>Subscribers: @if ($plan->displayTotalSubscribers() != 'Unlimited')<span id="newSubscriber{{ $plan->uid }}">${planTier['{{ $plan->uid }}'][credits]['subscriber_max']/1000}K</span> @else {{ $plan->displayTotalSubscribers() }} @endif</h4>
                                                                    <input type="range" class="w-100" style="accent-color: #B91827;" min="0" max="{{ count(json_decode($plan->tier)) - 1 }}" value="${credits}" name="discount_credits" id="discount_credits{{ $plan->uid }}" />
                                                                    <hr><h5> @if ($plan->displayTotalQuota() != 'Unlimited')Up to <span id="newEmails{{ $plan->uid }}">${planTier['{{ $plan->uid }}'][credits]['email_max']/1000}K</span> @else {{ $plan->displayTotalQuota() }} @endif emails/month </h5>
                                                                    <h5> @if ($plan->displayTotalUsers() != 'Unlimited') Up to <span id="newUsers{{ $plan->uid }}">${planTier['{{ $plan->uid }}'][credits]['users_max']}</span> @else {{ $plan->displayTotalUsers() }} @endif Users </h5>
                                                                    `;
                                                                        $("#rangeSilder{{ $plan->uid }}").append(html);
                                                                        $('#discount_credits{{ $plan->uid }}').on("change mousemove", function() {
                                                                            if ("{{ $plan->displayTotalQuota() }}" != 'Unlimited') {
                                                                                $('#newEmails{{ $plan->uid }}').html(convertAmount(planTier['{{ $plan->uid }}'][$(this)
                                                                                .val()]['email_max'] / 1000))
                                                                            }
                                                                            $('#newSubscriber{{ $plan->uid }}').html(convertAmount(planTier['{{ $plan->uid }}'][$(this)
                                                                            .val()]['subscriber_max'] / 1000))
                                                                            $('#newUsers{{ $plan->uid }}').html(planTier['{{ $plan->uid }}'][$(this).val()]['users_max'])
                                                                            $('#newPrice{{ $plan->uid }}').html(
                                                                                `${price_code}<span class="p-amount">${Number(planTier['{{ $plan->uid }}'][$(this).val()]['price'])}</span> <span class="p-currency-code">${country_code}</span>`
                                                                                )
                                                                            $('#comperePrice{{ $plan->uid }}').html(
                                                                                `${price_code}<span class="p-amount">${Number((planTier['{{ $plan->uid }}'][$(this).val()]['price']/12).toFixed(2))}</span> <span class="p-currency-code">${country_code}</span>`
                                                                                )
                                                                        });
                                                                    </script>
                                                                @else
                                                                    <h4 style="margin-top: 5rem!important;">Subscribers:
                                                                        {{ $plan->displayTotalSubscribers() }}</h4>
                                                                    <hr>
                                                                    <h5>Up to {{ $plan->displayTotalQuota() }}
                                                                        emails/{{ $plan->displayFrequencyTime() }} </h5>
                                                                    <h5>Up to {{ $plan->displayTotalUsers() }} Users </h5>
                                                                @endif
                                                            </div>
                                                            <hr class="mb-2 {{ $plan->isFree() ? 'mt-1' : '' }}"
                                                                style="width: 40px">
                                                            <span
                                                                class="time-box d-block text-center small py-2 fw-600 mb-5"
                                                                style="min-height: 9rem">
                                                                <div class="mb-1">
                                                                    <span>Email Campaign</span>
                                                                </div>
                                                                <div class="mb-1">
                                                                    <span>Automations</span>
                                                                </div>
                                                                <div class="mb-1">
                                                                    <span>Templates</span>
                                                                </div>
                                                                <div class="mb-1">
                                                                    <span>Custom DKIM Settings</span>
                                                                </div>
                                                                @if ($plan->hasIpAddress())
                                                                    <div class="mb-1">
                                                                        <span>{{ trans('messages.plan.has_ip_address') }}</span>
                                                                    </div>
                                                                @endif
                                                            </span>
                                                            <div class="text-center">
                                                                @if ($customer->subscription && !$customer->subscription->isEnded() && $customer->subscription->plan->uid == $plan->uid)
                                                                    <a href="javascript:;" class="btn btn-primary mt-30"
                                                                        disabled>
                                                                        {{ trans('messages.plan.current_subscribed') }}
                                                                    </a>
                                                                @else
                                                                    <div class="mt-4 submit-box">
                                                                        <a data-confirm="{{ trans('messages.customer.assign_plan.confirm', [
                                                                            'plan' => $plan->name,
                                                                            'customer' => $customer->uid,
                                                                        ]) }}"
                                                                            href="{{ action('Admin\CustomerController@assignPlan', [
                                                                                'uid' => $customer->uid,
                                                                                'plan_uid' => $plan->uid,
                                                                                'currency' => 'USD',
                                                                                'type' => 'year',
                                                                            ]) }}"
                                                                            class="btn btn-primary btn-mc_mk mt-2 change-plan-button">
                                                                            {{ trans('messages.subscription.get_started') }}
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                        <div class="new-price-box" style="margin-right: -30px">
                                            <div class="d-flex justify-content-center planSec">
                                                @foreach ($plans as $key => $plan)
                                                    @if ($plan->displayFrequencyTime() == 'month')
                                                        <div data-url="{{ action('SubscriptionController@orderBox', ['plan_uid' => $plan->uid]) }}"
                                                            class="new-price-item mb-3 d-inline-block plan-item"
                                                            style="width: calc(33% - 20px)">
                                                            <div class="mt-2">
                                                                <label
                                                                    class="plan-title fs-5 fw-600 mt-0">{{ $plan->name }}</label>
                                                            </div>
                                                            <div style="height:70px">
                                                                <p>{{ $plan->description }}</p>
                                                            </div>
                                                            <div style="height: 250px">
                                                                <div class="price" id="newPrice{{ $plan->uid }}">
                                                                    @if (is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                                                        {!! format_price(json_decode($plan->tier)[0]->price, $plan->currency->format, true) !!}
                                                                        <span
                                                                            class="p-currency-code">{{ $plan->currency->code }}</span>
                                                                    @else
                                                                        {!! format_price($plan->price, $plan->currency->format, true) !!}
                                                                        <span
                                                                            class="p-currency-code">{{ $plan->currency->code }}</span>
                                                                    @endif
                                                                </div>
                                                                <div id="rangeSildermonth{{ $plan->uid }}">

                                                                </div>
                                                                <script>
                                                                    planTier['{{ $plan->uid }}'] = [{
                                                                        'price': '{{ $plan->price }}'
                                                                    }]
                                                                    oldcurrencyPrice['{{ $plan->uid }}'] = [{
                                                                        'price': '{{ $plan->price }}'
                                                                    }]
                                                                </script>
                                                                @if (is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                                                    <script>
                                                                        var credits = 0;
                                                                        planTier['{{ $plan->uid }}'] = JSON.parse('{!! $plan->tier !!}');
                                                                        oldcurrencyPrice['{{ $plan->uid }}'] = JSON.parse('{!! $plan->tier !!}');
                                                                        html = `
                                                                    <h4>Subscribers: @if ($plan->displayTotalSubscribers() != 'Unlimited')<span id="newSubscriber{{ $plan->uid }}">${planTier['{{ $plan->uid }}'][credits]['subscriber_max']/1000}K</span>@else {{ $plan->displayTotalSubscribers() }} @endif</h4>
                                                                    <input type="range" class="w-100" style="accent-color: #B91827;" min="0" max="{{ count(json_decode($plan->tier)) - 1 }}" value="${credits}" name="discount_credits_month" id="discount_credits_month{{ $plan->uid }}" />
                                                                    <hr><h5> @if ($plan->displayTotalQuota() != 'Unlimited')Up to <span id="newEmails{{ $plan->uid }}">${planTier['{{ $plan->uid }}'][credits]['email_max']/1000}K</span> @else {{ $plan->displayTotalQuota() }} @endif emails/{{ $plan->displayFrequencyTime() }} </h5>
                                                                    <h5> @if ($plan->displayTotalUsers() != 'Unlimited') Up to <span id="newUsers{{ $plan->uid }}">${planTier['{{ $plan->uid }}'][credits]['users_max']}</span> @else {{ $plan->displayTotalUsers() }} @endif Users </h5>
                                                                    `;
                                                                        $("#rangeSildermonth{{ $plan->uid }}").append(html);
                                                                        $('#discount_credits_month{{ $plan->uid }}').on("change mousemove", function() {
                                                                            if ("{{ $plan->displayTotalQuota() }}" != 'Unlimited') {
                                                                                $('#newEmails{{ $plan->uid }}').html(convertAmount(planTier['{{ $plan->uid }}'][$(this)
                                                                                .val()]['email_max'] / 1000))
                                                                            }
                                                                            $('#newSubscriber{{ $plan->uid }}').html(convertAmount(planTier['{{ $plan->uid }}'][$(this)
                                                                            .val()]['subscriber_max'] / 1000))
                                                                            $('#newUsers{{ $plan->uid }}').html(planTier['{{ $plan->uid }}'][$(this).val()]['users_max'])
                                                                            $('#newPrice{{ $plan->uid }}').html(
                                                                                `${price_code}<span class="p-amount">${Number(planTier['{{ $plan->uid }}'][$(this).val()]['price'])}</span> <span class="p-currency-code">${country_code}</span>`
                                                                                )
                                                                        });
                                                                    </script>
                                                                @else
                                                                    <h4 class="mb-5">Subscribers:
                                                                        {{ $plan->displayTotalSubscribers() }}</h4>
                                                                    <hr>
                                                                    <h5>Up to {{ $plan->displayTotalQuota() }}
                                                                        emails/{{ $plan->displayFrequencyTime() }} </h5>
                                                                    <h5>Up to {{ $plan->displayTotalUsers() }} Users </h5>
                                                                @endif
                                                            </div>
                                                            <hr class="mb-2" style="width: 40px">
                                                            <span
                                                                class="time-box d-block text-center small py-2 fw-600 mb-5"
                                                                style="min-height: 9rem">
                                                                <div class="mb-1">
                                                                    <span>Email Campaign</span>
                                                                </div>
                                                                <div class="mb-1">
                                                                    <span>Automations</span>
                                                                </div>
                                                                <div class="mb-1">
                                                                    <span>Templates</span>
                                                                </div>
                                                                <div class="mb-1">
                                                                    <span>Custom DKIM Settings</span>
                                                                </div>
                                                                @if ($plan->hasIpAddress())
                                                                    <div class="mb-1">
                                                                        <span>{{ trans('messages.plan.has_ip_address') }}</span>
                                                                    </div>
                                                                @endif
                                                            </span>
                                                            <div class="text-center">
                                                                @if ($customer->subscription && !$customer->subscription->isEnded() && $customer->subscription->plan->uid == $plan->uid)
                                                                    <a href="javascript:;" class="btn btn-primary mt-30"
                                                                        disabled>
                                                                        {{ trans('messages.plan.current_subscribed') }}
                                                                    </a>
                                                                @else
                                                                    <div class="mt-4 submit-box">
                                                                        <a data-confirm="{{ trans('messages.customer.assign_plan.confirm', [
                                                                            'plan' => $plan->name,
                                                                            'customer' => $customer->uid,
                                                                        ]) }}"
                                                                            href="{{ action('Admin\CustomerController@assignPlan', [
                                                                                'uid' => $customer->uid,
                                                                                'plan_uid' => $plan->uid,
                                                                                'currency' => 'USD',
                                                                                'type' => 'month',
                                                                            ]) }}"
                                                                            class="btn btn-primary btn-mc_mk mt-2 change-plan-button">
                                                                            {{ trans('messages.subscription.get_started') }}
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                @endif

            </div>
        </div>
        <div class="col-md-1"></div>
    </div>

    <script>
        function convertAmount(val) {
            if (val.toString().length <= 3) {
                return val + 'K';
            } else if (val.toString().length == 1) {
                return val + 'K';
            } else {
                return parseInt(val / 1000) + 'M';
            }
        }

        $(function() {
            $('.change-plan-button').click(function(e) {
                e.preventDefault();
                const params = new URL($(this).attr('href')).searchParams;
                const planid = params.get('plan_uid');
                const type = params.get('type');
                var confirm = $(this).attr('data-confirm');
                var url = $(this).attr('href') + '&tier=' + $('#discount_credits' + (type == 'month' ?
                    '_month' + planid : planid)).val()
                var button = $(this);

                var dialog = new Dialog('confirm', {
                    message: confirm,
                    ok: function(dialog) {
                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: {
                                _token: CSRF_TOKEN,
                                gateway: button.closest('.submit-box').find(
                                    '[name=gateway]').val(),
                            },
                            statusCode: {
                                // validate error
                                400: function(res) {
                                    alert('Something went wrong!');
                                }
                            },
                            success: function(response) {
                                // notify
                                notify({
                                    type: 'success',
                                    title: '{!! trans('messages.notify.success') !!}',
                                    message: response.message
                                });

                                CustomersIndex.getList().load();

                                // hide modal
                                assignPlan.hide();
                            }
                        });
                    },
                });
            });
        });
    </script>
@endsection
