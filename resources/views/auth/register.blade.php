@extends('layouts.app')

@section('content')
    <div class="login-screen">
        <div class="bgc-screen">
            <img src="{{asset('/img/Reefer-Containers.jpg')}}">
        </div>

        <div class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-8 col-xs-4">
                        <div class="card">
                            <div class="card-header">
                                <img src="{{asset('/img/logo.png')}}" width="80">
                                <span class="text-dark">MSC PANAMA COUNTER TICKETING</span>
                            </div>
                            <form method="POST" action="{{ route('registro_transportista') }}" aria-label="{{ __('Register') }}">
                                @csrf
                                <div class="card-body pb-5">
                                    {{ __('registro_transportista.registro_transportista') }}

                                    <div class="form-group row mt-3">
                                        <label for="name"
                                               class="col-md-4 col-form-label text-md-right">{{ __('registro_transportista.name') }}</label>

                                        <div class="col-md-6">
                                            <input id="name" type="text"
                                                   class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                                   name="name" value="{{ old('name') }}" required autofocus>

                                            @if ($errors->has('name'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email"
                                               class="col-md-4 col-form-label text-md-right">{{ __('registro_transportista.email') }}</label>

                                        <div class="col-md-6">
                                            <input id="email" type="email"
                                                   class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                   name="email" value="{{ old('email') }}" required>

                                            @if ($errors->has('email'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <label for="name"
                                               class="col-md-4 col-form-label text-md-right">{{ __('registro_transportista.username') }}</label>

                                        <div class="col-md-6">
                                            <input id="username" type="text"
                                                   maxlength="20"
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
                                               class="col-md-4 col-form-label text-md-right">{{ __('registro_transportista.password') }}</label>

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

                                    <div class="form-group row">
                                        <label for="password-confirm"
                                               class="col-md-4 col-form-label text-md-right">{{ __('registro_transportista.confirm_password') }}</label>

                                        <div class="col-md-6">
                                            <input id="password-confirm" type="password" class="form-control"
                                                   name="password_confirmation" required>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 offset-md-4">

                                        </div>
                                    </div>
                                </div>

                                <div class="box-footer pb-5">
                                    <hr>
                                    <div class="text-center">
                                        <a class="btn btn-light f-size-20 mr-2" href="{{url("login")}}">
                                            Volver
                                        </a>

                                        <button type="submit" class="btn btn-primary f-size-20">
                                            Registrar
                                        </button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
