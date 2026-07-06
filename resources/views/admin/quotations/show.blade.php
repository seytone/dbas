@extends('layouts.admin')
@section('content')
<style>
	.description-cell img {
		max-width: 220px !important;
		max-height: 220px !important;
		width: auto !important;
		height: auto !important;
		object-fit: contain;
		margin: 4px 4px 4px 0;
		border-radius: 3px;
		display: inline-block !important;
		vertical-align: top;
	}
</style>
<div class="mb-3">
	<a class="btn btn-secondary" href="{{ route('admin.quotations.index') }}"><i class="fa fa-arrow-left mr-2"></i>Volver</a>
	@if($quotation->status !== 'accepted')
		<a class="btn btn-warning" href="{{ route('admin.quotations.edit', $quotation->id) }}"><i class="fa fa-wrench mr-2"></i>Editar</a>
	@endif
	<a class="btn btn-info" href="{{ route('admin.quotations.duplicate', $quotation->id) }}" onclick="return confirm('¿Desea duplicar esta cotización?');"><i class="fa fa-copy mr-2"></i>Duplicar</a>
	<a class="btn btn-dark" href="{{ route('admin.quotations.pdf', $quotation->id) }}" target="_blank"><i class="fa fa-file-pdf-o mr-2"></i>PDF</a>
	<a class="btn btn-default" href="{{ route('admin.quotations.print', $quotation->id) }}" target="_blank"><i class="fa fa-print mr-2"></i>Imprimir</a>
</div>

@if($quotation->status === 'accepted')
	<div class="alert alert-success">
		<i class="fa fa-lock mr-2"></i>
		<b>Cotización aceptada.</b> Esta cotización ya no puede ser modificada. Si necesitas hacer cambios, duplícala para crear una versión nueva.
	</div>
@endif

@if($quotation->manual_calculation)
	<div class="alert alert-secondary">
		<i class="fa fa-calculator mr-2"></i>
		<b>Cálculo manual.</b> Esta cotización es anterior a la automatización de tasas BCV/Binance. Los precios fueron calculados manualmente, sin la fórmula automática.
	</div>
@endif

<div class="card">
	<div class="card-header">
		<b>Cotización #{{ $quotation->formatted_number }}</b>
		<span class="float-right">
			@switch($quotation->status)
				@case('draft')
					<span class="badge badge-secondary">Borrador</span>
					@break
				@case('sent')
					<span class="badge badge-primary">Enviada</span>
					@break
				@case('accepted')
					<span class="badge badge-success">Aceptada</span>
					@break
				@case('rejected')
					<span class="badge badge-danger">Rechazada</span>
					@break
			@endswitch
		</span>
	</div>
	<div class="card-body">
		{{-- DATOS GENERALES --}}
		<div class="row mb-4">
			<div class="col-md-6">
				<h6 class="text-muted">DATOS DE LA COTIZACIÓN</h6>
				<table class="table table-sm table-borderless">
					<tr><td><b>Nro. Presupuesto</b></td><td>{{ $quotation->formatted_number }}</td></tr>
					<tr><td><b>Fecha Emisión</b></td><td>{{ $quotation->emission_date->format('d/m/Y') }}</td></tr>
					<tr><td><b>Fecha Vencimiento</b></td><td>{{ $quotation->expiration_date->format('d/m/Y') }}</td></tr>
					<tr><td><b>Moneda</b></td><td>{{ $quotation->currency }}</td></tr>
					@if($quotation->price_mode === 'bcv')
						<tr><td><b>Modo de precio</b></td><td><span class="badge badge-warning">Bolívares a BCV</span></td></tr>
						<tr><td><b>Tasa Binance</b></td><td>{{ number_format($quotation->binance_rate, 2, ',', '.') }}</td></tr>
						<tr><td><b>Tasa BCV</b></td><td>{{ number_format($quotation->bcv_rate, 2, ',', '.') }}</td></tr>
					@endif
					<tr><td><b>Creado por</b></td><td>{{ $quotation->author->getFullname() }}</td></tr>
				</table>
			</div>
			<div class="col-md-6">
				<h6 class="text-muted">DATOS DEL CLIENTE</h6>
				<table class="table table-sm table-borderless">
					<tr><td><b>Razón Social</b></td><td>{{ $quotation->client_title }}</td></tr>
					<tr><td><b>RIF</b></td><td>{{ $quotation->client_document }}</td></tr>
					<tr><td><b>Dirección</b></td><td>{{ $quotation->client_address }}</td></tr>
					<tr><td><b>Teléfono</b></td><td>{{ $quotation->client_phone }}</td></tr>
				</table>
			</div>
		</div>

		{{-- PRODUCTOS --}}
		<h6 class="text-muted">PRODUCTOS</h6>
		<div class="table-responsive mb-4">
			<table class="table table-bordered table-sm">
				<thead class="bg-light">
					<tr>
						<th>Código</th>
						<th>Descripción</th>
						<th class="text-right">Cantidad</th>
						<th class="text-right">P. Unitario</th>
						<th class="text-right">Tributos (%)</th>
						<th class="text-right">Total</th>
					</tr>
				</thead>
				<tbody>
					@foreach($quotation->items as $item)
						<tr>
							<td>{{ $item->code }}</td>
							<td class="description-cell">{!! $item->description !!}</td>
							<td class="text-right">{{ number_format($item->quantity, 0, ',', '.') }}</td>
							<td class="text-right">${{ number_format($item->unit_price, 2, ',', '.') }}</td>
							<td class="text-right">{{ number_format($item->discount_percent, 2, ',', '.') }}%</td>
							<td class="text-right"><b>${{ number_format($item->total, 2, ',', '.') }}</b></td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		{{-- TOTALES --}}
		<div class="row">
			<div class="col-md-6 offset-md-6">
				<table class="table table-sm">
					<tr><td><b>Sub-Total</b></td><td class="text-right">${{ number_format($quotation->subtotal, 2, ',', '.') }}</td></tr>
					@if($quotation->discount_1 > 0)
						<tr><td>Descuento 1 ({{ number_format($quotation->discount_1, 2, ',', '.') }}%)</td><td class="text-right">-${{ number_format($quotation->discount_1_amount, 2, ',', '.') }}</td></tr>
					@endif
					@if($quotation->discount_2 > 0)
						<tr><td>Descuento 2 ({{ number_format($quotation->discount_2, 2, ',', '.') }}%)</td><td class="text-right">-${{ number_format($quotation->discount_2_amount, 2, ',', '.') }}</td></tr>
					@endif
					@if($quotation->freight > 0)
						<tr><td>Flete</td><td class="text-right">${{ number_format($quotation->freight, 2, ',', '.') }}</td></tr>
					@endif
					<tr><td><b>Total Exento</b></td><td class="text-right">${{ number_format($quotation->tax_exempt, 2, ',', '.') }}</td></tr>
					<tr><td><b>Base Imponible</b></td><td class="text-right">${{ number_format($quotation->tax_base, 2, ',', '.') }}</td></tr>
					<tr><td><b>IVA ({{ number_format($quotation->iva_rate, 2, ',', '.') }}%)</b></td><td class="text-right">${{ number_format($quotation->iva_amount, 2, ',', '.') }}</td></tr>
					@if($quotation->igtf_amount > 0)
						<tr><td>IGTF</td><td class="text-right">${{ number_format($quotation->igtf_amount, 2, ',', '.') }}</td></tr>
					@endif
					<tr class="bg-light"><td><b style="font-size: 1.2em;">TOTAL OPERACIÓN</b></td><td class="text-right"><b style="font-size: 1.2em;">${{ number_format($quotation->total, 2, ',', '.') }} {{ $quotation->currency }}</b></td></tr>
				</table>
			</div>
		</div>

		{{-- NOTAS --}}
		@if($quotation->notes)
			<hr>
			<h6 class="text-muted">NOTAS</h6>
			<p>{!! nl2br(e($quotation->notes)) !!}</p>
		@endif

		{{-- COMENTARIO DE ESTADO --}}
		@if($quotation->status_comment)
			<hr>
			<h6 class="text-muted">COMENTARIO SOBRE EL ESTADO</h6>
			<p>{!! nl2br(e($quotation->status_comment)) !!}</p>
		@endif
	</div>
</div>
@endsection
