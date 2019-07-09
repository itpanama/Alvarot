@extends('layouts.app-transportista')

@section('content')
    <?php $contador = 1; ?>
    <div class="login-screen">
        <div class="bgc-screen">
            <img src="{{asset('/img/Reefer-Containers.jpg')}}">
        </div>

        <div class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-md-8 col-xs-4">
                        <div class="card mt-0">
                            <div class="card-header">
                                <img src="{{asset('/img/logo.png')}}" width="80">
                                <span class="text-dark">MSC PANAMA COUNTER TICKETING</span>
                            </div>

                            <form id="formulario-solicitud-transportista" method="POST" action="{{ route('solicitud-transportista') }}"
                                  aria-label="{{ __('transportista.register_transporters') }}"
                                  enctype="multipart/form-data" novalidate>
                                @csrf

                                <div class="card-body pb-5">
                                    <div class="clearfix"></div>
                                    @if (count($errors))
                                        <div class="alert alert-danger" role="alert">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if (Session::has('message'))
                                        <div class="alert alert-success">{{ Session::get('message') }}</div>
                                    @endif

                                    @if (Session::has('errmessage'))
                                        <div class="alert alert-danger">{{ Session::get('errmessage') }}</div>
                                    @endif

                                    {{ __('transportista.register_transporters') }}


                                    <fieldset>
                                        <legend>Datos del transportista</legend>

                                        <div class="row">
                                            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                                                <div class="form-group">
                                                    <label>
                                                        <?php echo $contador;$contador++; ?>
                                                        . {{ __('transportista.company_name_operation') }}
                                                        <span class="text-danger">(*)</span>
                                                    </label>
                                                    <input type="text" class="form-control"
                                                           name="company_name_operation" id="company_name_operation"
                                                           placeholder="" value="{{ old('company_name_operation') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                                                <div class="form-group">
                                                    <label>
                                                        <?php echo $contador;$contador++; ?>
                                                        . {{ __('transportista.address_company') }}
                                                        <span class="text-danger">(*)</span>
                                                    </label>
                                                    <input type="text" class="form-control" name="address_company"
                                                           id="address_company"
                                                           placeholder="" value="{{ old('address_company') }}">
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                                                <div class="form-group">
                                                    <label>
                                                        <?php echo $contador;$contador++; ?>
                                                        . {{ __('transportista.number_policy') }}
                                                        <span class="text-danger">(*)</span>
                                                    </label>
                                                    <input type="text" class="form-control" name="number_policy"
                                                           id="number_policy"
                                                           placeholder="" value="{{ old('number_policy') }}">
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                                                <div class="form-group">
                                                    <label>
                                                        <?php echo $contador;$contador++; ?>
                                                        . {{ __('transportista.expiration_date') }}
                                                        <span class="text-danger">(*)</span>
                                                    </label>
                                                    <input type="text" class="form-control" name="expiration_date"
                                                           id="expiration_date"
                                                           placeholder="Día/Mes/Año"
                                                           value="{{ old('expiration_date') }}">
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                                                <div class="form-group">
                                                    <label>
                                                        <?php echo $contador;$contador++; ?>
                                                        . {{ __('transportista.email') }}
                                                        <span class="text-danger">(*)</span>
                                                    </label>
                                                    <input type="email" class="form-control" name="email" id="email"
                                                           placeholder="" value="{{ old('email') }}">
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                                                <div class="form-group">
                                                    <label>
                                                        <?php echo $contador;$contador++; ?>
                                                        . {{ __('transportista.email_2') }}
                                                    </label>
                                                    <input type="email" class="form-control" name="email_2" id="email_2"
                                                           placeholder="Opcional" value="{{ old('email_2') }}">
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                                                <div class="form-group">
                                                    <label>
                                                        <?php echo $contador;$contador++; ?>
                                                        . {{ __('transportista.phone') }}
                                                        <span class="text-danger">(*)</span>
                                                    </label>
                                                    <input type="text" class="form-control" name="phone" id="phone"
                                                           placeholder="" value="{{ old('phone') }}">
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                                                <div class="form-group">
                                                    <label>
                                                        <?php echo $contador;$contador++; ?>
                                                        . {{ __('transportista.phone_2') }}
                                                    </label>
                                                    <input type="text" class="form-control" name="phone_2" id="phone_2"
                                                           placeholder="Opcional" value="{{ old('phone_2') }}">
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                                                <div class="form-group">
                                                    <label>
                                                        <?php echo $contador;$contador++; ?>
                                                        . {{ __('transportista.contact_name') }}
                                                        <span class="text-danger">(*)</span>
                                                    </label>
                                                    <input type="text" class="form-control" name="contact_name"
                                                           id="contact_name"
                                                           placeholder="" value="{{ old('contact_name') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="clearfix">

                                        <fieldset>
                                            <legend>
                                                Adjuntar Documentos
                                            </legend>
                                            @foreach($documentTypes as $td)
                                                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                                                    <div class="form-group">
                                                        <label>
                                                            {{$contador }}. {{ $td->description }}
                                                            <span class="text-danger">(*)</span>
                                                        </label>
                                                        <div>
                                                            <input
                                                                    type="file"
                                                                    name="documento_{{ $td->id }}"
                                                                    accept="
                                                                    .pdf,.docx,.xlsx,.doc,.jpg,.jpeg,.gif,.bmp">
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $contador++; ?>
                                            @endforeach
                                        </fieldset>

                                        <div class="col-lg-12 mt-3 mb-3">
                                            <p class="text-justify">
                                                - Tamaño maximo por documento {{$maxSizeDocumentHuman}}.
                                            </p>
                                        </div>
                                        <div class="col-lg-12 mt-3 mb-3">
                                            <p class="text-justify">
                                                - Formatos de documentos validos: {{$documentMimeType}}
                                            </p>
                                        </div>
                                        <div class="col-lg-12 mt-3 mb-3">
                                            <p class="text-justify">
                                                <span class="text-danger">(*)</span>: Campos obligatorios
                                            </p>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="box-footer pb-5">
                                    <hr>
                                    <div class="text-center">
                                        <a class="btn btn-light f-size-20 mr-2" href="{{url("login")}}">
                                            Volver
                                        </a>
                                        <button type="submit" class="btn btn-primary f-size-20">Enviar solicitud
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
