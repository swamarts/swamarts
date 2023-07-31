<ul class="dotted-list topborder section">
    <li>
        <div class="unit size1of2">
            <strong>{{ trans('messages.plan_name') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag><strong>{{ $plan->name }}</strong></mc:flag>
        </div>
    </li>
    <li class="selfclear">
        <div class="unit size1of2">
            <strong>{{ trans('messages.price.excluding') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            @php
                $priceDetails = 0;
                if(empty(json_decode($subscription->user_plan)->price)){
                    $priceDetails = $plan->price;
                }else{
                    $priceDetails = json_decode($subscription->user_plan)->price;
                }
            @endphp
            <mc:flag><strong>{{ Acelle\Library\Tool::format_price(($priceDetails*$currency['amount']), $currency['format']['format'])}}</strong></mc:flag>
        </div>
    </li>
    <li class="selfclear">
        <div class="unit size1of2">
            <strong>{{ trans('messages.duration') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag class="text-capitalize">{{ $plan->displayFrequencyTime() }}ly</mc:flag>
        </div>
    </li>
    <li class="selfclear">
        <div class="unit size1of2">
            <strong>{{ trans('messages.sending_quota_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            @if (!empty($subscription->user_plan) && !empty(json_decode($subscription->user_plan)->email_max))
                <mc:flag>{{ number_format(json_decode($subscription->user_plan)->email_max) ?? $plan->displayTotalQuota() }}</mc:flag>
            @else
                <mc:flag>{{ $plan->displayTotalQuota() }}</mc:flag>            
            @endif
        </div>
    </li>
    <li class="more">
        <a href="#more">{{ trans('messages.more_details') }}</a>
    </li>
    <li class="selfclear hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.max_lists_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag>{{ $plan->displayMaxList() }}</mc:flag>
        </div>
    </li>
        
    <li class="selfclear hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.max_subscribers_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            @if (!empty($subscription->user_plan) && !empty(json_decode($subscription->user_plan)->subscriber_max))
                <mc:flag>{{ number_format(json_decode($subscription->user_plan)->subscriber_max) ?? $plan->displayTotalQuota() }}</mc:flag>
            @else
                <mc:flag>{{ $plan->displayMaxSubscriber() }}</mc:flag>
            @endif
        </div>
    </li>
    <li class="selfclear hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.max_campaigns_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag>{{ $plan->displayMaxCampaign() }}</mc:flag>
        </div>
    </li>
    <li class="selfclear hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.max_size_upload_total_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag>{{ $plan->displayMaxSizeUploadTotal() }}</mc:flag>
        </div>
    </li>
    <li class="selfclear hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.max_file_size_upload_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag>{{ $plan->displayFileSizeUpload() }}</mc:flag>
        </div>
    </li>
    <li class="selfclear hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.no_of_users') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            @if (!empty($subscription->user_plan) && !empty(json_decode($subscription->user_plan)->users_max))
                <mc:flag>{{ json_decode($subscription->user_plan)->users_max }}</mc:flag>
            @else
                <mc:flag>1</mc:flag>      
            @endif
        </div>
    </li>
    {{-- <li class="selfclear hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.allow_create_sending_servers_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag>{!! $plan->displayAllowCreateSendingServer() !!}</mc:flag>
        </div>
    </li>
    <li class="selfclear hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.allow_create_sending_domains_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag>{!! $plan->displayAllowCreateSendingDomain() !!}</mc:flag>
        </div>
    </li>
    <li class="selfclear hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.allow_create_email_verification_servers_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag>{!! $plan->displayAllowCreateEmailVerificationServer() !!}</mc:flag>
        </div>
    </li> --}}
</ul>
