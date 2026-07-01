<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Cotización {{ $quotation->formatted_number }}</title>
	<style>
		@page {
			size: letter portrait;
			margin: 0.7in 0.5in 0.7in 0.5in;
		}
		body {
			font-family: 'DejaVu Sans', sans-serif;
			font-size: 11px;
			color: #333;
			line-height: 1.4;
			margin: 0;
			padding: 0;
			-webkit-print-color-adjust: exact !important;
			print-color-adjust: exact !important;
		}
		* {
			-webkit-print-color-adjust: exact !important;
			print-color-adjust: exact !important;
		}

		.watermark {
			position: fixed;
			top: 38%;
			left: 15%;
			font-size: 130px;
			font-weight: bold;
			color: rgba(200, 200, 200, 0.12);
			transform: rotate(-30deg);
			z-index: -1;
			letter-spacing: 20px;
		}

		.header { border-bottom: 3px solid #192440; padding-bottom: 10px; margin-bottom: 20px; }
		.header h2 { font-size: 16px; color: #333; margin-bottom: 4px; }
		.header p { font-size: 10px; color: #666; margin: 1px 0; }

		.info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
		.info-table td { vertical-align: top; padding: 0; }
		.info-table .left-col { width: 60%; padding-right: 20px; }
		.info-table .right-col { width: 40%; }

		.client-info { width: 100%; border-collapse: collapse; }
		.client-info td { padding: 3px 5px; font-size: 11px; vertical-align: top; }
		.client-info .label { font-weight: bold; color: #555; width: 110px; }

		.presupuesto-box {
			background: rgba(245, 245, 245, 0.65);
			border: 1px solid #ddd;
			padding: 12px;
			text-align: center;
		}
		.presupuesto-box .label { font-size: 11px; color: #666; margin-bottom: 2px; }
		.presupuesto-box .number { font-size: 20px; font-weight: bold; color: #333; }
		.presupuesto-box .dates { width: 100%; margin-top: 8px; font-size: 10px; border-collapse: collapse; }
		.presupuesto-box .dates td { padding: 2px 0; }
		.presupuesto-box .dates .right { text-align: right; }

		.products-header { width: 100%; border-collapse: collapse; table-layout: fixed; }
		.products-header th {
			background: rgba(25, 36, 64, 0.92);
			color: white;
			padding: 8px;
			font-size: 10px;
			text-align: left;
		}
		.products-header .text-right { text-align: right; }

		.product-item { border-bottom: 1px solid #eee; }
		.product-item-zebra { background: rgba(250, 250, 250, 0.55); }
		.product-meta { width: 100%; border-collapse: collapse; table-layout: fixed; }
		.product-meta td {
			padding: 6px 8px;
			font-size: 10px;
			vertical-align: top;
		}
		.product-meta .text-right { text-align: right; }
		.product-description {
			/* Padding-left aligned to the Descripción column: 8% col width
			   plus ~1.5% to cover the header cell's own 8px padding. Kept as
			   a pure percentage because DomPDF discards calc() with mixed
			   units and would silently fall back to 0. */
			padding: 0 8px 8px 9.5%;
			font-size: 10px;
			word-wrap: break-word;
		}
		.product-description img {
			max-width: 180px !important;
			max-height: 180px !important;
			width: auto !important;
			height: auto !important;
			display: inline-block !important;
			margin: 4px 4px 4px 0;
			vertical-align: top;
		}
		.products-spacer { height: 20px; }

		.totals-wrapper { width: 100%; border-collapse: collapse; margin-top: 10px; }
		.totals-wrapper td { vertical-align: top; padding: 0; }
		.totals-wrapper .totals-left-col { width: 50%; padding: 5px 15px 5px 0; }
		.totals-wrapper .totals-right-col { width: 50%; padding: 5px 0 5px 15px; }

		.totals-table { width: 100%; border-collapse: collapse; }
		.totals-table td { padding: 4px 5px; font-size: 11px; }
		.totals-table .label { font-weight: bold; color: #444; }
		.totals-table .value { text-align: right; }
		.grand-total td {
			background: rgba(25, 36, 64, 0.92);
			color: white;
			font-size: 13px;
			font-weight: bold;
			padding: 8px 5px;
		}

		.notes-section {
			margin-top: 20px;
			padding: 10px 12px;
			background: rgba(249, 249, 249, 0.55);
			border: 1px solid #eee;
		}
		.notes-section .notes-title { font-weight: bold; margin-bottom: 4px; color: #444; font-size: 11px; }
		.notes-section .notes-body { font-size: 10px; color: #555; }

		.footer {
			margin-top: 25px;
			padding-top: 10px;
			border-top: 2px solid #192440;
			font-size: 10px;
			color: #666;
		}
		.footer-table { width: 100%; border-collapse: collapse; }
		.footer-table td { vertical-align: middle; }
		.footer-left { text-align: left; }
		.footer-left .ref { font-weight: bold; font-size: 11px; margin-top: 3px; }
		.footer-right { text-align: right; width: 130px; }
		.no-fiscal-label {
			display: inline-block;
			border: 1px solid #aaa;
			border-radius: 6px;
			padding: 6px 16px;
			font-size: 14px;
			font-weight: bold;
			color: #888;
			letter-spacing: 1px;
		}
	</style>
</head>
<body>
	<div class="watermark">NO FISCAL</div>

	{{-- HEADER --}}
	<div class="header">
		<h2>DISTRIBUIDORA BIT DE ACTIVACIÓN Y SERVICIOS, C.A</h2>
		<p><b>RIF:</b> J402111843</p>
		<p>Dirección Fiscal: Calle Industrial el Coliseo, C.C Coliseo, Nivel 4, Local 160, Sector Potrerito Medio Guadalupe</p>
		<p>Teléfonos: 0212.415.32.82 / 0424.182.64.08</p>
	</div>

	{{-- CLIENT INFO + PRESUPUESTO --}}
	<table class="info-table">
		<tr>
			<td class="left-col">
				<table class="client-info">
					<tr><td class="label">Razón Social:</td><td>{{ $quotation->client->title }}</td></tr>
					<tr><td class="label">RIF:</td><td>{{ $quotation->client->document }}</td></tr>
					<tr><td class="label">Dirección:</td><td>{{ $quotation->client->address }}</td></tr>
					<tr><td class="label">Teléfonos:</td><td>{{ $quotation->client->phone }}</td></tr>
				</table>
			</td>
			<td class="right-col">
				<div class="presupuesto-box">
					<div class="label">Presupuesto Nro.</div>
					<div class="number">{{ $quotation->formatted_number }}</div>
					<table class="dates">
						<tr><td><b>Fecha Emisión:</b></td><td class="right">{{ $quotation->emission_date->format('d/m/Y') }}</td></tr>
						<tr><td><b>Fecha Vencimiento:</b></td><td class="right">{{ $quotation->expiration_date->format('d/m/Y') }}</td></tr>
					</table>
				</div>
			</td>
		</tr>
	</table>

	{{-- PRODUCTS — per-item blocks so long descriptions can flow across pages
	     (DomPDF cannot split a single table row, hence the block layout). --}}
	<table class="products-header">
		<tr>
			<th style="width: 8%;">Código</th>
			<th style="width: 56%;">Descripción</th>
			<th style="width: 8%;" class="text-right">Cantidad</th>
			<th style="width: 14%;" class="text-right">P. Unitario</th>
			<th style="width: 14%;" class="text-right">Total</th>
		</tr>
	</table>
	@foreach($quotation->items as $item)
		@php
			$adjustedUnitPrice = $item->unit_price * (1 + ($item->discount_percent / 100));
		@endphp
		<div class="product-item{{ $loop->even ? ' product-item-zebra' : '' }}">
			<table class="product-meta">
				<tr>
					<td style="width: 8%;">{{ $item->code }}</td>
					<td style="width: 56%;">&nbsp;</td>
					<td style="width: 8%;" class="text-right">{{ number_format($item->quantity, 0, ',', '.') }}</td>
					<td style="width: 14%;" class="text-right">{{ number_format($adjustedUnitPrice, 2, ',', '.') }}</td>
					<td style="width: 14%;" class="text-right"><b>{{ number_format($item->total, 2, ',', '.') }}</b></td>
				</tr>
			</table>
			<div class="product-description">{!! $item->description !!}</div>
		</div>
	@endforeach
	<div class="products-spacer"></div>

	{{-- TOTALS --}}
	<table class="totals-wrapper">
		<tr>
			<td class="totals-left-col">
				<table class="totals-table">
					<tr><td class="label">Sub-Total:</td><td class="value">{{ number_format($quotation->subtotal, 2, ',', '.') }}</td></tr>
					@if($quotation->discount_1 > 0)
						<tr><td>Descuento 1 ({{ number_format($quotation->discount_1, 2, ',', '.') }}%)</td><td class="value">{{ number_format($quotation->discount_1_amount, 2, ',', '.') }}</td></tr>
					@endif
					@if($quotation->discount_2 > 0)
						<tr><td>Descuento 2 ({{ number_format($quotation->discount_2, 2, ',', '.') }}%)</td><td class="value">{{ number_format($quotation->discount_2_amount, 2, ',', '.') }}</td></tr>
					@endif
					@if($quotation->freight > 0)
						<tr><td>Flete</td><td class="value">{{ number_format($quotation->freight, 2, ',', '.') }}</td></tr>
					@endif
				</table>
			</td>
			<td class="totals-right-col">
				<table class="totals-table">
					<tr><td class="label">Total Exento:</td><td class="value">{{ number_format($quotation->tax_exempt, 2, ',', '.') }}</td></tr>
					<tr><td class="label">Total Base Imponible:</td><td class="value">{{ number_format($quotation->tax_base, 2, ',', '.') }}</td></tr>
					<tr><td class="label">Total Impuesto {{ number_format($quotation->iva_rate, 2, ',', '.') }}%</td><td class="value">{{ number_format($quotation->iva_amount, 2, ',', '.') }}</td></tr>
					@if($quotation->igtf_amount > 0)
						<tr><td>Total IGTF</td><td class="value">{{ number_format($quotation->igtf_amount, 2, ',', '.') }}</td></tr>
					@endif
					<tr class="grand-total"><td>Total Operación</td><td class="value">{{ number_format($quotation->total, 2, ',', '.') }}</td></tr>
				</table>
			</td>
		</tr>
	</table>

	{{-- NOTES --}}
	@if($quotation->notes)
		<div class="notes-section">
			<div class="notes-title">Nota:</div>
			<div class="notes-body">{!! nl2br(e($quotation->notes)) !!}</div>
		</div>
	@endif

	{{-- FOOTER --}}
	<div class="footer">
		<table class="footer-table">
			<tr>
				<td class="footer-left">
					<p>Presupuesto expresado en: {{ $quotation->currency == 'USD' ? 'Dólar' : 'Bolívares' }}</p>
					@if($quotation->price_mode === 'bcv')
						<p>Precios calculados para pago en Bolívares a tasa BCV: {{ number_format($quotation->bcv_rate, 2, ',', '.') }}</p>
					@endif
					<p class="ref">COTIZACIÓN #: {{ $quotation->formatted_number }}. SIN DERECHO A CRÉDITO FISCAL</p>
				</td>
				<td class="footer-right">
					<span class="no-fiscal-label">NO FISCAL</span>
				</td>
			</tr>
		</table>
	</div>

	@if(isset($autoPrint) && $autoPrint)
		<script>
			window.addEventListener('load', function() {
				window.print();
			});
		</script>
	@endif
</body>
</html>
