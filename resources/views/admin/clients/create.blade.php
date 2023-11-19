@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            Creación de Cliente
        </div>
        <div class="card-body">
            <form action="{{ route('admin.clients.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
				<div class="row">
					{{-- <div class="col-sm-4">
						<div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
							<label for="code">Código&nbsp;<b class="text-danger">*</b></label>
							<input type="text" id="code" name="code" class="form-control" value="{{ old('code') }}" required>
							@if ($errors->has('code'))
								<em class="invalid-feedback">
									{{ $errors->first('code') }}
								</em>
							@endif
						</div>
					</div> --}}
					<div class="col-sm-6">
						<div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
							<label for="title">Razón Social&nbsp;<b class="text-danger">*</b></label>
							<input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required>
							@if ($errors->has('title'))
								<em class="invalid-feedback">
									{{ $errors->first('title') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group {{ $errors->has('document') ? 'has-error' : '' }}">
							<label for="document">Identificación&nbsp;<b class="text-danger">*</b></label>
							<input type="text" id="document" name="document" class="form-control" value="{{ old('document') }}" required>
							@if ($errors->has('document'))
								<em class="invalid-feedback">
									{{ $errors->first('document') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
							<label for="email">Email&nbsp;<b class="text-danger">*</b></label>
							<input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
							@if ($errors->has('email'))
								<em class="invalid-feedback">
									{{ $errors->first('email') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
							<label for="phone">Télefono&nbsp;<b class="text-danger">*</b></label>
							<input type="phone" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" required>
							@if ($errors->has('phone'))
								<em class="invalid-feedback">
									{{ $errors->first('phone') }}
								</em>
							@endif
						</div>
					</div>
				</div>
				<div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                    <label for="address">Dirección&nbsp;<b class="text-danger">*</b></label>
                    <input type="text" id="address" name="address" class="form-control" value="{{ old('address') }}" required>
                    @if ($errors->has('address'))
                        <em class="invalid-feedback">
                            {{ $errors->first('address') }}
                        </em>
                    @endif
                </div>
                <div class="text-center text-md-right mt-4">
					<hr>
                    <input class="btn btn-success" type="submit" value="Guardar">
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script>
        $(function() {
            $('.title').maxlength({
                threshold: 50
            });
            $('.resume').maxlength({
                threshold: 140
            });
        });
    </script>
@endsection
