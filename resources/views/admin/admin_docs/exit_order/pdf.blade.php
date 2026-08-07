<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Orden de Salida {{ $document->formatted_number }}</title>
	<style>
		@page { size: letter portrait; margin: 0.6in 0.6in 0.6in 0.6in; }
		body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
		.header { border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 10px; }
		.header h1 { font-size: 20px; margin: 0 0 4px 0; }
		.header p { margin: 0; font-size: 10px; color: #555; }
		.meta { text-align: right; margin-top: 6px; }
		.meta div { margin: 2px 0; font-size: 11px; }
		.title { text-align: center; font-weight: bold; font-size: 15px; margin: 20px 0 15px 0; }
		.fields { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
		.fields td { padding: 5px 4px; font-size: 11px; vertical-align: bottom; }
		.fields .label { font-weight: bold; width: 20%; }
		.fields .value { border-bottom: 1px solid #333; }
		.items { width: 100%; border-collapse: collapse; margin-top: 10px; }
		.items th, .items td { border: 1px solid #333; padding: 8px 6px; font-size: 11px; vertical-align: top; }
		.items th { background: #f5f5f5; text-align: center; }
		.observations { border: 1px solid #333; margin-top: 15px; padding: 10px; min-height: 60px; font-size: 11px; }
		.observations .obs-title { text-align: center; font-weight: bold; padding-bottom: 8px; border-bottom: 1px solid #ccc; margin-bottom: 8px; }
		.signatures { margin-top: 60px; }
		.signatures p { margin: 0; }
		.signatures .line { border-bottom: 1px solid #333; display: inline-block; width: 260px; }
	</style>
</head>
<body>
	<div class="header">
		<table style="width:100%;">
			<tr>
				<td>
					<h1>{{ $company['name'] }}</h1>
					<p>{{ $company['address'] }}</p>
				</td>
				<td style="text-align:right; vertical-align: top;">
					<div><b>Nro. De Orden</b> &nbsp; {{ $document->formatted_number }}</div>
				</td>
			</tr>
		</table>
	</div>

	<div class="title">SALIDA DE MERCANCIA POR VENTA</div>

	<table class="fields">
		<tr>
			<td class="label">Fecha:</td><td class="value">{{ $document->created_at->format('d/m/Y') }}</td>
		</tr>
		<tr>
			<td class="label">Nro de Nota/Factura:</td><td class="value">{{ $document->data['invoice_ref'] ?? '' }}</td>
		</tr>
		<tr>
			<td class="label">Cliente:</td><td class="value">{{ $document->data['client_name'] ?? '' }}</td>
		</tr>
		<tr>
			<td class="label">Vendedor Responsable:</td><td class="value">{{ $document->data['seller_name'] ?? '' }}</td>
		</tr>
	</table>

	<table class="items">
		<thead>
			<tr>
				<th style="width: 15%;">CANTIDAD</th>
				<th style="width: 25%;">SKU</th>
				<th>DESCRIPCION</th>
			</tr>
		</thead>
		<tbody>
			@foreach($document->data['items'] as $item)
				<tr>
					<td style="text-align:center;">{{ number_format($item['quantity'], 0, ',', '.') }}</td>
					<td style="text-align:center;">{{ $item['sku'] ?? '' }}</td>
					<td>{{ $item['description'] ?? '' }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>

	<div class="observations">
		<div class="obs-title">ESTE CAMPO ES PARA COLOCAR CUALQUIER MERCANCIA QUE DURANTE LA CONFIGURACION SE REQUIRIO RETIRAR DESPUES DE HABER IMPRESO</div>
		{!! nl2br(e($document->data['observations'] ?? '')) !!}
	</div>

	<div class="signatures">
		<p style="margin-top: 40px;"><b>Quien Entrego Mercancia (almacen)</b></p>
		<span class="line">&nbsp;</span>
		<p style="margin-top: 30px;"><b>Quien Recibio Mercancia (vendedor)</b></p>
		<span class="line">&nbsp;</span>
	</div>
</body>
</html>
