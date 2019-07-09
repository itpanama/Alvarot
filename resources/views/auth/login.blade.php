@extends('layouts.app')

@section('content')
    <div class="login-screen">
        <div class="bgc-screen">
            <img src="{{asset('/img/Reefer-Containers.jpg')}}">
        </div>

        <div class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8 col-xs-4">
                        <div class="card">
                            <div class="card-header">
                                <img src="{{asset('/img/logo.png')}}" width="80">
                                <span class="text-dark">MSC PANAMA COUNTER TICKETING</span>
                            </div>

                            <div class="card-body pb-5">
                                {{ __('Login') }}

                                <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                                    @csrf

                                    <div class="form-group row">
                                        <label for="username"
                                               class="col-sm-4 col-form-label text-md-right">{{ __('Username') }}</label>

                                        <div class="col-md-6">
                                            <input id="username" type="text"
                                                   class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"
                                                   name="username" value="{{ old('username') }}" required autofocus>

                                            @if ($errors->has('username'))
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="password"
                                               class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                        <div class="col-md-6">
                                            <input id="password" type="password"
                                                   class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                                   name="password" required>

                                            @if ($errors->has('password'))
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="submit" class="btn btn-secondary w-100">
                                                {{ __('Login') }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0 mt-5">
                                        <div class="col-md-6 offset-md-4">
                                            <a href="{{url("registro-transportista")}}">
                                                Registrar transportista
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
