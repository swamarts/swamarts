@extends('layouts.core.backend', [
    'menu' => 'plan',
])

@section('title', $plan->name)

@section('head')
    <script type="text/javascript" src="{{ URL::asset('core/js/group-manager.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li class="breadcrumb-item"><a href="{{ action('Admin\HomeController@index') }}">{{ trans('messages.home') }}</a>
            </li>
            <li class="breadcrumb-item"><a href="{{ action('Admin\PlanController@index') }}">{{ trans('messages.plans') }}</a>
            </li>
        </ul>
        <h1 class="mc-h1">
            <span class="text-semibold">{{ $plan->name }}</span>
        </h1>
    </div>

@endsection

@section('content')

    @include('admin.plans._menu', [
        'menu' => 'tier',
    ])

    <form enctype="multipart/form-data" action="{{ action('Admin\PlanController@save', $plan->uid) }}" method="POST"
        class="form-validate-jqueryx">
        <div class="row">
            <div class="col-md-12">
                {{ csrf_field() }}
                <div class="mc_section">
                    <h2>{{ trans('messages.plan.tier') }}</h2>

                    @include('elements._notification', [
                        'level' => 'warning',
                        'message' => trans('messages.plan.tier.intro'),
                    ])
                    <div class="unlimited_controls">
                        <div class="mb-3 unlimited_control row">
                            @if (empty($tiers))
                            {{-- {{dd(request())}} --}}
                                <div class="row col-md-10">
                                    <div id="newtier">
                                        <div class="row" id="starttier">
                                            <div class="form-group col-md-2">
                                                @include('helpers.form_control', [
                                                    'class' => 'numeric',
                                                    'type' => 'text',
                                                    'name' => 'plan[tier][price][]',
                                                    'label' => trans('messages.tier.price'),
                                                    'value' => Session::get('oldRequest')['price'][0] ?? '',
                                                    'help_class' => 'plan',
                                                    'rules' => $plan->generalRules(),
                                                    'attributes' => [
                                                        'required' => 'required',
                                                    ],
                                                ])
                                            </div>
                                            <div class="form-group col-md-3">
                                                @include('helpers.form_control.control', [
                                                    'type' => 'number',
                                                    'name' => 'plan[tier][subscriber_max][]',
                                                    'label' => trans('messages.tier.subscribers_count'),
                                                    'value' => Session::get('oldRequest')['subscriber_max'][0] ?? '',
                                                    'help_class' => 'plan',
                                                    'rules' => $plan->generalRules(),
                                                    'attributes' => [
                                                        'required' => 'required',
                                                    ],
                                                ])
                                            </div>
                                            <div class="form-group col-md-3">
                                                @include('helpers.form_control.control', [
                                                    'type' => 'number',
                                                    'name' => 'plan[tier][email_max][]',
                                                    'label' => trans('messages.tier.emails_count'),
                                                    'value' => Session::get('oldRequest')['email_max'][0] ?? '',
                                                    'help_class' => 'plan',
                                                    'rules' => $plan->generalRules(),
                                                    'attributes' => [
                                                        'required' => 'required',
                                                    ],
                                                ])
                                            </div>
                                            <div class="form-group col-md-3">
                                                @include('helpers.form_control.control', [
                                                    'type' => 'number',
                                                    'name' => 'plan[tier][users_max][]',
                                                    'label' => trans('messages.tier.users_count'),
                                                    'value' => Session::get('oldRequest')['users_max'][0] ?? '',
                                                    'help_class' => 'plan',
                                                    'rules' => $plan->generalRules(),
                                                    'attributes' => [
                                                        'required' => 'required',
                                                    ],
                                                ])
                                            </div>
                                            <div class="col-md-1 mt-4 pt-1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row col-md-10">
                                    <div id="newtier">
                                        @foreach ($tiers as $key => $item)
                                            <div class="row" id="{{ $key }}">
                                                <div class="form-group col-md-2">
                                                    @include('helpers.form_control', [
                                                        'class' => 'numeric',
                                                        'type' => 'text',
                                                        'name' => 'plan[tier][price][' . $key . ']',
                                                        'label' => trans('messages.tier.price'),
                                                        'value' => $item->price,
                                                        'help_class' => 'plan',
                                                        'rules' => $plan->generalRules(),
                                                        'attributes' => [
                                                            'required' => 'required',
                                                        ],
                                                    ])
                                                </div>
                                                <div class="form-group col-md-3">
                                                    @include('helpers.form_control.control', [
                                                        'type' => 'number',
                                                        'name' => 'plan[tier][subscriber_max][' . $key . ']',
                                                        'label' => trans('messages.tier.subscribers_count'),
                                                        'value' => $item->subscriber_max,
                                                        'help_class' => 'plan',
                                                        'rules' => $plan->generalRules(),
                                                        'attributes' => [
                                                            'required' => 'required',
                                                        ],
                                                    ])
                                                </div>
                                                <div class="form-group col-md-3">
                                                    @include('helpers.form_control.control', [
                                                        'type' => 'number',
                                                        'name' => 'plan[tier][email_max][' . $key . ']',
                                                        'label' => trans('messages.tier.emails_count'),
                                                        'value' => $item->email_max,
                                                        'help_class' => 'plan',
                                                        'rules' => $plan->generalRules(),
                                                        'attributes' => [
                                                            'required' => 'required',
                                                        ],
                                                    ])
                                                </div>
                                                <div class="form-group col-md-3">
                                                    @include('helpers.form_control.control', [
                                                        'type' => 'number',
                                                        'name' => 'plan[tier][users_max][' . $key . ']',
                                                        'label' => trans('messages.tier.users_count'),
                                                        'value' => $item->users_max,
                                                        'help_class' => 'plan',
                                                        'rules' => $plan->generalRules(),
                                                        'attributes' => [
                                                            'required' => 'required',
                                                        ],
                                                    ])
                                                </div>
                                                <div class="col-md-1 mt-4 pt-1">
                                                    <button class="btn btn-danger"
                                                        onclick="removeButtonTier({{ $key }})"
                                                        type="button">-</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <button class="btn btn-secondary me-2" id="submitTier">{{ trans('messages.save') }}</button>
                                <a href="{{ action('Admin\PlanController@index') }}" role="button" class="btn btn-link">
                                    {{ trans('messages.cancel') }}
                                </a>
                            </div>
                            <div class="col-md-6 text-end pe-1">
                                <button class="btn btn-primary ms-4" id="addtierbutton"
                                    type="button">{{ trans('messages.tier.add_more') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(function() {
            var manager = new GroupManager();

            $('.unlimited_controls .unlimited_control').each(function() {
                manager.add({
                    textBox: $(this).find('input[type=text],input[type=number]'),
                    unlimitedCheck: $(this).find('input[type=checkbox]'),
                    defaultValue: $(this).find('input[type=text],input[type=number]').attr(
                        'default-value'),
                    currentValue: $(this).find('input[type=text],input[type=number]').val()
                });
            });

            manager.bind(function(group) {
                var doCheck = function() {
                    var checked = group.unlimitedCheck.is(':checked');

                    if (checked) {
                        group.currentValue = group.textBox.val();
                        group.textBox.val(-1);
                        group.textBox.addClass("text-trans");
                        group.textBox.attr("readonly", "readonly");
                    } else {
                        if (group.textBox.val() == "-1") {
                            if (group.currentValue != "-1") {
                                group.textBox.val(group.currentValue);
                            } else {
                                group.textBox.val(group.defaultValue);
                            }
                        }
                        group.textBox.removeClass("text-trans");
                        group.textBox.removeAttr("readonly", "readonly");
                    }
                };

                group.unlimitedCheck.on('change', function() {
                    doCheck();
                });

                doCheck();
            });
        });

        $('#addtierbutton').click(function(e) {
            uniqueId = Math.floor(Math.random() * 100) + 1;
            html = `<div class="row" id="${uniqueId}">
                        <div class="form-group col-md-2">
                            @include('helpers.form_control', [
                                'class' => 'numeric',
                                'type' => 'text',
                                'name' => 'plan[tier][price][]',
                                'label' => trans('messages.tier.price'),
                                'value' => '',
                                'help_class' => 'plan',
                                'rules' => $plan->generalRules(),
                                'attributes' => [
                                    'required' => 'required',
                                ],
                            ])
                        </div>
                        <div class="form-group col-md-3">
                            @include('helpers.form_control.control', [
                                'type' => 'number',
                                'name' => 'plan[tier][subscriber_max][]',
                                'label' => trans('messages.tier.subscribers_count'),
                                'value' => '',
                                'help_class' => 'plan',
                                'rules' => $plan->generalRules(),
                                'attributes' => [
                                    'required' => 'required',
                                ],
                            ])
                        </div>
                        <div class="form-group col-md-3">
                            @include('helpers.form_control.control', [
                                'type' => 'number',
                                'name' => 'plan[tier][email_max][]',
                                'label' => trans('messages.tier.emails_count'),
                                'value' => '',
                                'help_class' => 'plan',
                                'rules' => $plan->generalRules(),
                                'attributes' => [
                                    'required' => 'required',
                                ],
                            ])
                        </div>
                        <div class="form-group col-md-3">
                            @include('helpers.form_control.control', [
                                'type' => 'number',
                                'name' => 'plan[tier][users_max][]',
                                'label' => trans('messages.tier.users_count'),
                                'value' => '',
                                'help_class' => 'plan',
                                'rules' => $plan->generalRules(),
                                'attributes' => [
                                    'required' => 'required',
                                ],
                            ])
                        </div>
                        <div class="col-md-1 mt-4 pt-1">
                            <button class="btn btn-danger" onclick="removeButtonTier(${uniqueId})" type="button">-</button>
                        </div>
                    </div>`;
            $('#newtier').append(html);
        });

        function removeButtonTier(id) {
            $('#' + id).remove();
        }
    </script>
@endsection
