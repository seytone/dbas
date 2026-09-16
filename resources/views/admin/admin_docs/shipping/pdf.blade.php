<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Guía de Envío {{ $document->formatted_number }}</title>
	<style>
		/* Formato limpio como el Excel de referencia: sin header con datos
		   fiscales, título centrado grande, dos cajas de ENVIA / RECIBE
		   con label vertical a la izquierda. */
		@page { size: letter portrait; margin: 0.6in 0.7in 0.6in 0.7in; }
		body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #000; margin: 0; padding: 0; }

		.title {
			text-align: center;
			font-size: 22px;
			font-weight: bold;
			border: 2px solid #000;
			padding: 14px 8px;
			margin: 0;
		}

		.box {
			border: 2px solid #000;
			border-top: 0;
			width: 100%;
			border-collapse: collapse;
		}
		.box td {
			border: 2px solid #000;
			vertical-align: middle;
			padding: 12px 14px;
		}
		.box .label {
			width: 18%;
			text-align: center;
			font-weight: bold;
			font-size: 15px;
			text-decoration: underline;
		}
		.box .content {
			text-align: center;
			font-weight: bold;
			font-size: 13px;
			line-height: 1.7;
		}
		.box .envia { min-height: 130px; }
		.box .recibe { min-height: 260px; }
		.box .content .prefix { display: inline-block; }
	</style>
</head>
<body>
	@php $d = $document->data; @endphp

	<div class="title">COBRO A DESTINO</div>

	<table class="box">
		<tr>
			<td class="label">ENVIA</td>
			<td class="content envia">
				<div>{{ $d['sender_name'] ?? '' }}</div>
				@if(!empty($d['sender_document']))<div>{{ $d['sender_document'] }}</div>@endif
				<div>{{ $d['sender_address'] ?? '' }}</div>
				@if(!empty($d['sender_phones']))
					@foreach(preg_split('~[/,]~', $d['sender_phones']) as $phone)
						@php $phone = trim($phone); @endphp
						@if($phone)<div>{{ $phone }}</div>@endif
					@endforeach
				@endif
			</td>
		</tr>
		<tr>
			<td class="label">RECIBE</td>
			<td class="content recibe">
				{{-- No repetimos "recibe:" porque la label vertical de la izquierda
				     ya dice RECIBE — sería redundante. --}}
				<div>{{ $d['client_name'] ?? '' }}</div>
				@if(!empty($d['client_document']))<div>RIF: {{ $d['client_document'] }}</div>@endif
				@if(!empty($d['client_phone']))<div>TELEFONO: {{ $d['client_phone'] }}</div>@endif
				@if(!empty($d['client_address']))
					<div>{{ $d['shipping_company'] ?? 'ENVIO' }}: {{ $d['client_address'] }}</div>
				@endif
			</td>
		</tr>
	</table>
</body>
</html>
