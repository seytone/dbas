@extends('layouts.admin')
@section('content')
	<div class="row mb-3">
		<div class="col-md-8"><h1>Nueva Orden de Entrega</h1></div>
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

			<h6 class="text-muted">DATOS DEL CLIENTE</h6>
			@include('admin.admin_docs._client_picker')
			<div class="row">
				<div class="col-md-6"><div class="form-group"><label>Nombre *</label><input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}" required maxlength="255"></div></div>
				<div class="col-md-6"><div class="form-group"><label>RIF / Documento</label><input type="text" name="client_document" class="form-control" value="{{ old('client_document') }}" maxlength="50"></div></div>
				<div class="col-md-6"><div class="form-group"><label>Teléfono</label><input type="text" name="client_phone" class="form-control" value="{{ old('client_phone') }}" maxlength="50"></div></div>
				<div class="col-md-6"><div class="form-group"><label>Dirección</label><input type="text" name="client_address" class="form-control" value="{{ old('client_address') }}" maxlength="500"></div></div>
			</div>

			<h6 class="text-muted mt-2">ITEMS</h6>
			<table class="table table-sm" id="items-table">
				<thead>
					<tr>
						<th style="width: 12%;">Cantidad</th>
						<th>Descripción</th>
						<th style="width: 25%;">Serial</th>
						<th style="width: 40px;"></th>
					</tr>
				</thead>
				<tbody id="items-body"></tbody>
			</table>
			<button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-item"><i class="fa fa-plus mr-1"></i> Añadir item</button>
		</div></div>

		<button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save mr-2"></i>Generar</button>
	</form>

<script>
	var idx = 0;
	function addItem(data) {
		data = data || {};
		var i = idx++;
		$('#items-body').append(
			'<tr>' +
			'<td><input type="number" step="0.01" min="0" name="items[' + i + '][quantity]" class="form-control form-control-sm text-right" value="' + (data.quantity || 1) + '" required></td>' +
			'<td><textarea name="items[' + i + '][description]" class="form-control form-control-sm" rows="2" required>' + (data.description || '') + '</textarea></td>' +
			'<td><input type="text" name="items[' + i + '][serial]" class="form-control form-control-sm" value="' + (data.serial || '') + '" maxlength="100"></td>' +
			'<td><button type="button" class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></button></td>' +
			'</tr>'
		);
	}
	$('#btn-add-item').on('click', function() { addItem(); });
	$('#items-body').on('click', '.btn-remove', function() { $(this).closest('tr').remove(); });
	@if(is_array(old('items')))
		@foreach(old('items') as $it) addItem(@json($it)); @endforeach
	@else
		addItem();
	@endif
</script>
@endsection
