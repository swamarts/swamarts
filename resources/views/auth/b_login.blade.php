@extends('layouts.core.register')
@section('title', trans('messages.login'))
@section('content')
    <style type="text/css">
        /* Animated Wave Background Style  */
        html,
        body {
            width: 100%;
            height: 100%;
        }
        body {
            background-image: url(https://alpha.yourwebsitedemos.com/web/Sendimpacttv1/wp-content/uploads/2023/05/New-Project-2023-05-08T175108.226.jpg);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            overflow: scroll;
        }

        input#vehicle1 {
            width: 8%;
        }

        .term {
            display: inline-flex;
        }

        .ocean {
            height: 5%;
            width: 100%;
            position: absolute;
            bottom: 0;
            left: 0;
            background: #B91827;
        }

        .social-container a {
            display: inline-block;
            width: 100%;
            background-color: #d1d1d1 !important;
            margin: 0px;
            position: relative;
            padding: 14px 0px;
            margin-top: 10px;
            color: #000;
            font-size: 15px;
            font-weight: 600;
            border-radius: 30px;
            opacity: 1 !important;
        }

        .social-container a img {
            width: 10%;
            position: absolute;
            left: 10px;
            top: 7px;
        }

        /*.wave {
            background: url(https://alpha.yourwebsitedemos.com/web/Sendimpacttv1/wp-content/uploads/2023/03/wave-svg-1.svg) repeat-x;
            position: absolute;
            top: -198px;
            width: 6400px;
            height: 198px;
            animation: wave 5s cubic-bezier( 0.36, 0.45, 0.63, 0.53) infinite;
            transform: translate3d(0, 0, 0);
        }*/



        body {
            font-family: 'Montserrat', sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: -20px 0 50px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        h1 {
            font-weight: bold;
            margin: 0;
        }

        p {
            font-size: 14px;
            font-weight: 100;
            line-height: 20px;
            letter-spacing: .5px;
            margin: 20px 0 30px;
        }

        span {
            font-size: 12px;
        }

        a {
            color: #0e263d;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }

        .container {
            background: #fff;
            border-radius: 90px;
            box-shadow: 30px 14px 28px rgba(0, 0, 5, .2), 0 10px 10px rgba(0, 0, 0, .2);
            position: relative;
            overflow: hidden;
            opacity: 85%;
            width: 768px;
            max-width: 100%;
            min-height: 650px;
            transition: 333ms;
        }


        .form-container form {
            background: #fff;
            display: flex;
            flex-direction: column;
            padding: 0 20px;
            height: 100%;
            text-align: center;
            margin-top: 7%;
        }

        .form-container input {
            background: #eee;
            border: none;
            border-radius: 50px;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
        }

        .form-container input:hover {
            transform: scale(101%);
        }

        button {
            border-radius: 50px;
            box-shadow: 0 1px 1px;
            border: 1px solid #B91827;
            background: #B91827;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            color: #fff;
        }

        button:active {
            transform: scale(.95);
        }

        button:focus {
            outline: none;
        }

        button.ghost {
            background: transparent;
            border-color: #fff;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all .6s ease-in-out;
        }

        .sign-in-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .sign-up-container {
            left: 0;
            width: 50%;
            z-index: 1;
            opacity: 0;
        }

        button#signIn {
            margin-top: 6%;
            margin-top: 6%;
            position: relative;
            left: -23%;
            top: 3%;
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform .6s ease-in-out;
            z-index: 100;
        }

        ul li {
            text-align: left;
            font-size: 16px;

        }

        select {
            width: 95% !important;
            background: #eee;
            border: none;
            border-radius: 50px;
            padding: 12px 15px;
            margin: 8px 0;
        }

        ul {
            position: relative;
            left: -25%;
            top: 4%;
        }

        .overlay {
            background: #B91827;
            background: #1D5A8B;
            color: #fff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateY(0);
            transition: transform .6s ease-in-out;
        }

        .overlay-panel {
            position: absolute;
            top: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0 40px;
            height: 100%;
            width: 50%;
            text-align: center;
            transform: translateY(0);
            transition: transform .6s ease-in-out;
        }

        .overlay-right {
            right: 0;
            transform: translateY(0);
        }

        .overlay-left {
            transform: translateY(-20%);
        }

        /* Move signin to right */
        .container.right-panel-active .sign-in-container {
            transform: translateY(100%);
        }

        /* Move overlay to left */
        .container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }

        /* Bring signup over signin */
        .container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
        }

        /* Move overlay back to right */
        .container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        /* Bring back the text to center */
        .container.right-panel-active .overlay-left {
            transform: translateY(0);
        }

        /* Same effect for right */
        .container.right-panel-active .overlay-right {
            transform: translateY(20%);
        }

        /* https://Github.com/YasinDehfuli
           https://Codepen.io/YasinDehfuli */

        .sign-in-container img {
            width: 85%;
            margin: 0 auto;
        }

        h1 {
            font-size: 30px;
        }

        .sign-up-btn {
            width: 80%;
            margin: 0 auto;
            margin-top: -10px !important;
        }
    </style>
    <section>
        <div class="container {{old('sign') == 'signForm' ? 'right-panel-active' : '' }}" id="container">
            <div class="form-container sign-up-container">
                <form enctype="multipart/form-data" action="{{ action('UserController@register') }}" method="POST">
                    {{ csrf_field() }}
                    <h1>Sign Up</h1>
                    <label>
                        <input type="email" placeholder="Email" name="email" value="{{old('email')}}"/>
                        @if ($errors->has("email"))
                            <span class="help-block">
                                {{ $errors->first("email") }}
                            </span>
                        @endif
                    </label>
                    <label>
                        <input type="text" placeholder="First Name" name="first_name" value="{{old('first_name')}}"/>
                        @if ($errors->has("first_name"))
                            <span class="help-block">
                                {{ $errors->first("first_name") }}
                            </span>
                        @endif
                    </label>
                    <label>
                        <input type="text" placeholder="Last Name" name="last_name" value="{{old('last_name')}}"/>
                        @if ($errors->has("last_name"))
                            <span class="help-block">
                                {{ $errors->first("last_name") }}
                            </span>
                        @endif
                    </label>
                    <label>
                        <input type="password" placeholder="Password" name="password" />
                        @if ($errors->has("password"))
                            <span class="help-block">
                                {{ $errors->first("password") }}
                            </span>
                        @endif
                    </label>
                    <label>
                        <input type="password" placeholder="Confirm Password" name="confirm_password" />
                        @if ($errors->has("confirm_password"))
                        <span class="help-block">
                            {{ $errors->first("confirm_password") }}
                        </span>
                    @endif
                    </label>
                    <input type="hidden" name="plan_id" id="plan_id">
                    <input type="hidden" name="tier" id="plan_tier">
                    <input type="hidden" name="currency" id="currency">
                    <input type="hidden" name="sign" value="signForm">
                    <div class="rs-select2 js-select-simple select--no-search">
                        <select name="timezone">
                            <option value="">Select Time Zone</option>
                            @foreach (Tool::getTimezoneSelectOptions() as $timeZone)
                                <option value="{{$timeZone['value']}}" {{$timeZone['value'] == old('timezone') ? 'selected' : ''}}>{{htmlspecialchars($timeZone['text'])}}</option>
                            @endforeach
                        </select>
                        <div class="select-dropdown"></div>
                        @if ($errors->has("timezone"))
                            <span class="help-block">
                                {{ $errors->first("timezone") }}
                            </span>
                        @endif
                    </div>
                    <div class="rs-select2 js-select-simple select--no-search">
                        <select name="language_id">
                            <option value="">Select language</option>
                            @foreach (Acelle\Model\Language::getSelectOptions() as $language)
                                <option value="{{$language['value']}}" {{$language['value'] == old('language_id') ? 'selected' : ''}}>{{htmlspecialchars($language['text'])}}</option>
                            @endforeach
                        </select>
                        <div class="select-dropdown"></div>
                        @if ($errors->has("language_id"))
                            <span class="help-block">
                                {{ $errors->first("language_id") }}
                            </span>
                        @endif
                    </div>
                    {{-- <div class="term">
                        <p>"By clicking "Sign Up" button I agree to the SendImpactt's <a
                                href="https://alpha.yourwebsitedemos.com/web/Sendimpacttv1/terms-of-services/">Terms &
                                Conditions</a>, <a
                                href="https://alpha.yourwebsitedemos.com/web/Sendimpacttv1/terms-of-services/"
                                style="margin-top: -12px;">Privacy Policy</a> and <a
                                href="https://alpha.yourwebsitedemos.com/web/Sendimpacttv1/anti-spam-policy/"
                                style="margin-top: -12px;">Anti-Spam Policy.</a>"</p>
                    </div> --}}
                    <div class="term">
                    </div>

                    @if (Acelle\Model\Setting::get('registration_recaptcha') == 'yes')
                                <!-- hCaptcha -->
                        @if (\Acelle\Model\Setting::getCaptchaProvider() == 'hcaptcha')
                            @php
                                $hcaptcha = \Acelle\Hcaptcha\Client::initialize();
                            @endphp
                            {!! $hcaptcha->renderFormHtml($errors) !!}
                        @else
                            {!! Acelle\Library\Tool::showReCaptcha($errors) !!}
                        @endif
                    @endif


                    <button type="submit" style="margin-top: 9px" class="sign-up-btn">Sign Up</button>
                </form>
            </div>
            <div class="form-container sign-in-container mt-5 pt-5">
                <form class="" role="form" method="POST" action="{{ url('/login') }}">
                    @csrf
                    <img
                        src="{{ getSiteLogoUrl('light') }}">
                    <h1>Sign in</h1>
                    <label>
                        <input type="email" placeholder="Email" name="email" />
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </label>
                    <label>
                        <input type="password" placeholder="Password" name="password"/>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </label>
                    <a href="#">Forgot your password?</a>
                    @if (\Acelle\Model\Setting::get('login_recaptcha') == 'yes')
                        <!-- hCaptcha -->
                        @if (\Acelle\Model\Setting::getCaptchaProvider() == 'hcaptcha')
                            @php
                                $hcaptcha = \Acelle\Hcaptcha\Client::initialize();
                            @endphp
                            {!! $hcaptcha->renderFormHtml($errors) !!}
                        @else
                            {!! \Acelle\Library\Tool::showReCaptcha($errors) !!}
                        @endif
                    @endif
                    <button type="submit">Sign In</button>
                    <div class="social-container">
                        @if (
                            \Acelle\Model\Setting::get('oauth.google_enabled') == 'yes' ||
                            \Acelle\Model\Setting::get('oauth.facebook_enabled') == 'yes'
                        )
                            @if (\Acelle\Model\Setting::get('oauth.google_enabled') == 'yes')
                                <a href="{{ action('AuthController@googleRedirect', ['language_code' => language_code(),]) }}" target="_blank" class="social"><img src="{{ url('images/google-login.svg') }}">{{ trans('messages.continue_with_google') }}</a>
                            @endif
                            @if (\Acelle\Model\Setting::get('oauth.facebook_enabled') == 'yes')
                                <a href="{{ action('AuthController@facebookRedirect') }}" target="_blank" class="social"><img src="{{ url('images/icons/facebook-logo.svg') }}">{{ trans('messages.continue_with_facebook') }}</a>
                            @endif
                        @endif
                    </div>
                </form>
            </div>
            <div class="overlay-container">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <img src="{{ getSiteLogoUrl('light') }}"
                            width="300">
                        <ul>
                            <li>Email Marketing</li>
                            <li>Marketing Automation</li>
                            <li>Segmentation</li>
                            <li>Lead Generation</li>

                        </ul>
                        <button class="ghost mt-5" id="signIn">Sign In</button>
                    </div>

                    <div class="overlay-panel overlay-right">

                        <h1>Create, Account!</h1>
                        <p>Sign up if you still don't have an account ... </p>
                        <button class="ghost" id="signUp">Sign Up</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        @if (isSiteDemo())
            $('.res-button').click(function(e) {
                e.preventDefault();

                notify('notice', '{{ trans('messages.notify.notice') }}',
                    '{{ trans('messages.operation_not_allowed_in_demo') }}');
            });
        @endif
        $(document).ready(function() {
            if (localStorage.getItem("embad_code")) {
                $('#plan_id').val(localStorage.getItem('plan_id'));
                $('#plan_tier').val(localStorage.getItem('plan_tier'));
                $('#currency').val(localStorage.getItem('currency'));
            }
        });
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('container');
        signUpButton.addEventListener('click', () => container.classList.add('right-panel-active'));
        signInButton.addEventListener('click', () => container.classList.remove('right-panel-active'));

        // https://Github.com/YasinDehfuli
        //   https://Codepen.io/YasinDehfuli
        // Disigned By Nisay
    </script>
@endsection
