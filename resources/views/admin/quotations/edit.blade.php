@extends('layouts.admin')
@section('content')
<style>
	.description-editor {
		min-height: 50px;
		max-height: 250px;
		overflow-y: auto;
		border: 1px solid #ced4da;
		border-radius: 4px;
		padding: 6px 8px;
		background: white;
		font-size: 13px;
		line-height: 1.4;
	}
	.description-editor:focus {
		outline: none;
		border-color: #4a9a5c;
		box-shadow: 0 0 0 2px rgba(74, 154, 92, 0.15);
	}
	.description-editor img {
		max-width: 180px !important;
		max-height: 180px !important;
		width: auto !important;
		height: auto !important;
		object-fit: contain;
		margin: 4px 4px 4px 0;
		border-radius: 3px;
		display: inline-block !important;
		vertical-align: top;
	}
	.description-editor:empty:before {
		content: attr(data-placeholder);
		color: #999;
		pointer-events: none;
	}
</style>
<div class="card">
	<div class="card-header">
		<b>Editar Cotización #{{ $quotation->formatted_number }}</b>
	</div>
	<div class="card-body">
		<form action="{{ route('admin.quotations.update', $quotation->id) }}" method="POST" id="quotation-form">
			@csrf
			@method('PUT')

			{{-- DATOS GENERALES --}}
			<div class="row">
				<div class="col-md-6">
					<h5 class="mb-3"><i class="fa fa-building mr-2"></i>Datos de la Cotización</h5>
					<div class="form-group">
						<label for="quotation_number"><b>Nro. Presupuesto</b></label>
						<input type="text" name="quotation_number" id="quotation_number" class="form-control" value="{{ $quotation->quotation_number }}" readonly>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="emission_date"><b>Fecha de Emisión *</b></label>
								<input type="date" name="emission_date" id="emission_date" class="form-control" value="{{ $quotation->emission_date->format('Y-m-d') }}" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="expiration_date"><b>Fecha de Vencimiento *</b></label>
								<input type="date" name="expiration_date" id="expiration_date" class="form-control" value="{{ $quotation->expiration_date->format('Y-m-d') }}" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="currency"><b>Moneda</b></label>
								<select name="currency" id="currency" class="form-control">
									<option value="USD" {{ $quotation->currency == 'USD' ? 'selected' : '' }}>Dólar (USD)</option>
									<option value="BS" {{ $quotation->currency == 'BS' ? 'selected' : '' }}>Bolívares (BS)</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="status"><b>Estado</b></label>
								<select name="status" id="status" class="form-control">
									<option value="draft" {{ $quotation->status == 'draft' ? 'selected' : '' }}>Borrador</option>
									<option value="sent" {{ $quotation->status == 'sent' ? 'selected' : '' }}>Enviada</option>
									<option value="accepted" {{ $quotation->status == 'accepted' ? 'selected' : '' }}>Aceptada</option>
									<option value="rejected" {{ $quotation->status == 'rejected' ? 'selected' : '' }}>Rechazada</option>
								</select>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group" id="status-comment-group" style="display: none;">
								<label for="status_comment"><b>Comentario sobre el estado</b> <small class="text-muted">(opcional)</small></label>
								<textarea name="status_comment" id="status_comment" class="form-control" rows="2" maxlength="1000" placeholder="Ej: Aceptada por el cliente el 25/05/2026 vía email, rechazada por precio, etc.">{{ $quotation->status_comment }}</textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<h5 class="mb-3"><i class="fa fa-user mr-2"></i>Datos del Cliente</h5>
					<div class="form-group">
						<label for="client_id"><b>Cliente</b></label>
						<div class="d-flex">
							<div class="flex-grow-1 mr-2">
								<select name="client_id" id="client_id" class="selectize-client">
									<option value="">Seleccione un cliente existente...</option>
									@foreach ($clients as $client)
										<option value="{{ $client->id }}" {{ $quotation->client_id == $client->id ? 'selected' : '' }}>
											{{ $client->getIdentification() }}
										</option>
									@endforeach
								</select>
							</div>
							<button type="button" class="btn btn-outline-secondary" id="btn-new-client" title="Limpiar para nuevo cliente">
								<i class="fa fa-user-plus"></i>
							</button>
						</div>
						<small class="text-muted">Modifica los datos para actualizar el cliente, o limpia el selector para crear uno nuevo.</small>
					</div>
					<div class="form-group">
						<label for="cli_title"><b>Razón Social *</b></label>
						<input type="text" name="cli_title" id="cli_title" class="form-control" required maxlength="100" value="{{ $quotation->client->title ?? '' }}">
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="cli_document"><b>RIF *</b></label>
								<input type="text" name="cli_document" id="cli_document" class="form-control" required maxlength="20" value="{{ $quotation->client->document ?? '' }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="cli_email">Email</label>
								<input type="email" name="cli_email" id="cli_email" class="form-control" maxlength="100" value="{{ $quotation->client->email ?? '' }}">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="cli_phone">Teléfono</label>
						<input type="text" name="cli_phone" id="cli_phone" class="form-control" maxlength="30" value="{{ $quotation->client->phone ?? '' }}">
					</div>
					<div class="form-group">
						<label for="cli_address">Dirección</label>
						<textarea name="cli_address" id="cli_address" class="form-control" rows="2" maxlength="500">{{ $quotation->client->address ?? '' }}</textarea>
					</div>
				</div>
			</div>

			<hr>

			{{-- PRODUCTOS --}}
			<h5 class="mb-3"><i class="fa fa-list mr-2"></i>Productos</h5>

			<div class="row mb-3">
				<div class="col-md-8">
					<select id="product-selector" class="selectize-products" placeholder="Buscar producto por código o nombre...">
						<option value="">Seleccione...</option>
						@foreach ($categories as $category)
							@if ($category->products->count() > 0)
								<optgroup label="{{ mb_strtoupper($category->title) }}">
									@foreach ($category->products as $product)
										<option value="{{ $product->id }}" data-data='@json($product)'>
											{{ $product->title }} ({{ $product->code }})
										</option>
									@endforeach
								</optgroup>
							@endif
						@endforeach
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" class="btn btn-outline-success btn-block" id="btn-add-free">
						<i class="fa fa-plus mr-1"></i> Producto Libre
					</button>
				</div>
			</div>

			<div id="products-cont">
				<div class="table-responsive">
					<table class="table table-bordered table-sm" id="products-table">
						<thead class="bg-light">
							<tr>
								<th width="100">Código</th>
								<th>Descripción</th>
								<th width="80">Cant.</th>
								<th width="120">P. Unitario</th>
								<th width="90">Tributos (%)</th>
								<th width="120">Total</th>
								<th width="40"></th>
							</tr>
						</thead>
						<tbody id="products-list">
							{{-- Pre-populate existing items --}}
						</tbody>
					</table>
				</div>
			</div>

			<div class="alert alert-warning d-none" id="no-products-alert">
				<i class="fa fa-exclamation-triangle mr-2"></i>Agregue al menos un producto a la cotización.
			</div>

			<hr>

			{{-- IVA TOGGLE --}}
			<div class="row">
				<div class="col-md-6">
					<h5 class="mb-3"><i class="fa fa-calculator mr-2"></i>Impuestos</h5>
					<div class="form-group">
						<div class="custom-control custom-switch">
							<input type="checkbox" class="custom-control-input" id="iva_toggle" {{ $quotation->iva_rate > 0 ? 'checked' : '' }}>
							<label class="custom-control-label" for="iva_toggle"><b>Aplicar IVA (16%)</b></label>
						</div>
					</div>
					<input type="hidden" name="iva_rate" id="iva_rate" value="{{ $quotation->iva_rate }}">
					<input type="hidden" name="igtf_rate" id="igtf_rate" value="{{ $quotation->igtf_rate }}">
				</div>
				<div class="col-md-6">
					<h5 class="mb-3"><i class="fa fa-sticky-note-o mr-2"></i>Notas</h5>
					<div class="form-group">
						<textarea name="notes" id="notes" class="form-control" rows="3">{{ $quotation->notes }}</textarea>
					</div>
				</div>
			</div>

			<hr>

			{{-- TOTALES --}}
			<h5 class="mb-3"><i class="fa fa-money mr-2"></i>Totales</h5>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>Sub-Total</b></label>
						<div class="col-sm-7">
							<input type="number" name="subtotal" id="subtotal" class="form-control text-right" step="0.01" value="{{ $quotation->subtotal }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label"><b>Descuento 1</b></label>
						<div class="col-sm-3">
							<div class="input-group">
								<input type="number" name="discount_1" id="discount_1" class="form-control text-right" step="0.01" value="{{ $quotation->discount_1 }}" min="0" max="100">
								<span class="input-group-text">%</span>
							</div>
						</div>
						<div class="col-sm-6">
							<input type="number" name="discount_1_amount" id="discount_1_amount" class="form-control text-right" step="0.01" value="{{ $quotation->discount_1_amount }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label"><b>Descuento 2</b></label>
						<div class="col-sm-3">
							<div class="input-group">
								<input type="number" name="discount_2" id="discount_2" class="form-control text-right" step="0.01" value="{{ $quotation->discount_2 }}" min="0" max="100">
								<span class="input-group-text">%</span>
							</div>
						</div>
						<div class="col-sm-6">
							<input type="number" name="discount_2_amount" id="discount_2_amount" class="form-control text-right" step="0.01" value="{{ $quotation->discount_2_amount }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>Flete</b></label>
						<div class="col-sm-7">
							<input type="number" name="freight" id="freight" class="form-control text-right" step="0.01" value="{{ $quotation->freight }}" min="0">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>Total Exento</b></label>
						<div class="col-sm-7">
							<input type="number" name="tax_exempt" id="tax_exempt" class="form-control text-right" step="0.01" value="{{ $quotation->tax_exempt }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>Base Imponible</b></label>
						<div class="col-sm-7">
							<input type="number" name="tax_base" id="tax_base" class="form-control text-right" step="0.01" value="{{ $quotation->tax_base }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>IVA (<span id="iva_label">{{ $quotation->iva_rate }}</span>%)</b></label>
						<div class="col-sm-7">
							<input type="number" name="iva_amount" id="iva_amount" class="form-control text-right" step="0.01" value="{{ $quotation->iva_amount }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>IGTF</b></label>
						<div class="col-sm-7">
							<input type="number" name="igtf_amount" id="igtf_amount" class="form-control text-right" step="0.01" value="{{ $quotation->igtf_amount }}" readonly>
						</div>
					</div>
					<div class="form-group row bg-light p-2 rounded">
						<label class="col-sm-5 col-form-label"><b class="text-success" style="font-size: 1.2em;">TOTAL</b></label>
						<div class="col-sm-7">
							<input type="number" name="total" id="total" class="form-control text-right font-weight-bold" step="0.01" value="{{ $quotation->total }}" readonly style="font-size: 1.2em;">
						</div>
					</div>
				</div>
			</div>

			<hr>

			<div class="row">
				<div class="col-12 text-right">
					<a href="{{ route('admin.quotations.index') }}" class="btn btn-secondary mr-2">Cancelar</a>
					<button type="submit" class="btn btn-success"><i class="fa fa-save mr-2"></i>Actualizar Cotización</button>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection

@section('scripts')
@parent
<script>
$(function() {
	var itemIndex = {{ $quotation->items->count() }};

	// ========================================
	// DESCRIPTION EDITOR (contenteditable + paste image compression)
	// ========================================
	function htmlEscape(str) {
		return $('<div>').text(str || '').html();
	}

	function syncEditorToInput(editor) {
		var $editor = $(editor);
		$editor.closest('.item').find('.description-input').val($editor.html());
	}

	$('body').on('input', '.description-editor', function() {
		syncEditorToInput(this);
	});

	$('body').on('paste', '.description-editor', function(e) {
		var clipboardData = e.originalEvent.clipboardData || window.clipboardData;
		if (!clipboardData) return;

		var items = clipboardData.items;
		if (!items) return;

		var editor = this;
		for (var i = 0; i < items.length; i++) {
			if (items[i].type && items[i].type.indexOf('image') === 0) {
				e.preventDefault();
				var file = items[i].getAsFile();

				if (file.size > 5 * 1024 * 1024) {
					alert('La imagen excede el tamaño máximo permitido (5 MB).');
					return;
				}

				compressAndInsertImage(file, editor);
				return;
			}
		}
	});

	function compressAndInsertImage(file, editor) {
		var reader = new FileReader();
		reader.onload = function(evt) {
			var img = new Image();
			img.onload = function() {
				var canvas = document.createElement('canvas');
				var maxDim = 1024;
				var w = img.width, h = img.height;

				if (w > maxDim || h > maxDim) {
					var ratio = Math.min(maxDim / w, maxDim / h);
					w = Math.round(w * ratio);
					h = Math.round(h * ratio);
				}

				canvas.width = w;
				canvas.height = h;
				canvas.getContext('2d').drawImage(img, 0, 0, w, h);

				var dataUrl = canvas.toDataURL('image/jpeg', 0.85);
				var imgTag = '<img src="' + dataUrl + '" style="max-width:100%;height:auto;display:block;margin:5px 0;">';

				editor.focus();
				document.execCommand('insertHTML', false, imgTag);
				syncEditorToInput(editor);
			};
			img.src = evt.target.result;
		};
		reader.readAsDataURL(file);
	}

	// ========================================
	// CLIENT SELECTION (inline editable fields)
	// ========================================
	@php
		$clientsForJs = $clients->keyBy('id')->map(function ($c) {
			return [
				'title' => $c->title,
				'document' => $c->document,
				'email' => $c->email,
				'phone' => $c->phone,
				'address' => $c->address,
			];
		});
	@endphp
	var clientsData = {!! json_encode($clientsForJs) !!};

	$('.selectize-client').selectize({
		persist: false,
		sortField: 'text',
		onChange: function(value) {
			if (!value || !clientsData[value]) {
				clearClientFields();
				return;
			}
			fillClientFields(clientsData[value]);
		}
	});

	function fillClientFields(info) {
		$('#cli_title').val(info.title || '');
		$('#cli_document').val(info.document || '');
		$('#cli_email').val(info.email || '');
		$('#cli_phone').val(info.phone || '');
		$('#cli_address').val(info.address || '');
	}

	function clearClientFields() {
		$('#cli_title, #cli_document, #cli_email, #cli_phone, #cli_address').val('');
	}

	$('#btn-new-client').on('click', function() {
		$('#client_id')[0].selectize.clear();
		clearClientFields();
		$('#cli_title').focus();
	});

	// ========================================
	// STATUS COMMENT TOGGLE (show only when status != draft)
	// ========================================
	function toggleStatusComment() {
		var status = $('#status').val();
		if (status === 'draft') {
			$('#status-comment-group').slideUp(150);
		} else {
			$('#status-comment-group').slideDown(150);
		}
	}
	$('#status').on('change', toggleStatusComment);
	toggleStatusComment();

	// ========================================
	// PRE-POPULATE EXISTING ITEMS
	// ========================================
	@foreach($quotation->items as $index => $item)
		(function() {
			var idx = {{ $index }};
			var descHtml = @json($item->description);
			@if($item->product_id)
				var row = `
					<tr class="item" id="item-${idx}">
						<td>
							<input type="text" class="form-control form-control-sm" value="{{ $item->code }}" readonly>
							<input type="hidden" name="items[${idx}][product_id]" value="{{ $item->product_id }}">
							<input type="hidden" name="items[${idx}][code]" value="{{ $item->code }}">
							<input type="hidden" name="items[${idx}][sort_order]" value="${idx}">
						</td>
						<td>
							<div class="description-editor" contenteditable="true" data-idx="${idx}" data-placeholder="Descripción del producto. Puedes pegar imágenes (Ctrl+V)."></div>
							<input type="hidden" name="items[${idx}][description]" class="description-input" value="">
						</td>
						<td>
							<input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm text-right quantity" value="{{ (int) $item->quantity }}" min="1" step="1">
						</td>
						<td>
							<input type="number" name="items[${idx}][unit_price]" class="form-control form-control-sm text-right unit-price" value="{{ $item->unit_price }}" min="0" step="0.01">
						</td>
						<td>
							<input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm text-right discount-pct" value="{{ $item->discount_percent }}" min="0" max="100" step="0.01">
							<input type="hidden" name="items[${idx}][discount_amount]" class="discount-amount" value="{{ $item->discount_amount }}">
						</td>
						<td>
							<input type="number" name="items[${idx}][total]" class="form-control form-control-sm text-right line-total font-weight-bold" value="{{ $item->total }}" readonly>
						</td>
						<td>
							<button type="button" class="btn btn-sm btn-danger btn-remove-item" data-idx="${idx}"><i class="fa fa-times"></i></button>
						</td>
					</tr>`;
			@else
				var row = `
					<tr class="item" id="item-${idx}">
						<td>
							<input type="text" name="items[${idx}][code]" class="form-control form-control-sm" value="{{ $item->code }}">
							<input type="hidden" name="items[${idx}][product_id]" value="">
							<input type="hidden" name="items[${idx}][sort_order]" value="${idx}">
						</td>
						<td>
							<div class="description-editor" contenteditable="true" data-idx="${idx}" data-placeholder="Descripción del producto o servicio. Puedes pegar imágenes (Ctrl+V)."></div>
							<input type="hidden" name="items[${idx}][description]" class="description-input" value="">
						</td>
						<td>
							<input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm text-right quantity" value="{{ (int) $item->quantity }}" min="1" step="1">
						</td>
						<td>
							<input type="number" name="items[${idx}][unit_price]" class="form-control form-control-sm text-right unit-price" value="{{ $item->unit_price }}" min="0" step="0.01">
						</td>
						<td>
							<input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm text-right discount-pct" value="{{ $item->discount_percent }}" min="0" max="100" step="0.01">
							<input type="hidden" name="items[${idx}][discount_amount]" class="discount-amount" value="{{ $item->discount_amount }}">
						</td>
						<td>
							<input type="number" name="items[${idx}][total]" class="form-control form-control-sm text-right line-total font-weight-bold" value="{{ $item->total }}" readonly>
						</td>
						<td>
							<button type="button" class="btn btn-sm btn-danger btn-remove-item" data-idx="${idx}"><i class="fa fa-times"></i></button>
						</td>
					</tr>`;
			@endif
			$('#products-list').append(row);
			// Inject description HTML after row is in DOM (safer than embedding in template literal)
			$('#item-' + idx + ' .description-editor').html(descHtml);
			$('#item-' + idx + ' .description-input').val(descHtml);
		})();
	@endforeach

	// ========================================
	// PRODUCT SELECTOR (SELECTIZE)
	// ========================================
	$('.selectize-products').selectize({
		persist: false,
		sortField: 'text',
		onItemAdd: function(value, $item) {
			var data = this.options[value].data;
			if (data) {
				addProductRow(data);
			}
			this.clear(true);
		}
	});

	function addProductRow(product) {
		var idx = itemIndex++;
		var initialDesc = htmlEscape(product.title + (product.description ? ' - ' + product.description : ''));
		var row = `
			<tr class="item" id="item-${idx}">
				<td>
					<input type="text" class="form-control form-control-sm" value="${product.code}" readonly>
					<input type="hidden" name="items[${idx}][product_id]" value="${product.id}">
					<input type="hidden" name="items[${idx}][code]" value="${product.code}">
					<input type="hidden" name="items[${idx}][sort_order]" value="${idx}">
				</td>
				<td>
					<div class="description-editor" contenteditable="true" data-idx="${idx}" data-placeholder="Descripción del producto. Puedes pegar imágenes (Ctrl+V).">${initialDesc}</div>
					<input type="hidden" name="items[${idx}][description]" class="description-input" value="${initialDesc}">
				</td>
				<td>
					<input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm text-right quantity" value="1" min="1" step="1">
				</td>
				<td>
					<input type="number" name="items[${idx}][unit_price]" class="form-control form-control-sm text-right unit-price" value="${product.price}" min="0" step="0.01">
				</td>
				<td>
					<input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm text-right discount-pct" value="0" min="0" max="100" step="0.01">
					<input type="hidden" name="items[${idx}][discount_amount]" class="discount-amount" value="0">
				</td>
				<td>
					<input type="number" name="items[${idx}][total]" class="form-control form-control-sm text-right line-total font-weight-bold" value="${product.price}" readonly>
				</td>
				<td>
					<button type="button" class="btn btn-sm btn-danger btn-remove-item" data-idx="${idx}"><i class="fa fa-times"></i></button>
				</td>
			</tr>`;
		$('#products-list').append(row);
		calculateLineTotal($('#item-' + idx));
	}

	$('#btn-add-free').on('click', function() {
		var idx = itemIndex++;
		var row = `
			<tr class="item" id="item-${idx}">
				<td>
					<input type="text" name="items[${idx}][code]" class="form-control form-control-sm" value="LIBRE">
					<input type="hidden" name="items[${idx}][product_id]" value="">
					<input type="hidden" name="items[${idx}][sort_order]" value="${idx}">
				</td>
				<td>
					<div class="description-editor" contenteditable="true" data-idx="${idx}" data-placeholder="Descripción del producto o servicio. Puedes pegar imágenes (Ctrl+V)."></div>
					<input type="hidden" name="items[${idx}][description]" class="description-input" value="">
				</td>
				<td>
					<input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm text-right quantity" value="1" min="1" step="1">
				</td>
				<td>
					<input type="number" name="items[${idx}][unit_price]" class="form-control form-control-sm text-right unit-price" value="0" min="0" step="0.01">
				</td>
				<td>
					<input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm text-right discount-pct" value="0" min="0" max="100" step="0.01">
					<input type="hidden" name="items[${idx}][discount_amount]" class="discount-amount" value="0">
				</td>
				<td>
					<input type="number" name="items[${idx}][total]" class="form-control form-control-sm text-right line-total font-weight-bold" value="0.00" readonly>
				</td>
				<td>
					<button type="button" class="btn btn-sm btn-danger btn-remove-item" data-idx="${idx}"><i class="fa fa-times"></i></button>
				</td>
			</tr>`;
		$('#products-list').append(row);
	});

	$('body').on('click', '.btn-remove-item', function() {
		$('#item-' + $(this).data('idx')).remove();
		calculateTotals();
		if ($('#products-list .item').length === 0) {
			$('#no-products-alert').removeClass('d-none');
		}
	});

	$('body').on('change input', '.quantity, .unit-price, .discount-pct', function() {
		calculateLineTotal($(this).closest('.item'));
	});

	function calculateLineTotal(row) {
		var qty = parseFloat(row.find('.quantity').val()) || 0;
		var price = parseFloat(row.find('.unit-price').val()) || 0;
		var tribPct = parseFloat(row.find('.discount-pct').val()) || 0;
		var lineSubtotal = qty * price;
		var tributeAmt = lineSubtotal * (tribPct / 100);
		var lineTotal = lineSubtotal + tributeAmt;
		row.find('.discount-amount').val(tributeAmt.toFixed(2));
		row.find('.line-total').val(lineTotal.toFixed(2));
		calculateTotals();
	}

	function calculateTotals() {
		var subtotal = 0;
		$('#products-list .item').each(function() {
			subtotal += parseFloat($(this).find('.line-total').val()) || 0;
		});
		var disc1Pct = parseFloat($('#discount_1').val()) || 0;
		var disc2Pct = parseFloat($('#discount_2').val()) || 0;
		var disc1Amt = subtotal * (disc1Pct / 100);
		var disc2Amt = subtotal * (disc2Pct / 100);
		var freight = parseFloat($('#freight').val()) || 0;
		var baseAfterDiscounts = subtotal - disc1Amt - disc2Amt;
		var ivaRate = parseFloat($('#iva_rate').val()) || 0;
		var igtfRate = parseFloat($('#igtf_rate').val()) || 0;
		var taxExempt = (ivaRate === 0) ? baseAfterDiscounts : 0;
		var taxBase = (ivaRate > 0) ? baseAfterDiscounts : 0;
		var ivaAmount = taxBase * (ivaRate / 100);
		var igtfAmount = taxBase * (igtfRate / 100);
		var total = taxBase + taxExempt + ivaAmount + igtfAmount + freight;
		$('#subtotal').val(subtotal.toFixed(2));
		$('#discount_1_amount').val(disc1Amt.toFixed(2));
		$('#discount_2_amount').val(disc2Amt.toFixed(2));
		$('#tax_exempt').val(taxExempt.toFixed(2));
		$('#tax_base').val(taxBase.toFixed(2));
		$('#iva_amount').val(ivaAmount.toFixed(2));
		$('#igtf_amount').val(igtfAmount.toFixed(2));
		$('#total').val(total.toFixed(2));
	}

	$('#iva_toggle').on('change', function() {
		var rate = $(this).is(':checked') ? 16 : 0;
		$('#iva_rate').val(rate);
		$('#iva_label').text(rate);
		calculateTotals();
	});

	$('#discount_1, #discount_2, #freight').on('input', function() {
		calculateTotals();
	});

	$('#quotation-form').on('submit', function(e) {
		if ($('#products-list .item').length === 0) {
			e.preventDefault();
			$('#no-products-alert').removeClass('d-none');
			$('html, body').animate({ scrollTop: $('#no-products-alert').offset().top - 100 }, 300);
			return false;
		}
	});
});
</script>
@endsection
