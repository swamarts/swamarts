<link rel="stylesheet" type="text/css" href="{{ env('APP_URL') }}/core/style.css">
<meta name="viewport" content="width=device-width, initial-scale=1">

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
   <div class="free-plan-tag" style="left:45px">
    <img src="{{asset('images/icons/SVG/free-plan.svg')}}" class="free-months-notice p-abs" alt="Two months free!">
    <img src="{{asset('images/icons/SVG/arrow-plan.svg')}}" class="curved-arrow p-abs d-none d-lg-block" alt="Curved arrow">
    <img src="{{asset('images/icons/SVG/plan-uparrow.svg')}}" class="straight-arrow p-abs" alt="Straight arrow">
   </div>
    <ul class="nav nav-tabs nav-fill justify-content-center" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                type="button" role="tab" aria-controls="home" aria-selected="true">
                Yearly Billing
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                type="button" role="tab" aria-controls="profile" aria-selected="false">
                Monthly Billing 
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <select class="nav-link me-3" name="currency" id="currency_convert">
                @foreach ($currency as $item)
                    <option value="{{$item->code}}" data-icon="{{$item->format}}" data-localisation_factor="{{$item->localisation_factor}}" {{(!empty($subscription->user_plan) && json_decode($subscription->user_plan,true)['currency']) == $item->code ? 'selected' : ''}} data-symbal="{{$item->usd_value}}">{{$item->code}} ({{$item->name}})</option>
                @endforeach
            </select>
        </li>
    </ul>
    {{-- <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="new-price-box" style="margin-right: -30px">
                <div class="d-flex justify-content-center planSec">
                    @foreach ($plans as $key => $plan)
                        @if ($plan->displayFrequencyTime() == "year")
                        <div data-url="{{ action('SubscriptionController@orderBox', ['plan_uid' => $plan->uid]) }}"
                            class="new-price-item mb-3 d-inline-block plan-item {{ $subscription && $subscription->plan->id == $plan->id ? 'current' : '' }}"
                            style="width: calc(33% - 20px)">
                            <div class="mt-2">
                                <label class="plan-title fs-5 fw-600 mt-0">{{ $plan->name }}</label>
                            </div>
                            <div style="height:100px">
                                <p>{{ $plan->description }}</p>
                            </div>
                            <div style="height: 250px">
                                
                                <div class="price" id="comperePrice{{$plan->uid}}">
                                    @if (is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                        {!! format_price((json_decode($plan->tier)[0]->price/12), $plan->currency->format, true) !!}
                                        <span class="p-currency-code">{{ $plan->currency->code }}</span>
                                    @else
                                        @if ($plan->price != 0)
                                            {!! format_price(($plan->price/12), $plan->currency->format, true) !!}
                                            <span class="p-currency-code">{{ $plan->currency->code }}</span>
                                        @else
                                            <div class="mt-4 pt-3"></div>
                                        @endif

                                    @endif
                                </div>
                                <div class="d-flex">
                                    <h5 class="mr-2 mt-1 {{$plan->isFree() ? 'd-none' : ''}}">Billed yearly as</h5>
                                    <div class="h4" id="newPrice{{$plan->uid}}">
                                        @if (is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                        {!! format_price(json_decode($plan->tier)[0]->price, $plan->currency->format, true) !!}
                                        <span class="p-currency-code">{{ $plan->currency->code }}</span>
                                        @else
                                            {!! format_price($plan->price, $plan->currency->format, true) !!}
                                            <span class="p-currency-code">{{ $plan->currency->code }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div id="rangeSilder{{$plan->uid}}">

                                </div>
                                <script>
                                    planTier['{{$plan->uid}}'] = [{'price' :'{{$plan->price}}' }]
                                    oldcurrencyPrice['{{$plan->uid}}'] = [{'price' :'{{$plan->price}}' }]
                                </script>
                                @if (is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                <script>
                                    var credits = 0;
                                    planTier['{{$plan->uid}}'] = JSON.parse('{!!$plan->tier!!}');
                                    oldcurrencyPrice['{{$plan->uid}}'] = JSON.parse('{!!$plan->tier!!}');
                                    html = `
                                            <h4>Subscribers : @if($plan->displayTotalSubscribers() != 'Unlimited')<span id="newSubscriber{{$plan->uid}}">${planTier['{{$plan->uid}}'][credits]['subscriber_max']/1000}k</span> @else {{$plan->displayTotalSubscribers()}} @endif</h4>
                                            <input type="range" class="w-100" style="accent-color: #B91827;" min="0" max="{{count(json_decode($plan->tier))-1}}" value="${credits}" name="discount_credits" id="discount_credits{{$plan->uid}}" />
                                            <hr><h5> @if($plan->displayTotalQuota() != 'Unlimited')Up to <span id="newEmails{{$plan->uid}}">${planTier['{{$plan->uid}}'][credits]['email_max']/1000}k</span> @else {{$plan->displayTotalQuota()}} @endif emails/month </h5>
                                            <h5> @if($plan->displayTotalUsers() != 'Unlimited') Up to <span id="newUsers{{$plan->uid}}">${planTier['{{$plan->uid}}'][credits]['users_max']}</span> @else {{$plan->displayTotalUsers()}} @endif Users </h5>
                                            `;
                                    $("#rangeSilder{{$plan->uid}}").append(html);
                                    $('#discount_credits{{$plan->uid}}').on("change mousemove", function() {
                                        if ("{{$plan->displayTotalQuota()}}" != 'Unlimited') {
                                            $('#newEmails{{$plan->uid}}').html(convertAmount(planTier['{{$plan->uid}}'][$(this).val()]['email_max']/1000))
                                        }
                                        $('#newSubscriber{{$plan->uid}}').html(convertAmount(planTier['{{$plan->uid}}'][$(this).val()]['subscriber_max']/1000))
                                        $('#newUsers{{$plan->uid}}').html(planTier['{{$plan->uid}}'][$(this).val()]['users_max'])
                                        $('#newPrice{{$plan->uid}}').html(`${price_code}<span class="p-amount">${Number(planTier['{{$plan->uid}}'][$(this).val()]['price'])}</span><span class="p-currency-code">${country_code}</span>`)
                                        $('#comperePrice{{ $plan->uid }}').html( `${price_code} <span class="p-amount"> ${(Number(planTier['{{ $plan->uid }}'][$(this).val()]['price']/12).toFixed(2))}</span><span class="p-currency-code"> ${country_code}</span>`)
                                    });
                                </script>
                                @else
                                    <h4 class="mb-5">Subscribers :
                                        {{ $plan->displayTotalSubscribers() }}</h4>
                                    <hr>
                                    <h5>Up to {{ $plan->displayTotalQuota() }}
                                        emails/{{ $plan->displayFrequencyTime() }} </h5>
                                    <h5>Up to {{ $plan->displayTotalUsers() }} Users </h5>
                                @endif
                            </div>
                            <hr class="mb-2" style="width: 40px">
                            <span class="time-box d-block text-center small py-2 fw-600 mb-5">
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
                                        <span>{{trans('messages.plan.has_ip_address')}}</span>
                                    </div>
                                @endif
                            </span>
                            <div>
                                <div style="vertical-align:bottom">
                                    @if ($plan->isFree() || $plan->hasTrial())
                                    <a link-method="POST" onclick="this.href='/account/subscription/init?plan_uid={{$plan->uid}}&currency='+ $('#currency_convert option:selected' ).val()"
                                        class="btn fw-600 btn-primary rounded-3 d-block py-2 shadow-sm">
                                        
                                            {{ trans('messages.plan.select') }}</a>
                                        @else
                                        <a link-method="POST" onclick="this.href='/account/subscription/init?plan_uid={{$plan->uid}}&tier='+document.getElementById('discount_credits{{$plan->uid}}').value+'&currency='+ $('#currency_convert option:selected' ).val()"
                                        class="btn fw-600 btn-primary rounded-3 d-block py-2 shadow-sm">
                                    
                                            {{ trans('messages.plan.buy') }}</a>
                                        @endif
                                    
                                    @if ($plan->hasTrial())
                                        <p link-method="POST" href="{{ action('SubscriptionController@init', [
                                                'plan_uid' => $plan->uid,
                                                'trial' => true,
                                            ]) }}"
                                            class="mt-3 fw-600 mb-0 text-center">
                                            {{ trans('messages.plan.has_free', [
                                                'num' => $plan->getTrialPeriodTimePhrase(),
                                            ]) }}
                                        </p>
                                    @endif
                                </div>
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
                        @if ($plan->displayFrequencyTime() == "month")
                        <div data-url="{{ action('SubscriptionController@orderBox', ['plan_uid' => $plan->uid]) }}"
                            class="new-price-item mb-3 d-inline-block plan-item {{ $subscription && $subscription->plan->id == $plan->id ? 'current' : '' }}"
                            style="width: calc(33% - 20px)">
                            <div class="mt-2">
                                <label class="plan-title fs-5 fw-600 mt-0">{{ $plan->name }}</label>
                            </div>
                            <div style="height:70px">
                                <p>{{ $plan->description }}</p>
                            </div>
                            <div style="height: 250px">
                                <div class="price" id="newPrice{{$plan->uid}}">
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
                                <div id="rangeSildermonth{{$plan->uid}}">

                                </div>
                                <script>
                                    planTier['{{$plan->uid}}'] = [{'price' :'{{$plan->price}}' }]
                                    oldcurrencyPrice['{{$plan->uid}}'] = [{'price' :'{{$plan->price}}' }]
                                </script>
                                @if (is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                <script>
                                    var credits = 0;
                                    planTier['{{$plan->uid}}'] = JSON.parse('{!!$plan->tier!!}');
                                    oldcurrencyPrice['{{$plan->uid}}'] = JSON.parse('{!!$plan->tier!!}');
                                    html = `
                                            <h4>Subscribers : @if($plan->displayTotalSubscribers() != 'Unlimited')<span id="newSubscriber{{$plan->uid}}">${planTier['{{$plan->uid}}'][credits]['subscriber_max']/1000}k</span>@else {{$plan->displayTotalSubscribers()}} @endif</h4>
                                            <input type="range" class="w-100" style="accent-color: #B91827;" min="0" max="{{count(json_decode($plan->tier))-1}}" value="${credits}" name="discount_credits_month" id="discount_credits_month{{$plan->uid}}" />
                                            <hr><h5> @if($plan->displayTotalQuota() != 'Unlimited')Up to <span id="newEmails{{$plan->uid}}">${planTier['{{$plan->uid}}'][credits]['email_max']/1000}k</span> @else {{$plan->displayTotalQuota()}} @endif emails/{{$plan->displayFrequencyTime()}} </h5>
                                            <h5> @if($plan->displayTotalUsers() != 'Unlimited') Up to <span id="newUsers{{$plan->uid}}">${planTier['{{$plan->uid}}'][credits]['users_max']}</span> @else {{$plan->displayTotalUsers()}} @endif Users </h5>
                                            `;

                                    $("#rangeSildermonth{{$plan->uid}}").append(html);

                                    $('#discount_credits_month{{$plan->uid}}').on("change mousemove", function() {
                                        if ("{{$plan->displayTotalQuota()}}" != 'Unlimited') {
                                            $('#newEmails{{$plan->uid}}').html(convertAmount(planTier['{{$plan->uid}}'][$(this).val()]['email_max']/1000))
                                        }
                                        $('#newSubscriber{{$plan->uid}}').html(convertAmount(planTier['{{$plan->uid}}'][$(this).val()]['subscriber_max']/1000))
                                        $('#newUsers{{$plan->uid}}').html(planTier['{{$plan->uid}}'][$(this).val()]['users_max'])
                                        $('#newPrice{{$plan->uid}}').html(`${price_code} <span class="p-amount">${Number(planTier['{{$plan->uid}}'][$(this).val()]['price'])}</span><span class="p-currency-code">${country_code}</span>`)
                                    });
                                </script>  
                            @else
                                <h4 class="mb-5">Subscribers :
                                    {{ $plan->displayTotalSubscribers() }}</h4>
                                <hr>
                                <h5>Up to {{ $plan->displayTotalQuota() }}
                                    emails/{{ $plan->displayFrequencyTime() }} </h5>
                                <h5>Up to {{ $plan->displayTotalUsers() }} Users </h5>
                            @endif
                            </div>
                            <hr class="mb-2" style="width: 40px">
                            <span class="time-box d-block text-center small py-2 fw-600 mb-5">
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
                                        <span>{{trans('messages.plan.has_ip_address')}}</span>
                                    </div>
                                @endif
                            </span>
                            <div>
                                <div style="vertical-align:bottom">
                                    @if ($plan->isFree() || $plan->hasTrial())
                                    <a link-method="POST" onclick="this.href='/account/subscription/init?plan_uid={{$plan->uid}}&currency='+ $('#currency_convert option:selected' ).val()"
                                        class="btn fw-600 btn-primary rounded-3 d-block py-2 shadow-sm">
                                        
                                            {{ trans('messages.plan.select') }}
                                    </a>
                                        @else
                                          <a link-method="POST" onclick="this.href='/account/subscription/init?plan_uid={{$plan->uid}}&tier='+document.getElementById('discount_credits_month{{$plan->uid}}').value+'&currency='+ $('#currency_convert option:selected' ).val()"
                                        class="btn fw-600 btn-primary rounded-3 d-block py-2 shadow-sm">
                                        
                                            {{ trans('messages.plan.buy') }}
                                    </a>
                                        @endif
                                    @if ($plan->hasTrial())
                                        <p link-method="POST" href="{{ action('SubscriptionController@init', [
                                                'plan_uid' => $plan->uid,
                                                'trial' => true,
                                            ]) }}"
                                            class="mt-3 fw-600 mb-0 text-center">
                                            {{ trans('messages.plan.has_free', [
                                                'num' => $plan->getTrialPeriodTimePhrase(),
                                            ]) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div> --}}

<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
        <div class="new-price-box" style="margin-right: -30px">
            <div class="d-flex justify-content-center planSec">
                @foreach ($plans as $key => $plan)
                    @if ($plan->displayFrequencyTime() == 'year')
                        <div data-url="{{ action('SubscriptionController@orderBox', ['plan_uid' => $plan->uid]) }}"
                            class="new-price-item mb-3 d-inline-block plan-item {{ $subscription && $subscription->plan->id == $plan->id ? 'current' : '' }}"
                            style="width: calc(33% - 20px)">
                            <div class="mt-2">
                                <label class="plan-title fs-5 fw-600 mt-0">{{ $plan->name }}</label>
                            </div>
                            <div style="height:75px">
                                <p>{{ $plan->description }}</p>
                            </div>
                            <div style="height: 250px">
                                <div class="price" id="comperePrice{{$plan->uid}}">
                                    @if (!$plan->isFree() && is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                        {!! format_price((json_decode($plan->tier)[0]->price/12), $plan->currency->format, true) !!}<span class="p-currency-code">{{ $plan->currency->code }}</span>
                                    @else
                                        @if (!$plan->isFree())
                                            {!! format_price(($plan->price/12), $plan->currency->format, true) !!}<span class="p-currency-code">{{ $plan->currency->code }}</span>
                                        @else
                                            <div class=""></div>
                                        @endif
                                    @endif
                                </div>
                                <div class="d-flex">
                                    <h5 class="mr-2 mt-1 {{$plan->isFree() ? 'd-none' : ''}}">Billed yearly as</h5>
                                    <div class="h4 {{$plan->isFree() ? 'price' : ''}}" id="newPrice{{ $plan->uid }}">
                                        @if (is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                            {!! format_price(json_decode($plan->tier)[0]->price, $plan->currency->format, true) !!}<span class="p-currency-code">{{ $plan->currency->code }}</span>
                                        @else
                                            {!! format_price($plan->price, $plan->currency->format, true) !!}<span class="p-currency-code">{{ $plan->currency->code }}</span>
                                        @endif
                                    </div>
                                </div>
                                @if (is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                    <div id="rangeSilder{{ $plan->uid }}">

                                    </div>
                                @endif
                                <script>
                                    planTier['{{$plan->uid}}'] = [{'price' :'{{$plan->price}}' }]
                                    oldcurrencyPrice['{{$plan->uid}}'] = [{'price' :'{{$plan->price}}' }]
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
                                                $('#newEmails{{ $plan->uid }}').html(convertAmount(planTier['{{ $plan->uid }}'][$(this).val()]['email_max'] / 1000))
                                            }
                                            $('#newSubscriber{{ $plan->uid }}').html(convertAmount(planTier['{{ $plan->uid }}'][$(this).val()]['subscriber_max'] / 1000))
                                            $('#newUsers{{ $plan->uid }}').html(planTier['{{ $plan->uid }}'][$(this).val()]['users_max'])
                                            $('#newPrice{{ $plan->uid }}').html(`${price_code}<span class="p-amount">${Number(planTier['{{ $plan->uid }}'][$(this).val()]['price'])}</span> <span class="p-currency-code">${country_code}</span>`)
                                            $('#comperePrice{{ $plan->uid }}').html(`${price_code}<span class="p-amount">${Number((planTier['{{ $plan->uid }}'][$(this).val()]['price']/12).toFixed(2))}</span> <span class="p-currency-code">${country_code}</span>`)
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
                            <hr class="mb-2 {{$plan->isFree() ? 'mt-1' : ''}}" style="width: 40px">
                            <span class="time-box d-block text-center small py-2 fw-600 mb-5" style="min-height: 9rem">
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
                                        <span>{{trans('messages.plan.has_ip_address')}}</span>
                                    </div>
                                @endif
                            </span>
                            <div>
                                <div style="vertical-align:bottom">
                                    @if ($plan->isFree() || $plan->hasTrial())
                                    <a link-method="POST" onclick="this.href='/account/subscription/init?plan_uid={{$plan->uid}}&currency='+ $('#currency_convert option:selected' ).val()"
                                        class="btn fw-600 btn-primary rounded-3 d-block py-2 shadow-sm">
                                            {{ trans('messages.subscription.get_started') }}</a>
                                        @else
                                        <a link-method="POST" onclick="this.href='/account/subscription/init?plan_uid={{$plan->uid}}&tier='+document.getElementById('discount_credits{{$plan->uid}}').value+'&currency='+ $('#currency_convert option:selected' ).val()"
                                            class="btn fw-600 btn-primary rounded-3 d-block py-2 shadow-sm">
                                            {{ trans('messages.subscription.get_started') }}</a>
                                        @endif
                                    
                                    @if ($plan->hasTrial())
                                        <p link-method="POST" href="{{ action('SubscriptionController@init', [
                                                'plan_uid' => $plan->uid,
                                                'trial' => true,
                                            ]) }}"
                                            class="mt-3 fw-600 mb-0 text-center">
                                            {{ trans('messages.plan.has_free', [
                                                'num' => $plan->getTrialPeriodTimePhrase(),
                                            ]) }}
                                        </p>
                                    @endif
                                </div>
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
                            class="new-price-item mb-3 d-inline-block plan-item {{ $subscription && $subscription->plan->id == $plan->id ? 'current' : '' }}"
                            style="width: calc(33% - 20px)">
                            <div class="mt-2">
                                <label class="plan-title fs-5 fw-600 mt-0">{{ $plan->name }}</label>
                            </div>
                            <div style="height:70px">
                                <p>{{ $plan->description }}</p>
                            </div>
                            <div style="height: 250px">
                                <div class="price" id="newPrice{{$plan->uid}}">
                                    @if (is_countable(json_decode($plan->tier)) && count(json_decode($plan->tier)) != 0)
                                        {!! format_price(json_decode($plan->tier)[0]->price, $plan->currency->format, true) !!}<span class="p-currency-code">{{ $plan->currency->code }}</span>
                                    @else
                                        {!! format_price($plan->price, $plan->currency->format, true) !!}<span class="p-currency-code">{{ $plan->currency->code }}</span>
                                    @endif
                                </div>
                                <div id="rangeSildermonth{{ $plan->uid }}">

                                </div>
                                <script>
                                    planTier['{{$plan->uid}}'] = [{'price':'{{$plan->price}}' }]
                                    oldcurrencyPrice['{{$plan->uid}}'] = [{'price':'{{$plan->price}}' }]
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
                                                $('#newEmails{{ $plan->uid }}').html(convertAmount(planTier['{{ $plan->uid }}'][$(this).val()]['email_max'] / 1000))
                                            }
                                            $('#newSubscriber{{ $plan->uid }}').html(convertAmount(planTier['{{ $plan->uid }}'][$(this).val()]['subscriber_max'] / 1000))
                                            $('#newUsers{{ $plan->uid }}').html(planTier['{{ $plan->uid }}'][$(this).val()]['users_max'])
                                            $('#newPrice{{ $plan->uid }}').html(`${price_code}<span class="p-amount">${Number(planTier['{{ $plan->uid }}'][$(this).val()]['price'])}</span> <span class="p-currency-code">${country_code}</span>`)
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
                            <span class="time-box d-block text-center small py-2 fw-600 mb-5" style="min-height: 9rem">
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
                                        <span>{{trans('messages.plan.has_ip_address')}}</span>
                                    </div>
                                @endif
                            </span>
                        <div>
                            <div style="vertical-align:bottom">
                                @if ($plan->isFree() || $plan->hasTrial())
                                <a link-method="POST" onclick="this.href='/account/subscription/init?plan_uid={{$plan->uid}}&currency='+ $('#currency_convert option:selected' ).val()"
                                    class="btn fw-600 btn-primary rounded-3 d-block py-2 shadow-sm">
                                    {{ trans('messages.subscription.get_started') }}
                                </a>
                                    @else
                                      <a link-method="POST" onclick="this.href='/account/subscription/init?plan_uid={{$plan->uid}}&tier='+document.getElementById('discount_credits_month{{$plan->uid}}').value+'&currency='+ $('#currency_convert option:selected' ).val()"
                                        class="btn fw-600 btn-primary rounded-3 d-block py-2 shadow-sm">
                                        {{ trans('messages.subscription.get_started') }}
                                </a>
                                    @endif
                                @if ($plan->hasTrial())
                                    <p link-method="POST" href="{{ action('SubscriptionController@init', [
                                            'plan_uid' => $plan->uid,
                                            'trial' => true,
                                        ]) }}"
                                        class="mt-3 fw-600 mb-0 text-center">
                                        {{ trans('messages.plan.has_free', [
                                            'num' => $plan->getTrialPeriodTimePhrase(),
                                        ]) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<script>
    function convertAmount(val) {
        if (val.toString().length <= 3) {
            return val+'K';
        } else {
            return parseInt(val/1000)+'M';
        }
    }
    @if (!empty($subscription->user_plan))
        $(document).ready(function () {
            $("#currency_convert option").filter(function() {
            //may want to use $.trim in here
            return $(this).val() == JSON.parse('{!!$subscription->user_plan!!}').currency;
            }).prop('selected', true);
            priceCode = JSON.parse('{!!$subscription->user_plan!!}').currency
            var selected =  $(this).find('option:selected');
            // $.ajax({
            //     type: "get",
            //     url: "https://api.exchangerate-api.com/v4/latest/USD",
            //     success: function (response) {
            //         $('.p-currency-code').text(priceCode);
            //         country_code = ' '+priceCode;
            //         price_code = selected.data('icon')[0]+' ';
            //         Object.keys(planTier).forEach(function (key) {
            //             for (let index = 0; index < planTier[key].length; index++) {
            //                 for (let index = 0; index < planTier[key].length; index++) {
            //                     planTier[key][index]['price'] = (oldcurrencyPrice[key][index]['price']*(response.rates[priceCode]-(response.rates[priceCode]*selected.data('localisation_factor')/100))).toFixed(2);
            //                     if (index == 0) {
            //                         $('#newPrice'+key).html(`${price_code}<span class="p-amount">${planTier[key][index]['price']}</span><span class="p-currency-code">${country_code}</span>`)
            //                     }
            //                 }
            //             }
            //         });
            //     }
            // });
            $('.p-currency-code').text(priceCode);
                country_code = ' ' + priceCode;
                usd_value = selected.data('icon')[0];
                price_code = selected.data('icon')[0] + ' ';
                Object.keys(planTier).forEach(function(key) {
                    for (let index = 0; index < planTier[key].length; index++) {
                        planTier[key][index]['price'] = Number((oldcurrencyPrice[key][index]['price'] * (selected.data('symbal') - (selected.data('symbal') * selected.data('localisation_factor') / 100))).toFixed(2));
                        if (index == 0) {
                            $('#newPrice' + key).html(`${price_code}<span class="p-amount">${planTier[key][index]['price']}</span><span class="p-currency-code">${country_code}</span>`)
                            if ($(`#comperePrice${key}`).length){
                                if (oldcurrencyPrice[key][index]['price'] != 0) {
                                    $(`#comperePrice${key}`).html( `${price_code} <span class="p-amount"> ${Number(((oldcurrencyPrice[key][index]['price'] * (selected.data('symbal') - (selected.data('symbal') * selected.data('localisation_factor') / 100)))/12).toFixed(2))}</span><span class="p-currency-code"> ${country_code}</span>`)
                                }
                            }
                        }
                    }
                });
        });
    @endif
    
    //  $('#currency_convert').change(function (e) { 
    //     var selected =  $(this).find('option:selected');
    //     $.ajax({
    //         type: "get",
    //         url: "https://api.exchangerate-api.com/v4/latest/USD",
    //         success: function (response) {
    //             $('.p-currency-code').text(e.target.value);
    //             country_code = ' '+e.target.value;
    //             price_code = selected.data('icon')[0]+' ';
    //             Object.keys(planTier).forEach(function (key) {
    //                 for (let index = 0; index < planTier[key].length; index++) {
    //                     planTier[key][index]['price'] = (oldcurrencyPrice[key][index]['price']*(response.rates[e.target.value]-(response.rates[e.target.value]*selected.data('localisation_factor')/100))).toFixed(2);
    //                     if (index == 0) {
    //                         $('#newPrice'+key).html(`${price_code}<span class="p-amount">${planTier[key][index]['price']}</span><span class="p-currency-code">${country_code}</span>`)
    //                     }
    //                 }
    //             });
    //         }
    //     });
    //  });

    $('#currency_convert').change(function(e) {
        var selected = $(this).find('option:selected');
        // $.ajax({
        //     type: "get",
        //     // url: "https://api.exchangerate-api.com/v4/latest/USD",
        //     success: function(response) {
            //sybmal mean usd Value 
                $('.p-currency-code').text(e.target.value);
                country_code = ' ' + e.target.value;
                usd_value = selected.data('icon')[0];
                price_code = selected.data('icon')[0] + '';
                Object.keys(planTier).forEach(function(key) {
                    for (let index = 0; index < planTier[key].length; index++) {
                        planTier[key][index]['price'] = Number((oldcurrencyPrice[key][index]['price'] * (selected.data('symbal') - (selected.data('symbal') * selected.data('localisation_factor') / 100))).toFixed(2));
                        if (index == 0) {
                            $('#newPrice' + key).html(`${price_code}<span class="p-amount">${planTier[key][index]['price']}</span><span class="p-currency-code">${country_code}</span>`)
                            if ($(`#comperePrice${key}`).length){
                                if (oldcurrencyPrice[key][index]['price'] != 0) {
                                    $(`#comperePrice${key}`).html( `${price_code} <span class="p-amount"> ${Number(((oldcurrencyPrice[key][index]['price'] * (selected.data('symbal') - (selected.data('symbal') * selected.data('localisation_factor') / 100)))/12).toFixed(2))}</span><span class="p-currency-code"> ${country_code}</span>`)
                                }
                            }
                        }
                    }
                });
            // }
        // });
    });
</script>