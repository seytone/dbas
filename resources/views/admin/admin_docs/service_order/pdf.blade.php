<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Orden de Servicio {{ $document->formatted_number }}</title>
	<style>
		/* Una orden típica (1-3 items por tabla) entra en una sola hoja letter
		   con espaciado cómodo. Si se cargan muchos items, desborda a una
		   segunda página de forma natural. Solo 2 filas en blanco por tabla
		   (el Excel original rellenaba hasta 6 y 7). */
		@page { size: letter portrait; margin: 0.45in 0.55in 0.45in 0.55in; }
		body { font-family: 'DejaVu Sans', sans-serif; font-size: 11.5px; color: #000; margin: 0; padding: 0; }

		.head { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
		.head td { vertical-align: top; padding: 0; }
		.head .company-cell { width: 33%; }
		/* 13px y no más: a 14px el nombre de la empresa pasa a tres líneas
		   y estira todo el encabezado. */
		.head .company-cell .name { font-weight: bold; font-size: 13px; line-height: 1.35; }
		.head .company-cell .addr { font-size: 8.5px; color: #444; margin-top: 4px; line-height: 1.4; }
		.head .nota-cell { width: 45%; font-size: 11.5px; line-height: 1.6; }
		.head .nota-cell .k { font-weight: bold; }
		.head .order-cell { width: 22%; text-align: right; font-weight: bold; font-size: 13px; }

		.section-title {
			text-align: center;
			font-weight: bold;
			font-size: 12.5px;
			border: 1px solid #000;
			border-bottom: 0;
			padding: 6px;
		}
		.items { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
		.items th, .items td { border: 1px solid #000; padding: 7px; font-size: 11.5px; }
		.items th { text-align: center; font-weight: bold; }
		.items td.num { text-align: center; width: 12%; }
		.items td.qty { text-align: center; width: 14%; }
		.items tr.blank td { height: 17px; }

		.final-box { border: 1px solid #000; border-top: 0; padding: 14px 12px; min-height: 95px; text-align: center; line-height: 1.6; }

		/* page-break-inside evita que el título quede en una hoja y las
		   líneas de firma en la siguiente si algún día crece el contenido. */
		.signoff { margin-top: 26px; text-align: center; page-break-inside: avoid; }
		.signoff .title { font-weight: bold; margin-bottom: 28px; }
		.signoff .line { border-bottom: 1px solid #000; width: 250px; margin: 0 auto 26px auto; }
	</style>
</head>
<body>
	@php
		$d = $document->data;
		$productItems = $d['product_items'] ?? [];
		$inventoryItems = $d['inventory_items'] ?? [];
		// Dos filas en blanco por tabla, por si hay que completar algo a mano
		// después de imprimir — sin inflar el documento a una segunda hoja.
		$blankRows = 2;
	@endphp

	<table class="head">
		<tr>
			<td class="company-cell">
				<div class="name">{{ $company['name'] }}</div>
				<div class="addr">{{ $company['address'] }}</div>
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
			@for($i = 0; $i < $blankRows; $i++)
				<tr class="blank">
					<td class="num">{{ count($productItems) + $i + 1 }}</td>
					<td class="qty">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
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
			@for($i = 0; $i < $blankRows; $i++)
				<tr class="blank">
					<td class="num">{{ count($inventoryItems) + $i + 1 }}</td>
					<td class="qty">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
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
