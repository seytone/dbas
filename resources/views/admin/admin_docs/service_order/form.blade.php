@extends('layouts.admin')
@section('content')
	@php $editing = isset($document) && $document; @endphp
	<div class="row mb-3">
		<div class="col-md-8"><h1>{{ $editing ? 'Editar Orden de Servicio' : 'Nueva Orden de Servicio' }} @if($editing)<small class="text-muted">{{ $document->formatted_number }}</small>@endif</h1></div>
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
						<label>Producto / Servicio *</label>
						<input type="text" name="product" class="form-control" value="{{ old('product') }}" required maxlength="255" placeholder="ej. Instalación de memoria en servidor">
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						@php
							// Valor compuesto "tipo:id" para poder mezclar dos
							// orígenes distintos en un mismo selector.
							$selectedRef = old('reference_kind') && old('reference_id')
								? old('reference_kind') . ':' . old('reference_id')
								: '';
						@endphp
						<label><b>Nota / Invoice</b> <small class="text-muted">(opcional — Nota de Entrega o Cotización de origen)</small></label>
						<select id="doc-reference" class="selectize-reference">
							<option value="">Sin referencia…</option>
							@if(isset($invoices) && $invoices->count())
								<optgroup label="Notas de Entrega / Invoice">
									@foreach($invoices as $inv)
										@php
											$refPayload = json_encode([
												'kind'            => 'invoice',
												'id'              => $inv->id,
												'number'          => $inv->formatted_number,
												'client_name'     => $inv->data['client_name']     ?? '',
												'client_document' => $inv->data['client_document'] ?? '',
												'client_phone'    => $inv->data['client_phone']    ?? '',
												'client_address'  => $inv->data['client_address']  ?? '',
												// La Orden de Servicio solo tiene cantidad +
												// descripción: el código y el precio del invoice
												// no tienen dónde ir y se descartan.
												'items'           => collect($inv->data['items'] ?? [])->map(fn ($it) => [
													'quantity'    => (float) ($it['quantity'] ?? 1),
													'description' => $it['description'] ?? '',
												])->values(),
											]);
										@endphp
										<option value="invoice:{{ $inv->id }}" data-data='{{ $refPayload }}' {{ $selectedRef === 'invoice:' . $inv->id ? 'selected' : '' }}>
											{{ $inv->formatted_number }} — {{ $inv->data['client_name'] ?? '' }} ({{ $inv->created_at->format('d/m/Y') }})
										</option>
									@endforeach
								</optgroup>
							@endif
							@if(isset($quotations) && $quotations->count())
								<optgroup label="Cotizaciones">
									@foreach($quotations as $q)
										@php
											$refPayload = json_encode([
												'kind'            => 'quotation',
												'id'              => $q->id,
												'number'          => 'COT-' . $q->formatted_number,
												// La cotización sí guarda a qué cliente registrado
												// apunta, así que el picker se puede marcar directo.
												// Las Notas de Entrega no lo guardan y se resuelven
												// por coincidencia de RIF/nombre.
												'client_id'       => $q->client_id,
												'client_name'     => $q->client_title    ?? '',
												'client_document' => $q->client_document ?? '',
												'client_phone'    => $q->client_phone    ?? '',
												'client_address'  => $q->client_address  ?? '',
												'items'           => $q->items->map(fn ($it) => [
													'quantity' => (float) $it->quantity,
													// La descripción de la cotización es HTML
													// editable; se aplana para el textarea.
													'description' => trim(preg_replace('/\s+/', ' ', strip_tags((string) $it->description))),
												])->values(),
											]);
										@endphp
										<option value="quotation:{{ $q->id }}" data-data='{{ $refPayload }}' {{ $selectedRef === 'quotation:' . $q->id ? 'selected' : '' }}>
											COT-{{ $q->formatted_number }} — {{ $q->client_title ?? '' }} ({{ $q->emission_date->format('d/m/Y') }})
										</option>
									@endforeach
								</optgroup>
							@endif
						</select>
						<input type="hidden" name="reference_kind" value="{{ old('reference_kind') }}">
						<input type="hidden" name="reference_id" value="{{ old('reference_id') }}">
						<input type="hidden" name="reference_number" value="{{ old('reference_number') }}">
						<small class="text-muted">Al seleccionar se cargan automáticamente el cliente y los items de «Para agregar al producto». Podés editarlos después. Si lo dejás vacío, se llena todo a mano.</small>
					</div>
				</div>
				@if($editing && old('note'))
					{{-- Órdenes creadas antes de que NOTA pasara a ser un selector:
					     el texto libre viaja oculto para no perderse al guardar. --}}
					<input type="hidden" name="note" value="{{ old('note') }}">
				@endif
			</div>

			<h6 class="text-muted">DATOS DEL CLIENTE</h6>
			@include('admin.admin_docs._client_picker')
			<div class="row">
				<div class="col-md-6"><div class="form-group"><label>Cliente *</label><input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}" required maxlength="255"></div></div>
				<div class="col-md-6"><div class="form-group"><label>RIF / Documento</label><input type="text" name="client_document" class="form-control" value="{{ old('client_document') }}" maxlength="50"></div></div>
				<div class="col-md-6"><div class="form-group"><label>Teléfono</label><input type="text" name="client_phone" class="form-control" value="{{ old('client_phone') }}" maxlength="50"></div></div>
				<div class="col-md-6"><div class="form-group"><label>Dirección</label><input type="text" name="client_address" class="form-control" value="{{ old('client_address') }}" maxlength="500"></div></div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Solicitado por *</label>
						<select name="requested_by" class="selectize-person" required>
							<option value="">Selecciona o escribe…</option>
							@foreach($sellers as $s)
								@php $name = $s->user->getFullname(); @endphp
								<option value="{{ $name }}" {{ old('requested_by') === $name ? 'selected' : '' }}>{{ $name }}</option>
							@endforeach
							@if(old('requested_by') && !$sellers->map(fn($s) => $s->user->getFullname())->contains(old('requested_by')))
								<option value="{{ old('requested_by') }}" selected>{{ old('requested_by') }}</option>
							@endif
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Elaborado por *</label>
						<select name="prepared_by" class="selectize-person" required>
							<option value="">Selecciona o escribe…</option>
							@foreach($sellers as $s)
								@php $name = $s->user->getFullname(); @endphp
								<option value="{{ $name }}" {{ old('prepared_by') === $name ? 'selected' : '' }}>{{ $name }}</option>
							@endforeach
							@if(old('prepared_by') && !$sellers->map(fn($s) => $s->user->getFullname())->contains(old('prepared_by')))
								<option value="{{ old('prepared_by') }}" selected>{{ old('prepared_by') }}</option>
							@endif
						</select>
					</div>
				</div>
			</div>

			<hr>

			<h6 class="text-muted">PARA AGREGAR AL PRODUCTO</h6>
			@include('admin.admin_docs._product_picker', ['scope' => '-prod'])
			<table class="table table-sm">
				<thead>
					<tr>
						<th style="width: 12%;">Cantidad</th>
						<th>Descripción</th>
						<th style="width: 40px;"></th>
					</tr>
				</thead>
				<tbody id="product-items-body"></tbody>
			</table>

			<hr>

			<h6 class="text-muted">PARA INCLUIR EN EL INVENTARIO</h6>
			@include('admin.admin_docs._product_picker', ['scope' => '-inv'])
			<table class="table table-sm">
				<thead>
					<tr>
						<th style="width: 12%;">Cantidad</th>
						<th>Descripción</th>
						<th style="width: 40px;"></th>
					</tr>
				</thead>
				<tbody id="inventory-items-body"></tbody>
			</table>

			<hr>

			<div class="form-group">
				<label><b>Configuración final</b> <small class="text-muted">(texto libre que se imprime en el recuadro inferior)</small></label>
				<textarea name="final_config" class="form-control" rows="4" maxlength="2000">{{ old('final_config') }}</textarea>
			</div>
		</div></div>

		<button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save mr-2"></i>{{ $editing ? 'Guardar cambios' : 'Generar' }}</button>
	</form>
@endsection

@section('scripts')
<script>
(function() {
	// Las dos tablas comparten estructura (cantidad + descripción), así que
	// una sola factory las maneja. El scope separa los IDs del picker.
	function makeItemsTable(opts) {
		var idx = 0;

		function addRow(data) {
			data = data || {};
			var i = idx++;
			$(opts.body).append(
				'<tr>' +
				'<td><input type="number" step="0.01" min="0" name="' + opts.name + '[' + i + '][quantity]" class="form-control form-control-sm text-right" value="' + (data.quantity != null ? data.quantity : 1) + '" required></td>' +
				'<td><textarea name="' + opts.name + '[' + i + '][description]" class="form-control form-control-sm" rows="2" required>' + (data.description || '') + '</textarea></td>' +
				'<td><button type="button" class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></button></td>' +
				'</tr>'
			);
		}

		$(opts.selector).selectize({
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

		$(opts.addFreeBtn).on('click', function() { addRow(); });
		$(opts.body).on('click', '.btn-remove', function() { $(this).closest('tr').remove(); });

		// reset() hace falta para que al elegir una referencia se reemplace
		// la tabla completa en vez de acumular filas sobre las existentes.
		return {
			add: addRow,
			reset: function() { $(opts.body).empty(); idx = 0; },
		};
	}

	$(function() {
		// Solicitado / Elaborado por: dropdown que también acepta texto libre.
		$('.selectize-person').selectize({
			create: true,
			persist: false,
			sortField: 'text',
			createOnBlur: true,
		});

		var productTable = makeItemsTable({
			body: '#product-items-body',
			name: 'product_items',
			selector: '.selectize-products-prod',
			addFreeBtn: '#btn-add-free-prod',
		});

		var inventoryTable = makeItemsTable({
			body: '#inventory-items-body',
			name: 'inventory_items',
			selector: '.selectize-products-inv',
			addFreeBtn: '#btn-add-free-inv',
		});

		// Referencia (Nota de Entrega o Cotización). A diferencia del import
		// del Invoice, acá la selección NO se limpia: queda visible porque es
		// un dato que se guarda y se imprime en el encabezado del PDF.
		$('.selectize-reference').selectize({
			persist: false,
			onChange: function(value) {
				var payload = value && this.options[value] ? this.options[value].data : null;

				$('[name="reference_kind"]').val(payload ? payload.kind : '');
				$('[name="reference_id"]').val(payload ? payload.id : '');
				$('[name="reference_number"]').val(payload ? payload.number : '');

				// Al quitar la referencia solo se suelta el vínculo; lo ya
				// capturado a mano no se borra.
				if (!payload) return;

				// Marcar el cliente en su propio picker. Se hace en silencio
				// (segundo argumento de setValue) para que no dispare su
				// onChange: los datos que valen son los del documento de
				// origen, no los de la ficha del cliente, que pudo cambiar
				// desde entonces.
				var picker = $('#client_picker')[0] && $('#client_picker')[0].selectize;
				if (picker) {
					var clientId = payload.client_id
						|| (window.__findClientId && window.__findClientId(payload.client_name, payload.client_document));
					if (clientId && picker.options[clientId]) {
						picker.setValue(clientId, true);
					} else {
						// Cliente no registrado (o borrado): el picker queda
						// limpio y los campos se llenan igual, a mano.
						picker.clear(true);
					}
				}

				$('[name="client_name"]').val(payload.client_name || '');
				$('[name="client_document"]').val(payload.client_document || '');
				$('[name="client_phone"]').val(payload.client_phone || '');
				$('[name="client_address"]').val(payload.client_address || '');

				// Los items van a «Para agregar al producto»; el inventario se
				// llena a mano porque el origen no distingue entre ambos.
				productTable.reset();
				(payload.items || []).forEach(function(it) { productTable.add(it); });
			},
		});

		@if(is_array(old('product_items')))
			@foreach(old('product_items') as $it) productTable.add(@json($it)); @endforeach
		@endif
		@if(is_array(old('inventory_items')))
			@foreach(old('inventory_items') as $it) inventoryTable.add(@json($it)); @endforeach
		@endif
	});
})();
</script>
@endsection
