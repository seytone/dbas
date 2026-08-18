@extends('layouts.admin')
@section('content')
	@php $editing = isset($document) && $document; @endphp
	<div class="row mb-3">
		<div class="col-md-8"><h1>{{ $editing ? 'Editar Invoice / Nota de Entrega' : 'Nuevo Invoice / Nota de Entrega' }} @if($editing)<small class="text-muted">{{ $document->formatted_number }}</small>@endif</h1></div>
		<div class="col-md-4 text-right">
			<a href="{{ $editing ? route('admin.admin_docs.show', [$type, $document->id]) : route('admin.admin_docs.index', $type) }}" class="btn btn-secondary"><i class="fa fa-arrow-left mr-1"></i> Cancelar</a>
		</div>
	</div>

	@if($errors->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
			</ul>
		</div>
	@endif

	<form action="{{ $editing ? route('admin.admin_docs.update', [$type, $document->id]) : route('admin.admin_docs.store', $type) }}" method="POST" id="doc-form">
		@csrf
		@if($editing) @method('PUT') @endif

		<div class="card mb-3">
			<div class="card-body">
				@if(!$editing && isset($quotations) && $quotations->count())
					{{-- Importar desde una cotización previamente aprobada.
					     Rellena cliente + items para no retipear la venta. --}}
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
											$factor = 1 + (($it->discount_percent ?? 0) / 100);
											return [
												'code'        => $it->code,
												// Descripción viene como HTML (contenteditable); la limpiamos para el textarea del invoice.
												'description' => trim(preg_replace('/\s+/', ' ', strip_tags((string) $it->description))),
												'quantity'    => (int) $it->quantity,
												'price'       => round((float) $it->unit_price * $factor, 2),
											];
										})->values(),
									]);
								@endphp
								{{-- Selectize parsea automáticamente el atributo data-data
								     como JSON y lo expone en option.data — usar cualquier
								     otro nombre pierde ese binding. --}}
								<option value="{{ $q->id }}" data-data='{{ $qPayload }}'>
									{{ $q->formatted_number }} — {{ $q->client_title ?? '' }} ({{ $q->emission_date->format('d/m/Y') }} · {{ ucfirst($q->status) }})
								</option>
							@endforeach
						</select>
						<small class="text-muted">Al seleccionar, se reemplazan los datos del cliente y todos los items con los de la cotización. Podés editarlos después.</small>
					</div>
					<hr>
				@endif

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="company"><b>Empresa emisora *</b></label>
							<select name="company" id="company" class="form-control" required>
								@foreach(config('companies') as $code => $co)
									<option value="{{ $code }}" {{ old('company', 've') == $code ? 'selected' : '' }}>{{ $co['label'] }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>

				<h6 class="text-muted mt-2">BILL TO / DATOS DEL CLIENTE</h6>
				@include('admin.admin_docs._client_picker')
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Nombre / Razón Social *</label>
							<input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}" required maxlength="255">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>RIF / Documento</label>
							<input type="text" name="client_document" class="form-control" value="{{ old('client_document') }}" maxlength="50">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Teléfono</label>
							<input type="text" name="client_phone" class="form-control" value="{{ old('client_phone') }}" maxlength="50">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Dirección de entrega (Ship To)</label>
							<input type="text" name="client_address" class="form-control" value="{{ old('client_address') }}" maxlength="500">
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label>Ship To alterno <small class="text-muted">(opcional — solo si la entrega va a una dirección distinta a la de arriba)</small></label>
							<textarea name="ship_address" class="form-control" rows="2" maxlength="500">{{ old('ship_address') }}</textarea>
						</div>
					</div>
				</div>

				<h6 class="text-muted mt-3">ITEMS</h6>
				@include('admin.admin_docs._product_picker')

				<table class="table table-sm" id="items-table">
					<thead>
						<tr>
							<th style="width: 15%;">Item / Código</th>
							<th>Descripción</th>
							<th style="width: 10%;">Cantidad</th>
							<th style="width: 15%;">Precio ($)</th>
							<th style="width: 15%;">Amount</th>
							<th style="width: 40px;"></th>
						</tr>
					</thead>
					<tbody id="items-body"></tbody>
				</table>

				<div class="text-right mt-3">
					<h4><b>Total: $<span id="grand-total">0,00</span></b></h4>
				</div>
			</div>
		</div>

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
		var row = '<tr>' +
			'<td><input type="text" name="items[' + i + '][code]" class="form-control form-control-sm code-lookup" value="' + (data.code || '') + '" maxlength="100" placeholder="SKU..."></td>' +
			'<td><textarea name="items[' + i + '][description]" class="form-control form-control-sm desc" rows="2" required>' + (data.description || '') + '</textarea></td>' +
			'<td><input type="number" step="0.01" min="0" name="items[' + i + '][quantity]" class="form-control form-control-sm text-right qty" value="' + (data.quantity != null ? data.quantity : 1) + '" required></td>' +
			'<td><input type="number" step="0.01" name="items[' + i + '][price]" class="form-control form-control-sm text-right price" value="' + (data.price != null ? data.price : 0) + '" required></td>' +
			'<td class="text-right amount align-middle">0,00</td>' +
			'<td><button type="button" class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></button></td>' +
			'</tr>';
		$('#items-body').append(row);
		recalc();
	}

	// Al salir del campo Código, si coincide con el SKU de un producto
	// registrado autollenar descripción y precio (solo si están vacíos o
	// en su default para no pisar edits manuales).
	function onCodeLookup() {
		var $input = $(this);
		var product = window.__lookupProductBySku($input.val());
		if (!product) return;
		var $row = $input.closest('tr');
		var $desc = $row.find('.desc');
		var $price = $row.find('.price');
		if (!$desc.val().trim()) {
			$desc.val(product.title + (product.description ? ' - ' + product.description : ''));
		}
		if (parseFloat($price.val()) === 0 || !$price.val()) {
			$price.val(product.price);
		}
		recalc();
	}

	function recalc() {
		var grand = 0;
		$('#items-body tr').each(function() {
			var q = parseFloat($(this).find('.qty').val()) || 0;
			var p = parseFloat($(this).find('.price').val()) || 0;
			var amt = q * p;
			grand += amt;
			$(this).find('.amount').text(amt.toFixed(2).replace('.', ','));
		});
		$('#grand-total').text(grand.toFixed(2).replace('.', ','));
	}

	function importFromQuotation(payload) {
		if (!payload) return;

		// Reemplazar datos del cliente.
		$('[name="client_name"]').val(payload.client_name || '');
		$('[name="client_document"]').val(payload.client_document || '');
		$('[name="client_phone"]').val(payload.client_phone || '');
		$('[name="client_address"]').val(payload.client_address || '');

		// Reemplazar items completos.
		$('#items-body').empty();
		idx = 0;
		(payload.items || []).forEach(function(it) { addRow(it); });
	}

	$(function() {
		// Selector de import (solo modo create). Selectize parsea data-data
		// como JSON y lo expone en options[value].data — mismo patrón que
		// usa el picker de productos.
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
			// Buscar por SKU además del texto visible (option.data viene
			// de data-data que Selectize parsea como JSON).
			searchField: ['text', 'code', 'title'],
			onItemAdd: function(value) {
				var product = this.options[value].data;
				if (product) {
					addRow({
						code: product.code,
						description: product.title + (product.description ? ' - ' + product.description : ''),
						quantity: 1,
						price: product.price,
					});
				}
				this.clear(true);
			},
		});

		$('#btn-add-free').on('click', function() { addRow(); });
		$('#items-body').on('input', '.qty, .price', recalc);
		$('#items-body').on('blur change', '.code-lookup', onCodeLookup);
		$('#items-body').on('click', '.btn-remove', function() { $(this).closest('tr').remove(); recalc(); });

		@if(is_array(old('items')))
			@foreach(old('items') as $it)
				addRow(@json($it));
			@endforeach
		@endif
	});
})();
</script>
@endsection
