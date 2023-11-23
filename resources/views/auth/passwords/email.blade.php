@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-group">
            <div class="card p-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('password.email') }}">
                        {{ csrf_field() }}
                        <div class="text-center">
                            <img class="img-fluid" src="{{ asset('img/logo.png') }}" alt="Logo Distribuidora Bit" width="400">
                        </div>
                        <h4 class="text-center text-muted my-5">Recuperación de contraseña</h4>
                        <p class="text-muted">Escribe el email con el que accedes a la plataforma.</p>
                        <div>
                            {{ csrf_field() }}
                            <div class="form-group has-feedback">
                                <input type="email" name="email" class="form-control" required="autofocus" placeholder="Email">
                                @if($errors->has('email'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('email') }}
                                    </em>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-right">
                                <button type="submit" class="btn btn-custom btn-block btn-flat">
                                    Recuperar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection