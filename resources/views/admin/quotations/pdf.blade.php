<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Cotización {{ $quotation->formatted_number }}</title>
	<style>
		* { margin: 0; padding: 0; box-sizing: border-box; }
		body { font-family: Arial, sans-serif; font-size: 11px; color: #333; position: relative; }

		.watermark {
			position: fixed;
			top: 40%;
			left: 15%;
			font-size: 90px;
			font-weight: bold;
			color: rgba(200, 200, 200, 0.25);
			transform: rotate(-45deg);
			z-index: -1;
			letter-spacing: 15px;
		}

		.header { border-bottom: 3px solid #4a9a5c; padding-bottom: 10px; margin-bottom: 15px; }
		.header h2 { font-size: 16px; color: #333; margin-bottom: 3px; }
		.header p { font-size: 10px; color: #666; margin: 1px 0; }

		.info-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
		.info-table td { padding: 4px 8px; font-size: 11px; vertical-align: top; }
		.info-table .label { font-weight: bold; color: #555; width: 120px; }
		.info-table .right-col { text-align: right; }

		.presupuesto-box { background: #f5f5f5; border: 1px solid #ddd; padding: 8px; text-align: center; }
		.presupuesto-box .number { font-size: 18px; font-weight: bold; color: #333; }
		.presupuesto-box .label { font-size: 10px; color: #666; }

		.products-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
		.products-table th { background: #4a9a5c; color: white; padding: 6px 8px; font-size: 10px; text-align: left; }
		.products-table td { padding: 5px 8px; border-bottom: 1px solid #eee; font-size: 10px; }
		.products-table tr:nth-child(even) { background: #fafafa; }
		.products-table .text-right { text-align: right; }
		.products-table .description { max-width: 250px; word-wrap: break-word; }

		.totals-section { width: 100%; margin-top: 10px; }
		.totals-left { width: 50%; vertical-align: top; }
		.totals-right { width: 50%; vertical-align: top; }

		.totals-table { width: 100%; border-collapse: collapse; }
		.totals-table td { padding: 4px 8px; font-size: 11px; }
		.totals-table .total-label { font-weight: bold; }
		.totals-table .total-value { text-align: right; }
		.totals-table .grand-total { background: #4a9a5c; color: white; font-size: 13px; font-weight: bold; }

		.notes-section { margin-top: 15px; padding: 8px; background: #f9f9f9; border: 1px solid #eee; font-size: 10px; }
		.notes-section .title { font-weight: bold; margin-bottom: 3px; }

		.footer { margin-top: 20px; text-align: center; font-size: 9px; color: #999; border-top: 2px solid #4a9a5c; padding-top: 8px; }
		.footer .cotizacion-ref { font-size: 10px; font-weight: bold; color: #666; }
	</style>
</head>
<body>
	<div class="watermark">NO FISCAL</div>

	{{-- HEADER --}}
	<div class="header">
		<h2>DISTRIBUIDORA BIT DE ACTIVACI&Oacute;N Y SERVICIOS, C.A</h2>
		<p>Direcci&oacute;n Fiscal: Calle Industrial el Coliseo, C.C Coliseo, Nivel 4, Local 160, Sector Potrerito Medio Guadalupe</p>
		<p>Tel&eacute;fonos: 0212.415.32.82 / 0212.373.66.08</p>
	</div>

	{{-- CLIENT INFO + PRESUPUESTO NUMBER --}}
	<table class="info-table">
		<tr>
			<td style="width: 65%;">
				<table class="info-table" style="margin: 0;">
					<tr><td class="label">Raz&oacute;n Social:</td><td>{{ $quotation->client->title }}</td></tr>
					<tr><td class="label">RIF:</td><td>{{ $quotation->client->document }}</td></tr>
					<tr><td class="label">Direcci&oacute;n:</td><td>{{ $quotation->client->address }}</td></tr>
					<tr><td class="label">Tel&eacute;fonos:</td><td>{{ $quotation->client->phone }}</td></tr>
				</table>
			</td>
			<td style="width: 35%; vertical-align: top;">
				<div class="presupuesto-box">
					<div class="label">Presupuesto Nro.</div>
					<div class="number">{{ $quotation->formatted_number }}</div>
					<div style="margin-top: 8px;">
						<table style="width: 100%; font-size: 10px;">
							<tr><td><b>Fecha Emisi&oacute;n:</b></td><td style="text-align:right;">{{ $quotation->emission_date->format('d/m/Y') }}</td></tr>
							<tr><td><b>Fecha Vencimiento:</b></td><td style="text-align:right;">{{ $quotation->expiration_date->format('d/m/Y') }}</td></tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table>

	{{-- PRODUCTS TABLE --}}
	<table class="products-table">
		<thead>
			<tr>
				<th style="width: 70px;">C&oacute;digo</th>
				<th>Descripci&oacute;n</th>
				<th style="width: 60px; text-align: right;">Cantidad</th>
				<th style="width: 80px; text-align: right;">P. Unitario</th>
				<th style="width: 70px; text-align: right;">Descuento</th>
				<th style="width: 80px; text-align: right;">Total</th>
			</tr>
		</thead>
		<tbody>
			@foreach($quotation->items as $item)
				<tr>
					<td>{{ $item->code }}</td>
					<td class="description">{!! nl2br(e($item->description)) !!}</td>
					<td class="text-right">{{ number_format($item->quantity, 2, ',', '.') }}</td>
					<td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
					<td class="text-right">{{ number_format($item->discount_percent, 2, ',', '.') }}%</td>
					<td class="text-right"><b>{{ number_format($item->total, 2, ',', '.') }}</b></td>
				</tr>
			@endforeach
		</tbody>
	</table>

	{{-- TOTALS --}}
	<table class="totals-section">
		<tr>
			<td class="totals-left">
				<table class="totals-table">
					<tr><td class="total-label">Sub-Total:</td><td class="total-value">{{ number_format($quotation->subtotal, 2, ',', '.') }}</td></tr>
					@if($quotation->discount_1 > 0)
						<tr><td>Descuento 1 ({{ number_format($quotation->discount_1, 2, ',', '.') }}%)</td><td class="total-value">{{ number_format($quotation->discount_1_amount, 2, ',', '.') }}</td></tr>
					@endif
					@if($quotation->discount_2 > 0)
						<tr><td>Descuento 2 ({{ number_format($quotation->discount_2, 2, ',', '.') }}%)</td><td class="total-value">{{ number_format($quotation->discount_2_amount, 2, ',', '.') }}</td></tr>
					@endif
					@if($quotation->freight > 0)
						<tr><td>Flete</td><td class="total-value">{{ number_format($quotation->freight, 2, ',', '.') }}</td></tr>
					@endif
				</table>
			</td>
			<td class="totals-right">
				<table class="totals-table">
					<tr><td class="total-label">Total Exento:</td><td class="total-value">{{ number_format($quotation->tax_exempt, 2, ',', '.') }}</td></tr>
					<tr><td class="total-label">Total Base Imponible:</td><td class="total-value">{{ number_format($quotation->tax_base, 2, ',', '.') }}</td></tr>
					<tr><td class="total-label">Total Impuesto {{ number_format($quotation->iva_rate, 2, ',', '.') }}%</td><td class="total-value">{{ number_format($quotation->iva_amount, 2, ',', '.') }}</td></tr>
					@if($quotation->igtf_amount > 0)
						<tr><td>Total IGTF</td><td class="total-value">{{ number_format($quotation->igtf_amount, 2, ',', '.') }}</td></tr>
					@endif
					<tr class="grand-total"><td style="padding: 6px 8px;">Total Operaci&oacute;n</td><td class="total-value" style="padding: 6px 8px;">{{ number_format($quotation->total, 2, ',', '.') }}</td></tr>
				</table>
			</td>
		</tr>
	</table>

	{{-- NOTES --}}
	@if($quotation->notes)
		<div class="notes-section">
			<div class="title">Nota:</div>
			<p>{!! nl2br(e($quotation->notes)) !!}</p>
		</div>
	@endif

	{{-- FOOTER --}}
	<div class="footer">
		<p>Presupuesto expresado en: {{ $quotation->currency == 'USD' ? 'D&oacute;lar' : 'Bol&iacute;vares' }}</p>
		<p class="cotizacion-ref">COTIZACI&Oacute;N #: {{ $quotation->formatted_number }}. SIN DERECHO A CR&Eacute;DITO FISCAL</p>
	</div>
</body>
</html>
