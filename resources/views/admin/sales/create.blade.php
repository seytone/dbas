@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            Registrar Venta
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sales.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group {{ $errors->has('registered_at') ? 'has-error' : '' }}">
							<label for="registered_at">Fecha&nbsp;<b class="text-danger">*</b></label>
							<input type="date" id="registered_at" name="registered_at" class="form-control" value="{{ old('registered_at', date('Y-m-d')) }}" required>
							@if ($errors->has('registered_at'))
								<em class="invalid-feedback">
									{{ $errors->first('registered_at') }}
								</em>
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group {{ $errors->has('invoice_type') ? 'has-error' : '' }}">
							<label for="invoice_type">Tipo&nbsp;<b class="text-danger">*</b></label>
							<select name="invoice_type" id="invoice_type" class="custom-select" required onchange="calculateValues()">
								<option value="nota" {{ old('invoice_type') == 'nota' ? 'selected' : '' }}>Nota de Entrega</option>
								<option value="factura" {{ old('invoice_type') == 'factura' ? 'selected' : '' }}>Factura</option>
							</select>
							@if ($errors->has('invoice_type'))
								<em class="invalid-feedback">
									{{ $errors->first('invoice_type') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group {{ $errors->has('invoice_number') ? 'has-error' : '' }}">
							<label for="invoice_number">Número&nbsp;<b class="text-danger">*</b></label>
							<input type="text" id="invoice_number" name="invoice_number" class="form-control" value="{{ old('invoice_number') }}" required>
							@if ($errors->has('invoice_number'))
								<em class="invalid-feedback">
									{{ $errors->first('invoice_number') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group {{ $errors->has('payment_method') ? 'has-error' : '' }}">
							<label for="payment_method">Forma de Pago&nbsp;<b class="text-danger">*</b></label>
							<select name="payment_method" id="payment_method" class="custom-select" required onchange="calculateValues()">
								<option value="bolivares" {{ old('payment_method') == 'bolivares' ? 'selected' : '' }}>Bolívares</option>
								<option value="dolares" {{ old('payment_method') == 'dolares' ? 'selected' : '' }}>Dólares</option>
								<option value="zelle" {{ old('payment_method') == 'zelle' ? 'selected' : '' }}>Zelle</option>
								<option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>Paypal</option>
								<option value="binance" {{ old('payment_method') == 'binance' ? 'selected' : '' }}>Binance</option>
								<option value="panama" {{ old('payment_method') == 'panama' ? 'selected' : '' }}>Panamá</option>
							</select>
							@if ($errors->has('payment_method'))
								<em class="invalid-feedback">
									{{ $errors->first('payment_method') }}
								</em>
							@endif
						</div>
					</div>
				</div>
				<div class="form-group {{ $errors->has('trello') ? 'has-error' : '' }}">
                    <label for="trello">Trello&nbsp;<b class="text-danger">*</b></label>
                    <input type="url" id="trello" name="trello" class="form-control" value="{{ old('trello') }}" required>
                    @if ($errors->has('trello'))
                        <em class="invalid-feedback">
                            {{ $errors->first('trello') }}
                        </em>
                    @endif
                </div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group {{ $errors->has('client_id') ? 'has-error' : '' }}">
							<label for="client_id">Cliente&nbsp;<b class="text-danger">*</b></label>
							<select name="client_id" class="selectize-create" required>
								<option value="">Seleccione</option>
								@foreach ($clients as $client)
									<option value="{{ $client->id }}">{{ $client->getIdentification() }}</option>
								@endforeach
							</select>
							@if ($errors->has('client_id'))
								<em class="invalid-feedback">
									{{ $errors->first('client_id') }}
								</em>
							@endif
						</div>
					</div>
					@if ($user->hasRole('Superadmin'))
						<div class="col-sm-6">
							<div class="form-group {{ $errors->has('seller_id') ? 'has-error' : '' }}">
								<label for="seller_id">Vendedor&nbsp;<b class="text-danger">*</b></label>
								<select name="seller_id" id="seller" class="selectize-seller" required>
									<option value="" selected>Seleccione</option>
									@foreach ($sellers as $seller)
										<option value="{{ $seller->id }}" data-data="{{ json_encode($seller) }}">{{ $seller->user->getFullname() }}</option>
									@endforeach
								</select>
								@if ($errors->has('seller_id'))
									<em class="invalid-feedback">
										{{ $errors->first('seller_id') }}
									</em>
								@endif
							</div>
						</div>
					@else
						<input type="hidden" id="seller" name="seller_id" value="{{ $user->seller->id }}">
					@endif
					<input type="hidden" id="margin_prods" value="{{ $user->seller->commission_1 ?? 1 }}">
					<input type="hidden" id="margin_servs" value="{{ $user->seller->commission_4 ?? 50 }}">
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group {{ $errors->has('product_id') ? 'has-error' : '' }}">
							<label for="products">Productos</label>
							<select name="products_selected[]" class="selectize-products" multiple>
								<option value="">Seleccione</option>
								@foreach ($categories as $category)
									@if ($category->products->count() > 0)
										<optgroup label="{{ mb_strtoupper($category->title) }}">
											@foreach ($category->products as $product)
												<option value="{{ $product->id }}" data-data="{{ $product->toJson() }}" class="pl-3">{{ $product->title . ' (' . $product->code . ')' }}</option>
											@endforeach
										</optgroup>
									@endif
								@endforeach
							</select>
							@if ($errors->has('product_id'))
								<em class="invalid-feedback">
									{{ $errors->first('product_id') }}
								</em>
							@endif
						</div>
						<div class="table-responsive">
							<table class="table table-hover align-middle">
								<thead class="bg-light">
									<th class="text-left" width="250">Producto</th>
									<th class="text-left" width="150">Código</th>
									<th class="text-left">Tipo</th>
									<th class="text-right">Costo</th>
									<th class="text-right">Precio</th>
									<th class="text-center" width="150">Cantidad</th>
									<th class="text-right" width="80">Subtotal</th>
									<th class="text-right" width="80">Desc.</th>
									<th class="text-right" width="80">Prov.</th>
									<th class="text-right" width="80">Total</th>
								</thead>
								<tbody id="products-list"></tbody>
								<tfoot class="bg-light">
									<th colspan="8"></th>
									<th class="text-right">Costo<br><strong id="costo_prods">0.00</strong></th>
									<th class="text-right">Total<br><strong id="total_prods">0.00</strong></th>
								</tfoot>
							</table>
							<input type="hidden" id="provider" name="provider" value="0">
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group {{ $errors->has('service_id') ? 'has-error' : '' }}">
							<label for="services">Servicios</label>
							<select name="services_selected[]" class="selectize-services" multiple>
								<option value="">Seleccione</option>
								@foreach ($services as $service)
									<option value="{{ $service->id }}" data-data="{{ $service->toJson() }}">{{ $service->title . ' (' . $service->code . ')' }}</option>
								@endforeach
							</select>
							@if ($errors->has('service_id'))
								<em class="invalid-feedback">
									{{ $errors->first('service_id') }}
								</em>
							@endif
						</div>
						<div class="table-responsive">
							<table class="table table-hover align-middle">
								<thead class="bg-light">
									<th class="text-left" width="250">Servicio</th>
									<th class="text-left" width="150">Código</th>
									<th class="text-right">Precio</th>
									<th class="text-center" width="150">Cantidad</th>
									<th class="text-right" width="80">Subtotal</th>
									<th class="text-right" width="80">Desc.</th>
									<th class="text-right" width="80">Total</th>
								</thead>
								<tbody id="services-list"></tbody>
								<tfoot class="bg-light">
									<th colspan="6"></th>
									<th class="text-right">Total<br><strong id="total_servs">0.00</strong></th>
								</tfoot>
							</table>
						</div>
					</div>
					<div class="col-12">
						<hr>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('subtotal') ? 'has-error' : '' }}">
							<label for="subtotal">Base Imponible <span class="text-muted">(subtotal)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="subtotal" name="subtotal" class="form-control" value="{{ old('subtotal', 0) }}" min="0" required readonly>
							@if ($errors->has('subtotal'))
								<em class="invalid-feedback">
									{{ $errors->first('subtotal') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('cityhall') ? 'has-error' : '' }}">
							<label for="cityhall">Alcaldía <span class="text-muted">(9% sobre subtotal cuando factura)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="cityhall" name="cityhall" class="form-control" value="{{ old('cityhall', 0) }}" min="0" required readonly>
							@if ($errors->has('cityhall'))
								<em class="invalid-feedback">
									{{ $errors->first('cityhall') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('iva') ? 'has-error' : '' }}">
							<label for="iva">IVA <span class="text-muted">(16% sobre [subtotal + alcaldía] cuando factura)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="iva" name="iva" class="form-control" value="{{ old('iva', 0) }}" min="0" required readonly>
							@if ($errors->has('iva'))
								<em class="invalid-feedback">
									{{ $errors->first('iva') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('igtf') ? 'has-error' : '' }}">
							<label for="igtf">IGTF <span class="text-muted">(3% sobre pago en USD)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="igtf" name="igtf" class="form-control" value="{{ old('igtf', 0) }}" min="0" required readonly>
							@if ($errors->has('igtf'))
								<em class="invalid-feedback">
									{{ $errors->first('igtf') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('total') ? 'has-error' : '' }}">
							<label for="total">Total Venta <span class="text-muted">(subtotal + alcaldía + iva + igtf)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="total" name="total" class="form-control" value="{{ old('total', 0) }}" min="0" required readonly>
							@if ($errors->has('total'))
								<em class="invalid-feedback">
									{{ $errors->first('total') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('profit') ? 'has-error' : '' }}">
							<label for="profit">Ganancia <span class="text-muted">(total productos - costos)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="profit" name="profit" class="form-control" value="{{ old('profit', 0) }}" min="0" required readonly>
							@if ($errors->has('profit'))
								<em class="invalid-feedback">
									{{ $errors->first('profit') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('commission_prod') ? 'has-error' : '' }}">
							<label for="commission_prod">Comisión Productos <span class="text-muted">(<span id="margin_p">{{ $user->seller->commission_1 ?? 1 }}</span>% sobre ganancia productos)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="commission_prod" name="commission_prod" class="form-control" value="{{ old('commission_prod', 0) }}" min="0" required readonly>
							@if ($errors->has('commission_prod'))
								<em class="invalid-feedback">
									{{ $errors->first('commission_prod') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('commission_serv') ? 'has-error' : '' }}">
							<label for="commission_serv">Comisión Servicios <span class="text-muted">(<span id="margin_s">{{ $user->seller->commission_4 ?? 50 }}</span>% sobre servicios)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="commission_serv" name="commission_serv" class="form-control" value="{{ old('commission_serv', 0) }}" min="0" required readonly>
							@if ($errors->has('commission_serv'))
								<em class="invalid-feedback">
									{{ $errors->first('commission_serv') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('commission_total') ? 'has-error' : '' }}">
							<label for="commission_total">Comisión Total <span class="text-muted">(productos + servicios)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="commission_total" name="commission_total" class="form-control" value="{{ old('commission_total', 0) }}" min="0" required readonly>
							@if ($errors->has('commission_total'))
								<em class="invalid-feedback">
									{{ $errors->first('commission_total') }}
								</em>
							@endif
						</div>
					</div>
				</div>
				<div class="row d-none" id="payment_currency">
					<div class="col-12">
						<hr>
					</div>
					<div class="col-md-4">
						<br>
						<div class="custom-control custom-radio">
							<input type="radio" id="payment_usd_total" name="payment_currency" class="custom-control-input" value="usd" onchange="calculateValues()" checked>
							<label class="custom-control-label" for="payment_usd_total">Pago total en dólares</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="payment_usd_mixed" name="payment_currency" class="custom-control-input" value="mix" onchange="calculateValues()">
							<label class="custom-control-label" for="payment_usd_mixed">Pago en moneda combinada</label>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="payment_amount_usd">Monto a pagar en dólares</label>
							<input type="number" id="payment_amount_usd" name="payment_amount_usd" class="form-control" value="{{ old('payment_amount_usd', 0) }}" min="0" readonly onchange="calculateValues(true)">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="payment_amount_bsf">Monto a pagar en bolívares</label>
							<input type="number" id="payment_amount_bsf" name="payment_amount_bsf" class="form-control" value="{{ old('payment_amount_bsf', 0) }}" min="0" readonly>
						</div>
					</div>
					<div class="col-12">
						<hr>
					</div>
				</div>
				<div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                    <label for="notes">Anotaciones</label>
                    <textarea id="notes" name="notes" rows="1" class="form-control notes" maxlength="300">{{ old('notes') }}</textarea>
                    @if ($errors->has('notes'))
                        <em class="invalid-feedback">
                            {{ $errors->first('notes') }}
                        </em>
                    @endif
                </div>
                <div class="text-center text-md-right mt-4">
					<hr>
                    <input class="btn btn-success" type="submit" value="Registrar">
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script>
		function calculateValues(adjust = false)
		{
			console.log('Calculating...');

			var type = $('#invoice_type').val();
			var method = $('#payment_method').val();
			var margin_prods = parseFloat($('#margin_prods').val());
			var margin_servs = parseFloat($('#margin_servs').val());
			var payment_usd = parseFloat($('#payment_amount_usd').val());
			var payment_total = $('input[name="payment_currency"]:checked').val();

			var subtotal = subtotal_prod = subtotal_serv = iva = igtf = cityhall = total = profit = commission_prod = commission_serv = commission_total = costs = payment_amount_usd = payment_amount_bsf = 0;
			var seller_margin_prods = margin_prods / 100; // 1%
			var seller_margin_servs = margin_servs / 100; // 50%

			if (method != 'bolivares') {
				$('#payment_currency').removeClass('d-none');
			} else {
				$('#payment_currency').addClass('d-none');
			}

			// Calculate total costs for products
			$('.item .provider').each(function() {
				var value = parseFloat($(this).val());
				costs += value;
			});

			// Calculate total value for products
			$('.item .product').each(function() {
				var value = parseFloat($(this).val());
				subtotal_prod += value;
			});

			// Calculate total value for services
			$('.item .service').each(function() {
				var value = parseFloat($(this).val());
				subtotal_serv += value;
			});

			subtotal = subtotal_prod + subtotal_serv;

			if (type == 'factura')
			{
				cityhall = subtotal * 0.09;
				iva = (subtotal + cityhall) * 0.16;

				if (method != 'bolivares') {
					if (payment_total == 'usd') {
						igtf = subtotal * 0.03;
					} else {
						igtf = (adjust ? payment_usd : subtotal) * 0.03;
					}
				}
			}

			if (subtotal > 0) {
				profit = subtotal_prod - costs;
				commission_prod = (subtotal_prod - costs) * seller_margin_prods;
				commission_serv = subtotal_serv * seller_margin_servs;
				total = subtotal + cityhall + igtf + iva;
			}

			if (payment_total == 'usd') {
				payment_amount_usd = total;
				payment_amount_bsf = 0;
				$('#payment_amount_usd').attr('readonly', true);
			} else {
				payment_amount_usd = payment_usd > 0 && adjust ? payment_usd : subtotal;
				payment_amount_bsf = payment_usd > 0 ? total - payment_amount_usd : 0;
				$('#payment_amount_usd').attr('readonly', false);
			}

			commission_total = commission_prod + commission_serv;

			console.log(subtotal.toFixed(2), cityhall.toFixed(2), iva.toFixed(2), igtf.toFixed(2), total.toFixed(2), profit.toFixed(2), commission_prod.toFixed(2), commission_serv.toFixed(2), payment_amount_usd.toFixed(2), payment_amount_bsf.toFixed(2), commission_total.toFixed(2));
		
			$('#iva').val(iva.toFixed(2));
			$('#igtf').val(igtf.toFixed(2));
			$('#total').val(total.toFixed(2));
			$('#profit').val(profit.toFixed(2));
			$('#provider').val(costs.toFixed(2));
			$('#subtotal').val(subtotal.toFixed(2));
			$('#cityhall').val(cityhall.toFixed(2));
			$('#costo_prods').html(costs.toFixed(2));
			$('#total_prods').html(subtotal_prod.toFixed(2));
			$('#total_servs').html(subtotal_serv.toFixed(2));
			$('#commission_prod').val(commission_prod.toFixed(2));
			$('#commission_serv').val(commission_serv.toFixed(2));
			$('#commission_total').val(commission_total.toFixed(2));
			$('#payment_amount_usd').val(payment_amount_usd.toFixed(2));
			$('#payment_amount_bsf').val(payment_amount_bsf.toFixed(2));
		}

        $(function()
		{
			function productHandler(action)
			{
				return function() {
					var item = arguments[0];
					var data = this.options[item].data;

					if (action == 'ADD') {
						$('#products-list').append('<tr class="item" id="prod-' + data.id + '"><td><b>' + data.title + '</b><input type="hidden" name="products[' + item + '][id]" value="' + data.id + '"></td><td><i>' + data.code + '</i></td><td><span class="badge badge-secondary">' + data.type + '</span></td><td class="text-right cost">' + data.cost + '</td><td class="text-right price"><input type="hidden" name="products[' + item + '][price]" value="' + data.price + '">' + data.price + '</td><td><input type="number" name="products[' + item + '][quantity]" min="1" value="1" class="form-control p-0 quantity quantity_prod"></td><td><input type="number" min="0" class="form-control p-0 text-right subtotal" value="' + data.price + '" readonly></td><td><input type="number" name="products[' + item + '][discount]" min="0" class="form-control p-0 text-right discount" value="0"></td><td><input type="number" min="0" class="form-control p-0 text-right provider" value="' + data.cost + '" readonly></td><td><input type="number" name="products[' + item + '][total]" min="0" class="form-control p-0 text-right total product" value="' + data.price + '" readonly></td></tr>');
						$('#products-list #prod-' + data.id + ' .quantity_prod').inputSpinner();
					} else {
						$('#products-list #prod-' + data.id).remove();
					}
					calculateValues();
				};
			}

			function serviceHandler(action)
			{
				return function() {
					var item = arguments[0];
					var data = this.options[item].data;

					if (action == 'ADD') {
						$('#services-list').append('<tr class="item" id="serv-' + data.id + '"><td><b>' + data.title + '</b><input type="hidden" name="services[' + item + '][id]" value="' + data.id + '"></td><td><i>' + data.code + '</i></td><td class="price text-right"><input type="hidden" name="services[' + item + '][price]" value="' + data.price + '">' + data.price + '</td><td><input type="number" name="services[' + item + '][quantity]" min="1" value="1" class="form-control p-0 quantity quantity_serv"></td><td><input type="number" min="0" class="form-control p-0 text-right subtotal" value="' + data.price + '" readonly></td><td><input type="number" name="services[' + item + '][discount]" min="0" class="form-control p-0 text-right discount" value="0"></td><td><input type="number" name="services[' + item + '][total]" min="0" class="form-control p-0 text-right total service" value="' + data.price + '" readonly></td></tr>');
						$('#services-list #serv-' + data.id + ' .quantity_serv').inputSpinner();
					} else {
						$('#services-list #serv-' + data.id).remove();
					}
					calculateValues();
				};
			}

			function sellerHandler()
			{
				return function() {
					var item = arguments[0];
					var data = this.options[item].data;
					$('#margin_p').html(data.commission_1);
					$('#margin_s').html(data.commission_4);
					$('#margin_prods').val(data.commission_1);
					$('#margin_servs').val(data.commission_4);
					calculateValues();
				};
			}

			$('body').on('change', '.quantity_prod', function() {
				var quantity = $(this).val();
				var parent = $(this).parents('.item');
				var cost = parent.find('.cost').text();
				var price = parent.find('.price').text();
				var total = parseFloat(price) * parseInt(quantity);
				var provider = parseFloat(cost) * parseInt(quantity);
				parent.find('.provider').val(provider);
				parent.find('.subtotal').val(total);
				parent.find('.product').val(total);
				calculateValues();
			});

			$('body').on('change', '.quantity_serv', function() {
				var quantity = $(this).val();
				var parent = $(this).parents('.item');
				var price = parent.find('.price').text();
				var total = parseFloat(price) * parseInt(quantity);
				parent.find('.subtotal').val(total);
				parent.find('.service').val(total);
				calculateValues();
			});

			$('body').on('input', '.discount', function() {
				var discount = $(this).val();
				var parent = $(this).parents('.item');
				var subtotal = parent.find('.subtotal').val();
				var total = parseFloat(subtotal) - parseFloat(discount);
				parent.find('.total').val(total);
				calculateValues();
			});

			$('.selectize-products').selectize({
				persist: false,
				sortField: 'text',
				plugins: ["remove_button"],
				onItemAdd: productHandler('ADD'),
				onItemRemove: productHandler('DEL'),
			});

			$('.selectize-services').selectize({
				persist: false,
				sortField: 'text',
				plugins: ["remove_button"],
				onItemAdd: serviceHandler('ADD'),
				onItemRemove: serviceHandler('DEL'),
			});
			
			$('.selectize-seller').selectize({
				persist: false,
				sortField: 'text',
				onItemAdd: sellerHandler(),
			});

			$('#notes').maxlength({
                threshold: 300
            });
        });
    </script>
@endsection
