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
								<option value="efectivo" {{ old('payment_method') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
								<option value="deposito" {{ old('payment_method') == 'deposito' ? 'selected' : '' }}>Deposito</option>
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
					<div class="col-sm-6">
						<div class="form-group {{ $errors->has('seller_id') ? 'has-error' : '' }}">
							<label for="seller_id">Vendedor&nbsp;<b class="text-danger">*</b></label>
							<select name="seller_id" id="seller" class="selectize-sorted" required>
								<option value="">Seleccione</option>
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
					<div class="col-sm-6">
						<div class="form-group {{ $errors->has('product_id') ? 'has-error' : '' }}">
							<label for="products">Productos&nbsp;<b class="text-danger">*</b></label>
							<select name="products[]" class="selectize-products" multiple required>
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
									<th>Producto</th>
									<th>Código</th>
									<th>Tipo</th>
									<th>Costo</th>
									<th>Precio</th>
									<th width="150">Cantidad</th>
									<th>Proveedor</th>
									<th>Subtotal</th>
									<th width="50">Descuento</th>
									<th>Total</th>
								</thead>
								<tbody id="products-list"></tbody>
							</table>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group {{ $errors->has('service_id') ? 'has-error' : '' }}">
							<label for="services">Servicios&nbsp;<b class="text-danger">*</b></label>
							<select name="services[]" class="selectize-services" multiple required>
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
									<th>Servicio</th>
									<th>Código</th>
									<th>Precio</th>
									<th width="150">Cantidad</th>
									<th>Subtotal</th>
									<th width="50">Descuento</th>
									<th>Total</th>
								</thead>
								<tbody id="services-list"></tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('subtotal') ? 'has-error' : '' }}">
							<label for="subtotal"><b>Subtotal</b>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="subtotal" name="subtotal" class="form-control" value="{{ old('subtotal', 0) }}" min="0" required readonly>
							@if ($errors->has('subtotal'))
								<em class="invalid-feedback">
									{{ $errors->first('subtotal') }}
								</em>
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('cityhall') ? 'has-error' : '' }}">
							<label for="cityhall">Alcaldía (9% cuando factura)&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="cityhall" name="cityhall" class="form-control" value="{{ old('cityhall', 0) }}" min="0" required readonly>
							@if ($errors->has('cityhall'))
								<em class="invalid-feedback">
									{{ $errors->first('cityhall') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('igtf') ? 'has-error' : '' }}">
							<label for="igtf">IGTF (3% cuando paga dolares)&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="igtf" name="igtf" class="form-control" value="{{ old('igtf', 0) }}" min="0" required readonly>
							@if ($errors->has('igtf'))
								<em class="invalid-feedback">
									{{ $errors->first('igtf') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('iva') ? 'has-error' : '' }}">
							<label for="iva">IVA (16% sobre: subtotal + alcaldía si aplica)&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="iva" name="iva" class="form-control" value="{{ old('iva', 0) }}" min="0" required readonly>
							@if ($errors->has('iva'))
								<em class="invalid-feedback">
									{{ $errors->first('iva') }}
								</em>
							@endif
						</div>
					</div>
					{{-- <div class="col-sm-3">
						<div class="form-group {{ $errors->has('total') ? 'has-error' : '' }}">
							<label for="total">Total&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="total" name="total" class="form-control" value="{{ old('total', 0) }}" min="0" required readonly>
							@if ($errors->has('total'))
								<em class="invalid-feedback">
									{{ $errors->first('total') }}
								</em>
							@endif
						</div>
					</div> --}}
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('profit') ? 'has-error' : '' }}">
							<label for="profit">Ganancia&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="profit" name="profit" class="form-control" value="{{ old('profit', 0) }}" min="0" required readonly>
							@if ($errors->has('profit'))
								<em class="invalid-feedback">
									{{ $errors->first('profit') }}
								</em>
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('commission') ? 'has-error' : '' }}">
							<label for="commission"><b>Comisión</b>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="commission" name="commission" class="form-control" value="{{ old('commission', 0) }}" min="0" required readonly>
							@if ($errors->has('commission'))
								<em class="invalid-feedback">
									{{ $errors->first('commission') }}
								</em>
							@endif
						</div>
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
		function calculateValues()
		{
			console.log('Calculating...');

			var type = $('#invoice_type').val();
			var method = $('#payment_method').val();
			var subtotal = iva = igtf = cityhall = total = profit = commission = provider = 0;
			var seller_profit = 0.01; // 1%

			$('.item .total').each(function() {
				var value = parseFloat($(this).val());
				subtotal += value;
			});

			$('.item .provider').each(function() {
				var value = parseFloat($(this).val());
				provider += value;
			});

			if (type == 'factura') {
				cityhall = subtotal * 0.09;
			}

			if (method == 'dolares') {
				igtf = subtotal * 0.03;
			}

			if (subtotal > 0) {
				iva = (subtotal + cityhall) * 0.16;
				profit = subtotal - provider - cityhall;
				commission = profit * seller_profit;
				total = subtotal + cityhall + igtf + iva;
			}

			console.log(subtotal.toFixed(2), iva.toFixed(2), igtf.toFixed(2), cityhall.toFixed(2), total.toFixed(2), profit.toFixed(2), commission.toFixed(2));
			
			$('#iva').val(iva.toFixed(2));
			$('#igtf').val(igtf.toFixed(2));
			// $('#total').val(total.toFixed(2));
			$('#profit').val(profit.toFixed(2));
			$('#subtotal').val(subtotal.toFixed(2));
			$('#cityhall').val(cityhall.toFixed(2));
			$('#commission').val(commission.toFixed(2));
		}

        $(function()
		{
			function productHandler(action)
			{
				return function() {
					var item = arguments[0];
					var data = this.options[item].data;

					if (action == 'ADD') {
						$('#products-list').append('<tr class="item" id="prod-' + data.id + '"><td><b>' + data.title + '</b><input type="hidden" name="prod[]" value="' + data.id + '"></td><td><i>' + data.code + '</i></td><td><span class="badge badge-secondary">' + data.type + '</span></td><td class="cost text-right">' + data.cost + '</td><td class="price text-right">' + data.price + '</td><td><input type="number" name="cant[]" min="1" value="1" class="form-control quantity"></td><td><input type="number" name="provider[]" min="0" class="form-control text-right provider" value="' + data.cost + '" readonly></td><td><input type="number" name="subtotal[]" min="0" class="form-control text-right subtotal" value="' + data.price + '" readonly></td><td><input type="number" name="discount[]" min="0" class="form-control text-right discount" value="0"></td><td><input type="number" name="total[]" min="0" class="form-control text-right total" value="' + data.price + '" readonly></td></tr>');
						$('#products-list #prod-' + data.id + ' .quantity').inputSpinner();
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
						$('#services-list').append('<tr class="item" id="serv-' + data.id + '"><td><b>' + data.title + '</b><input type="hidden" name="serv[]" value="' + data.id + '"></td><td><i>' + data.code + '</i></td><td class="price text-right">' + data.price + '</td><td><input type="number" name="cant[]" min="1" value="1" class="form-control quantity"></td><td><input type="number" name="subtotal[]" min="0" class="form-control text-right subtotal" value="' + data.price + '" readonly></td><td><input type="number" name="discount[]" min="0" class="form-control text-right discount" value="0"></td><td><input type="number" name="total[]" min="0" class="form-control text-right total" value="' + data.price + '" readonly></td></tr>');
						$('#services-list #serv-' + data.id + ' .quantity').inputSpinner();
					} else {
						$('#services-list #serv-' + data.id).remove();
					}
					calculateValues();
				};
			}

			$('body').on('change', '.quantity', function() {
				var quantity = $(this).val();
				var parent = $(this).parents('.item');
				var cost = parent.find('.cost').text();
				var price = parent.find('.price').text();
				var total = parseFloat(price) * parseInt(quantity);
				var provider = parseFloat(cost) * parseInt(quantity);
				parent.find('.provider').val(provider);
				parent.find('.subtotal').val(total);
				parent.find('.total').val(total);
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

			$('#seller').on('change', function() {
				var data = $(this).find(':selected').data('data');
				console.log(data);
			});

			$('#notes').maxlength({
                threshold: 300
            });
        });
    </script>
@endsection
