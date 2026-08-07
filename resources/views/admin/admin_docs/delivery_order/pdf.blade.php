<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Orden de Entrega {{ $document->formatted_number }}</title>
	<style>
		@page { size: letter portrait; margin: 0.6in 0.6in 0.6in 0.6in; }
		body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
		.header { border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 20px; }
		.header h1 { font-size: 22px; margin: 0 0 4px 0; }
		.header p { margin: 0; font-size: 10px; color: #555; }
		.meta { text-align: right; margin: 5px 0 20px 0; }
		.meta .kind { color: #d18a2a; font-weight: bold; font-size: 13px; }
		.meta .num { font-weight: bold; font-size: 13px; }
		.client-info { margin: 20px 0 30px 0; padding-left: 20px; }
		.client-info div { margin: 2px 0; }
		.items { width: 100%; border-collapse: collapse; margin: 30px 0; }
		.items th, .items td { border: 1px solid #999; padding: 10px 6px; }
		.items th { text-align: center; background: #f5f5f5; font-size: 11px; }
		.items td { vertical-align: middle; text-align: center; font-size: 11px; }
		.signatures { margin-top: 80px; width: 100%; border-collapse: collapse; }
		.signatures td { text-align: center; padding: 15px 5px; width: 50%; }
		.signatures .line { border-top: 1px solid #333; margin: 5px 40px 0 40px; padding-top: 4px; }
	</style>
</head>
<body>
	<div class="header">
		<h1>{{ $company['name'] }}</h1>
		<p>{{ $company['address'] }}</p>
	</div>

	<div class="meta">
		<div><span class="kind">ORDEN DE ENTREGA</span> &nbsp; <span class="num">{{ $document->formatted_number }}</span></div>
		<div><b>Fecha emisión</b> &nbsp; {{ $document->created_at->format('d/m/Y') }}</div>
	</div>

	<div class="client-info">
		<div>{{ $document->data['client_name'] ?? '' }}</div>
		@if(!empty($document->data['client_document']))<div>Rif: {{ $document->data['client_document'] }}</div>@endif
		@if(!empty($document->data['client_phone']))<div>Número de teléfono: {{ $document->data['client_phone'] }}</div>@endif
		@if(!empty($document->data['client_address']))<div>Dirección: {{ $document->data['client_address'] }}</div>@endif
	</div>

	<table class="items">
		<thead>
			<tr>
				<th style="width: 15%;">CANTIDAD</th>
				<th>DESCRIPCION</th>
				<th style="width: 25%;">SERIAL</th>
			</tr>
		</thead>
		<tbody>
			@foreach($document->data['items'] as $item)
				<tr>
					<td>{{ number_format($item['quantity'], 0, ',', '.') }}</td>
					<td>{{ $item['description'] ?? '' }}</td>
					<td><b>{{ $item['serial'] ?? '' }}</b></td>
				</tr>
			@endforeach
		</tbody>
	</table>

	<table class="signatures">
		<tr>
			<td>
				<b>Recibe</b>
				<div class="line">&nbsp;</div>
			</td>
			<td>
				<b>Entrega</b>
				<div class="line">&nbsp;</div>
			</td>
		</tr>
	</table>
</body>
</html>
