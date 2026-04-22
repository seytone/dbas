<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Cotización {{ $quotation->formatted_number }} - Imprimir</title>
	<style>
		* { margin: 0; padding: 0; box-sizing: border-box; }
		body { font-family: Arial, sans-serif; font-size: 12px; color: #333; padding: 30px; position: relative; }

		@media print {
			body { padding: 0; }
			.no-print { display: none !important; }
		}

		.watermark {
			position: fixed;
			top: 40%;
			left: 15%;
			font-size: 100px;
			font-weight: bold;
			color: rgba(200, 200, 200, 0.2);
			transform: rotate(-45deg);
			z-index: -1;
			letter-spacing: 15px;
		}

		.print-bar { margin-bottom: 20px; padding: 10px; background: #f5f5f5; text-align: center; }
		.print-bar button { padding: 8px 20px; font-size: 14px; cursor: pointer; background: #4a9a5c; color: white; border: none; border-radius: 4px; margin: 0 5px; }
		.print-bar button:hover { background: #3d8a4f; }
		.print-bar a { padding: 8px 20px; font-size: 14px; text-decoration: none; color: #666; }

		.header { border-bottom: 3px solid #4a9a5c; padding-bottom: 10px; margin-bottom: 20px; }
		.header h2 { font-size: 18px; color: #333; margin-bottom: 3px; }
		.header p { font-size: 11px; color: #666; margin: 2px 0; }

		.info-row { display: table; width: 100%; margin-bottom: 20px; }
		.info-col { display: table-cell; vertical-align: top; }
		.info-col.left { width: 60%; }
		.info-col.right { width: 40%; }

		.info-table { width: 100%; }
		.info-table td { padding: 3px 5px; font-size: 12px; }
		.info-table .label { font-weight: bold; color: #555; width: 130px; }

		.presupuesto-box { background: #f5f5f5; border: 1px solid #ddd; padding: 12px; text-align: center; }
		.presupuesto-box .number { font-size: 22px; font-weight: bold; color: #333; }
		.presupuesto-box .label { font-size: 11px; color: #666; }

		.products-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
		.products-table th { background: #4a9a5c; color: white; padding: 8px; font-size: 11px; text-align: left; }
		.products-table td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
		.products-table tr:nth-child(even) { background: #fafafa; }
		.text-right { text-align: right; }

		.totals-row { display: table; width: 100%; margin-top: 10px; }
		.totals-col { display: table-cell; width: 50%; vertical-align: top; padding: 5px; }

		.totals-table { width: 100%; }
		.totals-table td { padding: 4px 8px; font-size: 12px; }
		.total-label { font-weight: bold; }
		.total-value { text-align: right; }
		.grand-total td { background: #4a9a5c; color: white; font-size: 14px; font-weight: bold; padding: 8px !important; }

		.notes-section { margin-top: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #eee; }
		.notes-title { font-weight: bold; margin-bottom: 5px; }

		.footer { margin-top: 25px; text-align: center; font-size: 10px; color: #999; border-top: 2px solid #4a9a5c; padding-top: 10px; }
		.footer .ref { font-size: 11px; font-weight: bold; color: #666; }
	</style>
</head>
<body>
	<div class="watermark">NO FISCAL</div>

	<div class="print-bar no-print">
		<button onclick="window.print()"><b>Imprimir</b></button>
		<a href="{{ route('admin.quotations.show', $quotation->id) }}">Volver</a>
	</div>

	<div class="header">
		<h2>DISTRIBUIDORA BIT DE ACTIVACI&Oacute;N Y SERVICIOS, C.A</h2>
		<p>Direcci&oacute;n Fiscal: Calle Industrial el Coliseo, C.C Coliseo, Nivel 4, Local 160, Sector Potrerito Medio Guadalupe</p>
		<p>Tel&eacute;fonos: 0212.415.32.82 / 0212.373.66.08</p>
	</div>

	<div class="info-row">
		<div class="info-col left">
			<table class="info-table">
				<tr><td class="label">Raz&oacute;n Social:</td><td>{{ $quotation->client->title }}</td></tr>
				<tr><td class="label">RIF:</td><td>{{ $quotation->client->document }}</td></tr>
				<tr><td class="label">Direcci&oacute;n:</td><td>{{ $quotation->client->address }}</td></tr>
				<tr><td class="label">Tel&eacute;fonos:</td><td>{{ $quotation->client->phone }}</td></tr>
			</table>
		</div>
		<div class="info-col right">
			<div class="presupuesto-box">
				<div class="label">Presupuesto Nro.</div>
				<div class="number">{{ $quotation->formatted_number }}</div>
				<table style="width: 100%; margin-top: 8px; font-size: 11px;">
					<tr><td><b>Fecha Emisi&oacute;n:</b></td><td class="text-right">{{ $quotation->emission_date->format('d/m/Y') }}</td></tr>
					<tr><td><b>Fecha Vencimiento:</b></td><td class="text-right">{{ $quotation->expiration_date->format('d/m/Y') }}</td></tr>
				</table>
			</div>
		</div>
	</div>

	<table class="products-table">
		<thead>
			<tr>
				<th style="width: 80px;">C&oacute;digo</th>
				<th>Descripci&oacute;n</th>
				<th style="width: 70px;" class="text-right">Cantidad</th>
				<th style="width: 90px;" class="text-right">P. Unitario</th>
				<th style="width: 80px;" class="text-right">Descuento</th>
				<th style="width: 90px;" class="text-right">Total</th>
			</tr>
		</thead>
		<tbody>
			@foreach($quotation->items as $item)
				<tr>
					<td>{{ $item->code }}</td>
					<td>{!! nl2br(e($item->description)) !!}</td>
					<td class="text-right">{{ number_format($item->quantity, 2, ',', '.') }}</td>
					<td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
					<td class="text-right">{{ number_format($item->discount_percent, 2, ',', '.') }}%</td>
					<td class="text-right"><b>{{ number_format($item->total, 2, ',', '.') }}</b></td>
				</tr>
			@endforeach
		</tbody>
	</table>

	<div class="totals-row">
		<div class="totals-col">
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
		</div>
		<div class="totals-col">
			<table class="totals-table">
				<tr><td class="total-label">Total Exento:</td><td class="total-value">{{ number_format($quotation->tax_exempt, 2, ',', '.') }}</td></tr>
				<tr><td class="total-label">Total Base Imponible:</td><td class="total-value">{{ number_format($quotation->tax_base, 2, ',', '.') }}</td></tr>
				<tr><td class="total-label">Total Impuesto {{ number_format($quotation->iva_rate, 2, ',', '.') }}%</td><td class="total-value">{{ number_format($quotation->iva_amount, 2, ',', '.') }}</td></tr>
				@if($quotation->igtf_amount > 0)
					<tr><td>Total IGTF</td><td class="total-value">{{ number_format($quotation->igtf_amount, 2, ',', '.') }}</td></tr>
				@endif
				<tr class="grand-total"><td>Total Operaci&oacute;n</td><td class="total-value">{{ number_format($quotation->total, 2, ',', '.') }}</td></tr>
			</table>
		</div>
	</div>

	@if($quotation->notes)
		<div class="notes-section">
			<div class="notes-title">Nota:</div>
			<p>{!! nl2br(e($quotation->notes)) !!}</p>
		</div>
	@endif

	<div class="footer">
		<p>Presupuesto expresado en: {{ $quotation->currency == 'USD' ? 'D&oacute;lar' : 'Bol&iacute;vares' }}</p>
		<p class="ref">COTIZACI&Oacute;N #: {{ $quotation->formatted_number }}. SIN DERECHO A CR&Eacute;DITO FISCAL</p>
	</div>
</body>
</html>
