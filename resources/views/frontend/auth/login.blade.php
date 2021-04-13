@extends('frontend.layouts.app')

@section('title', app_name() . ' | ' . __('navs.general.home'))

@section('content')

    @if(Auth::check())

        <section class="login-block">

            <div class="text-center mobileLogo">
                <img style="width:52%;height:100%" src="{{URL::asset('/img/frontend/logo.png')}}"/>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-md-4 login-sec">
                        <h2 class="text-center">Current Login: {{ Auth::user()->name }}</h2>
                        <div class="btn-group-vertical" role="group" aria-label="Basic example">
                            <a href="{{ route('admin.dashboard') }}" style="text-decoration:none;">
                                <button type="button" class="btn btn-info">Administration Panel</button>
                            </a>
                            <br/>
                            <a href="{{ route('frontend.auth.logout') }}" style="text-decoration:none;">
                                <button type="button" class="btn btn-danger">Logout</button>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-8 banner-sec visuallyhidden">
                        <img style="width:72%;display: block;margin-left: auto;margin-right: auto;margin-top: 190px;"
                             src="{{URL::asset('/img/frontend/logo.png')}}"/>
                    </div>
                </div>
            </div>
        </section>

    @else

        <section class="login-block">

            <div class="text-center mobileLogo">
                <img style="width:52%;height:100%" src="{{URL::asset('/img/frontend/logo.png')}}"/>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-md-4 login-sec">
                        <h2 class="text-center">Login Now</h2>
                        @include('includes.partials.messages')
                        {{ html()->form('POST', route('frontend.auth.login.post'))->open() }}
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="text-uppercase">Email</label>
                            {{--<input type="text" class="form-control" placeholder="">--}}
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-at" aria-hidden="true"></i>
                                    </div>
                                </div>
                                {{ html()->email('email')
                                        ->class('form-control')
                                        ->placeholder(__('validation.attributes.frontend.email'))
                                        ->attribute('maxlength', 191)
                                        ->required() }}
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1" class="text-uppercase">Password</label>

                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-key" aria-hidden="true"></i>
                                    </div>
                                </div>
                                {{ html()->password('password')
                                         ->class('form-control')
                                         ->placeholder(__('validation.attributes.frontend.password'))
                                         ->required() }}
                            </div>
                        </div>


                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input">
                                <small>Remember Me</small>
                            </label>
                            <button type="submit" class="btn btn-login float-right">Login</button>
                        </div>

                        {{ html()->form()->close() }}
                    </div>

                    <div class="col-md-8 banner-sec visuallyhidden">
                        <img style="width:42%;display: block;margin-left: auto;margin-right: auto;margin-top: 190px;"
                             src="{{URL::asset('/img/frontend/logo.png')}}"/>
                    </div>
                </div>
            </div>
        </section>


    @endif

@endsection
