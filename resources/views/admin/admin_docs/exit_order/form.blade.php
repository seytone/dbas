@extends('layouts.admin')
@section('content')
	@php $editing = isset($document) && $document; @endphp
	<div class="row mb-3">
		<div class="col-md-8"><h1>{{ $editing ? 'Editar Orden de Salida' : 'Nueva Orden de Salida' }} @if($editing)<small class="text-muted">{{ $document->formatted_number }}</small>@endif</h1></div>
		<div class="col-md-4 text-right">
			<a href="{{ $editing ? route('admin.admin_docs.show', [$type, $document->id]) : route('admin.admin_docs.index', $type) }}" class="btn btn-secondary"><i class="fa fa-arrow-left mr-1"></i> Cancelar</a>
		</div>
	</div>

	@if($errors->any())
		<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
	@endif

	<form action="{{ $editing ? route('admin.admin_docs.update', [$type, $document->id]) : route('admin.admin_docs.store', $type) }}" method="POST">
		@csrf
		@if($editing) @method('PUT') @endif
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
				<div class="col-md-6">
					<div class="form-group">
						<label><b>Nro. de Nota / Factura</b></label>
						<input type="text" name="invoice_ref" class="form-control" value="{{ old('invoice_ref') }}" maxlength="50">
					</div>
				</div>
			</div>

			<h6 class="text-muted">DATOS DEL CLIENTE Y VENDEDOR</h6>
			@include('admin.admin_docs._client_picker')
			<div class="row">
				<div class="col-md-6"><div class="form-group"><label>Cliente *</label><input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}" required maxlength="255"></div></div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Vendedor Responsable *</label>
						<select name="seller_name" class="form-control" required>
							<option value="">Selecciona el vendedor…</option>
							@foreach($sellers as $s)
								@php $sellerFullname = $s->user->getFullname(); @endphp
								<option value="{{ $sellerFullname }}" {{ old('seller_name') === $sellerFullname ? 'selected' : '' }}>{{ $sellerFullname }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-md-6"><div class="form-group"><label>Documento del cliente</label><input type="text" name="client_document" class="form-control" value="{{ old('client_document') }}" maxlength="50"></div></div>
				<div class="col-md-6"><div class="form-group"><label>Teléfono del cliente</label><input type="text" name="client_phone" class="form-control" value="{{ old('client_phone') }}" maxlength="50"></div></div>
				<div class="col-md-12"><div class="form-group"><label>Dirección</label><input type="text" name="client_address" class="form-control" value="{{ old('client_address') }}" maxlength="500"></div></div>
			</div>

			<h6 class="text-muted mt-2">MERCANCÍA</h6>
			@include('admin.admin_docs._product_picker')

			<table class="table table-sm" id="items-table">
				<thead>
					<tr>
						<th style="width: 12%;">Cantidad</th>
						<th style="width: 20%;">SKU</th>
						<th>Descripción</th>
						<th style="width: 40px;"></th>
					</tr>
				</thead>
				<tbody id="items-body"></tbody>
			</table>

			<div class="form-group mt-3">
				<label>Observaciones (mercancía retirada después de imprimir)</label>
				<textarea name="observations" class="form-control" rows="2" maxlength="1000">{{ old('observations') }}</textarea>
			</div>
		</div></div>
		<button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save mr-2"></i>{{ $editing ? 'Guardar cambios' : 'Generar' }}</button>
	</form>
@endsection

@section('scripts')
<script>
(function() {
	var idx = 0;

	function addRow(data) {
		data = data || {};
		var i = idx++;
		$('#items-body').append(
			'<tr>' +
			'<td><input type="number" step="0.01" min="0" name="items[' + i + '][quantity]" class="form-control form-control-sm text-right" value="' + (data.quantity != null ? data.quantity : 1) + '" required></td>' +
			'<td><input type="text" name="items[' + i + '][sku]" class="form-control form-control-sm" value="' + (data.sku || '') + '" maxlength="100"></td>' +
			'<td><textarea name="items[' + i + '][description]" class="form-control form-control-sm" rows="2" required>' + (data.description || '') + '</textarea></td>' +
			'<td><button type="button" class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></button></td>' +
			'</tr>'
		);
	}

	$(function() {
		$('.selectize-products').selectize({
			persist: false,
			sortField: 'text',
			onItemAdd: function(value) {
				var product = this.options[value].data;
				if (product) {
					addRow({
						quantity: 1,
						sku: product.code,
						description: product.title + (product.description ? ' - ' + product.description : ''),
					});
				}
				this.clear(true);
			},
		});

		$('#btn-add-free').on('click', function() { addRow(); });
		$('#items-body').on('click', '.btn-remove', function() { $(this).closest('tr').remove(); });

		@if(is_array(old('items')))
			@foreach(old('items') as $it) addRow(@json($it)); @endforeach
		@endif
	});
})();
</script>
@endsection
