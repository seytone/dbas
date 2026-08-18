@extends('layouts.admin')
@section('content')
	@php $editing = isset($document) && $document; @endphp
	<div class="row mb-3">
		<div class="col-md-8"><h1>{{ $editing ? 'Editar Orden de Entrega' : 'Nueva Orden de Entrega' }} @if($editing)<small class="text-muted">{{ $document->formatted_number }}</small>@endif</h1></div>
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
			@if(!$editing && isset($quotations) && $quotations->count())
				{{-- Importar desde cotización aprobada — trae cliente e items. --}}
				<div class="form-group">
					<label for="import_quotation"><b><i class="fa fa-file-import mr-1"></i> Importar desde cotización</b> <small class="text-muted">(opcional)</small></label>
					<select id="import_quotation" class="selectize-import-quotation">
						<option value="">Selecciona una cotización aprobada para autollenar…</option>
						@foreach($quotations as $q)
							@php
								$qPayload = json_encode([
									'client_name'     => $q->client_title    ?? '',
									'client_document' => $q->client_document ?? '',
									'client_phone'    => $q->client_phone    ?? '',
									'client_address'  => $q->client_address  ?? '',
									'items'           => $q->items->map(function ($it) {
										return [
											// Orden de Entrega no lleva precio ni código; solo cantidad
											// + descripción + serial (que se llena a mano).
											'quantity'    => (int) $it->quantity,
											'description' => trim(preg_replace('/\s+/', ' ', strip_tags((string) $it->description))),
										];
									})->values(),
								]);
							@endphp
							<option value="{{ $q->id }}" data-data='{{ $qPayload }}'>
								{{ $q->formatted_number }} — {{ $q->client_title ?? '' }} ({{ $q->emission_date->format('d/m/Y') }} · {{ ucfirst($q->status) }})
							</option>
						@endforeach
					</select>
					<small class="text-muted">Al seleccionar, se reemplazan los datos del cliente y los items con los de la cotización. El serial se llena manualmente.</small>
				</div>
				<hr>
			@endif

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
			@include('admin.admin_docs._product_picker')

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
			'<td><textarea name="items[' + i + '][description]" class="form-control form-control-sm" rows="2" required>' + (data.description || '') + '</textarea></td>' +
			'<td><input type="text" name="items[' + i + '][serial]" class="form-control form-control-sm" value="' + (data.serial || '') + '" maxlength="100"></td>' +
			'<td><button type="button" class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></button></td>' +
			'</tr>'
		);
	}

	function importFromQuotation(payload) {
		if (!payload) return;
		$('[name="client_name"]').val(payload.client_name || '');
		$('[name="client_document"]').val(payload.client_document || '');
		$('[name="client_phone"]').val(payload.client_phone || '');
		$('[name="client_address"]').val(payload.client_address || '');
		$('#items-body').empty();
		idx = 0;
		(payload.items || []).forEach(function(it) { addRow(it); });
	}

	$(function() {
		$('.selectize-import-quotation').selectize({
			persist: false,
			sortField: 'text',
			onChange: function(value) {
				if (!value) return;
				var payload = this.options[value] && this.options[value].data;
				if (payload) importFromQuotation(payload);
				this.clear(true);
			},
		});

		$('.selectize-products').selectize({
			persist: false,
			sortField: 'text',
			searchField: ['text', 'code', 'title'],
			onItemAdd: function(value) {
				var product = this.options[value].data;
				if (product) {
					addRow({
						quantity: 1,
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
