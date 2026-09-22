<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Orden de Servicio {{ $document->formatted_number }}</title>
	<style>
		@page { size: letter portrait; margin: 0.5in 0.55in 0.5in 0.55in; }
		body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #000; margin: 0; padding: 0; }

		.head { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
		.head td { vertical-align: top; padding: 0; }
		.head .logo-cell { width: 33%; }
		.head .logo-cell img { width: 175px; }
		.head .nota-cell { width: 45%; font-size: 11px; line-height: 1.55; }
		.head .nota-cell .k { font-weight: bold; }
		.head .order-cell { width: 22%; text-align: right; font-weight: bold; font-size: 13px; }

		.section-title {
			text-align: center;
			font-weight: bold;
			font-size: 13px;
			border: 1px solid #000;
			border-bottom: 0;
			padding: 6px;
		}
		.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
		.items th, .items td { border: 1px solid #000; padding: 6px; font-size: 11px; }
		.items th { text-align: center; font-weight: bold; }
		.items td.num { text-align: center; width: 12%; }
		.items td.qty { text-align: center; width: 14%; }
		.items tr.blank td { height: 20px; }

		.final-box { border: 1px solid #000; border-top: 0; padding: 14px 10px; min-height: 150px; text-align: center; line-height: 1.6; }

		.signoff { margin-top: 45px; text-align: center; }
		.signoff .title { font-weight: bold; margin-bottom: 40px; }
		.signoff .line { border-bottom: 1px solid #000; width: 230px; margin: 0 auto 38px auto; }
	</style>
</head>
<body>
	@php
		$d = $document->data;
		$productItems = $d['product_items'] ?? [];
		$inventoryItems = $d['inventory_items'] ?? [];
		// El Excel de referencia imprime filas en blanco para poder completar
		// a mano después de imprimir: 6 en la primera tabla, 7 en la segunda.
		$productBlanks = max(0, 6 - count($productItems));
		$inventoryBlanks = max(0, 7 - count($inventoryItems));
		$logo = public_path('img/logo.png');
	@endphp

	<table class="head">
		<tr>
			<td class="logo-cell">
				@if(file_exists($logo))
					<img src="{{ $logo }}" alt="Distribuidora Bit">
				@endif
			</td>
			<td class="nota-cell">
				<div><span class="k">NOTA:</span> {{ $d['note'] ?? '' }}</div>
				<div><span class="k">CLIENTE:</span> {{ $d['client_name'] ?? '' }}</div>
				<div><span class="k">FECHA:</span> {{ $document->created_at->format('d/m/Y') }}</div>
				<div><span class="k">SOLICITADO POR:</span> {{ $d['requested_by'] ?? '' }}</div>
				<div><span class="k">ELABORADO POR:</span> {{ $d['prepared_by'] ?? '' }}</div>
				<div><span class="k">PRODUCTO:</span> {{ $d['product'] ?? '' }}</div>
			</td>
			<td class="order-cell">ORDEN {{ $document->formatted_number }}</td>
		</tr>
	</table>

	<div class="section-title">PARA AGREGAR AL PRODUCTO</div>
	<table class="items">
		<thead>
			<tr>
				<th style="width: 12%;">PEDIDO</th>
				<th style="width: 14%;">CANTIDAD</th>
				<th>DESCRIPCION</th>
			</tr>
		</thead>
		<tbody>
			@foreach($productItems as $i => $item)
				<tr>
					<td class="num">{{ $i + 1 }}</td>
					<td class="qty">{{ number_format($item['quantity'], 0, ',', '.') }}</td>
					<td>{{ $item['description'] ?? '' }}</td>
				</tr>
			@endforeach
			@for($i = 0; $i < $productBlanks; $i++)
				<tr class="blank"><td class="num">&nbsp;</td><td class="qty">&nbsp;</td><td>&nbsp;</td></tr>
			@endfor
		</tbody>
	</table>

	<div class="section-title">PARA INCLUIR EN EL INVENTARIO</div>
	<table class="items">
		<thead>
			<tr>
				<th style="width: 12%;">PEDIDO</th>
				<th style="width: 14%;">CANTIDAD</th>
				<th>DESCRIPCION</th>
			</tr>
		</thead>
		<tbody>
			@foreach($inventoryItems as $i => $item)
				<tr>
					<td class="num">{{ $i + 1 }}</td>
					<td class="qty">{{ number_format($item['quantity'], 0, ',', '.') }}</td>
					<td>{{ $item['description'] ?? '' }}</td>
				</tr>
			@endforeach
			@for($i = 0; $i < $inventoryBlanks; $i++)
				<tr class="blank"><td class="num">{{ count($inventoryItems) + $i + 1 }}</td><td class="qty">&nbsp;</td><td>&nbsp;</td></tr>
			@endfor
		</tbody>
	</table>

	<div class="section-title">CONFIGURACION FINAL</div>
	<div class="final-box">{!! nl2br(e($d['final_config'] ?? '')) !!}</div>

	<div class="signoff">
		<div class="title">REVISADO CONFORME</div>
		<div class="line">&nbsp;</div>
		<div class="line">&nbsp;</div>
	</div>
</body>
</html>
