<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Nota de Crédito {{ $document->formatted_number }}</title>
	<style>
		@page { size: letter portrait; margin: 0.6in 0.6in 0.6in 0.6in; }
		body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
		.header { border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 20px; }
		.header h1 { font-size: 22px; margin: 0 0 4px 0; }
		.header p { margin: 0; font-size: 10px; color: #555; }
		.top-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
		.top-table td { vertical-align: top; padding: 0; }
		.title-cell { text-align: right; }
		.title-cell .title { font-size: 18px; font-weight: bold; }
		.title-cell .meta { font-size: 11px; margin-top: 4px; }
		.boxes { width: 100%; margin: 15px 0 20px 0; border-collapse: separate; border-spacing: 12px 0; }
		.boxes td { vertical-align: top; padding: 0; }
		.box { border: 1px solid #999; padding: 10px; min-height: 90px; }
		.box .label { font-weight: bold; margin-bottom: 4px; }
		.items { width: 100%; border-collapse: collapse; margin-top: 5px; }
		.items th { text-align: left; padding: 6px 4px; border-bottom: 1px solid #333; font-size: 11px; }
		.items td { padding: 8px 4px; border-bottom: 1px solid #eee; font-size: 10px; vertical-align: top; }
		.items .right { text-align: right; }
		.reason-row td { font-weight: bold; text-align: center; padding: 12px 4px 6px; }
		.total-row td { padding-top: 20px; font-size: 14px; font-weight: bold; }
	</style>
</head>
<body>
	<div class="header">
		<h1>{{ $company['name'] }}</h1>
		<p>{{ $company['address'] }}</p>
	</div>

	<table class="top-table">
		<tr>
			<td></td>
			<td class="title-cell">
				<div class="title">Nota de Crédito</div>
				<div class="meta">#{{ $document->formatted_number }}</div>
				<div class="meta">{{ $document->created_at->format('d/m/Y') }}</div>
			</td>
		</tr>
	</table>

	<table class="boxes">
		<tr>
			<td width="60%">
				<div class="box">
					<div>{{ $document->data['client_name'] ?? '' }}</div>
					@if(!empty($document->data['client_document']))<div>{{ $document->data['client_document'] }}</div>@endif
					@if(!empty($document->data['client_phone']))<div>{{ $document->data['client_phone'] }}</div>@endif
					@if(!empty($document->data['client_address']))<div style="margin-top: 6px;">direccion: {{ $document->data['client_address'] }}</div>@endif
				</div>
			</td>
			<td width="40%">
				<div class="box">
					<div class="label">factura Afectada</div>
					<div style="font-size: 14px; font-weight: bold;">{{ $document->parent->formatted_number ?? '' }}</div>
				</div>
			</td>
		</tr>
	</table>

	<table class="items">
		<thead>
			<tr>
				<th style="width: 18%;">Item</th>
				<th>Description</th>
				<th class="right" style="width: 10%;">Quantity</th>
				<th class="right" style="width: 15%;">Price</th>
				<th class="right" style="width: 15%;">Amount</th>
			</tr>
		</thead>
		<tbody>
			@if(!empty($document->data['reason']))
				<tr class="reason-row"><td colspan="5">{{ $document->data['reason'] }}</td></tr>
			@endif
			@php $grand = 0; @endphp
			@foreach($document->data['items'] as $item)
				@php $amt = ($item['quantity'] ?? 0) * ($item['price'] ?? 0); $grand += $amt; @endphp
				<tr>
					<td>{{ $item['code'] ?? '' }}</td>
					<td>{{ $item['description'] ?? '' }}</td>
					<td class="right">{{ number_format($item['quantity'], 2, ',', '.') }}</td>
					<td class="right">${{ number_format($item['price'], 2, ',', '.') }}</td>
					<td class="right">${{ number_format($amt, 2, ',', '.') }}</td>
				</tr>
			@endforeach
			<tr class="total-row">
				<td colspan="4" class="right">Total $</td>
				<td class="right">{{ number_format($grand, 2, ',', '.') }}</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
