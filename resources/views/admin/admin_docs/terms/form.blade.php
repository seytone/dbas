@extends('layouts.admin')
@section('content')
	<div class="row mb-3">
		<div class="col-md-8"><h1>Nuevo Términos y Condiciones</h1></div>
		<div class="col-md-4 text-right">
			<a href="{{ route('admin.admin_docs.index', $type) }}" class="btn btn-secondary"><i class="fa fa-arrow-left mr-1"></i> Cancelar</a>
		</div>
	</div>

	@if($errors->any())
		<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
	@endif

	<form action="{{ route('admin.admin_docs.store', $type) }}" method="POST">
		@csrf
		<div class="card mb-3"><div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label><b>Empresa emisora *</b></label>
						<select name="company" class="form-control" required>
							@foreach(config('companies') as $code => $co)
								<option value="{{ $code }}" {{ old('company', 've') == $code ? 'selected' : '' }}>{{ $co['label'] }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>

			<h6 class="text-muted">DATOS DEL CLIENTE (se inyectan en el texto)</h6>
			<div class="row">
				<div class="col-md-6"><div class="form-group"><label>Razón Social / Nombre *</label><input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}" required maxlength="255"></div></div>
				<div class="col-md-6"><div class="form-group"><label>RIF o Cédula</label><input type="text" name="client_document" class="form-control" value="{{ old('client_document') }}" maxlength="50"></div></div>
				<div class="col-md-6"><div class="form-group"><label>Teléfono</label><input type="text" name="client_phone" class="form-control" value="{{ old('client_phone') }}" maxlength="50"></div></div>
				<div class="col-md-6"><div class="form-group"><label>Dirección</label><input type="text" name="client_address" class="form-control" value="{{ old('client_address') }}" maxlength="500"></div></div>
			</div>

			<h6 class="text-muted mt-2">DATOS DEL DOCUMENTO</h6>
			<div class="row">
				<div class="col-md-6"><div class="form-group"><label>Ref. Orden de Entrega</label><input type="text" name="delivery_order_ref" class="form-control" value="{{ old('delivery_order_ref') }}" maxlength="50" placeholder="ej. 637"></div></div>
				<div class="col-md-6"><div class="form-group"><label>Método de pago *</label><input type="text" name="payment_method" class="form-control" value="{{ old('payment_method', 'TRANSFERENCIA') }}" required maxlength="100"></div></div>
				<div class="col-md-6"><div class="form-group"><label>Ciudad de firma *</label><input type="text" name="sign_city" class="form-control" value="{{ old('sign_city') }}" required maxlength="100"></div></div>
				<div class="col-md-6"><div class="form-group"><label>Estado de firma *</label><input type="text" name="sign_state" class="form-control" value="{{ old('sign_state') }}" required maxlength="100"></div></div>
			</div>
		</div></div>
		<button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save mr-2"></i>Generar</button>
	</form>
@endsection
