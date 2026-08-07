<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Términos y Condiciones {{ $document->formatted_number }}</title>
	<style>
		@page { size: letter portrait; margin: 0.7in 0.7in 0.7in 0.7in; }
		body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; line-height: 1.5; }
		.header { border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 20px; }
		.header h1 { font-size: 22px; margin: 0 0 4px 0; }
		.header p { margin: 0; font-size: 10px; color: #555; }
		.title { text-align: center; font-weight: bold; font-size: 15px; margin: 15px 0 15px 0; }
		p.body { text-align: justify; margin: 0 0 10px 0; }
		.signature { margin-top: 40px; }
		.signature .line { border-bottom: 1px solid #333; display: inline-block; width: 200px; margin-left: 10px; }
	</style>
</head>
<body>
	@php
		$d = $document->data;
		$MESES = ['','ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];
		$now = $document->created_at;
	@endphp

	<div class="header">
		<h1>{{ $company['name'] }}</h1>
		<p>{{ $company['address'] }}</p>
	</div>

	<div class="title">Términos y Condiciones</div>

	<p class="body">
		La razón social o nombre <b>{{ $d['client_name'] ?? '' }}</b>. RIF o Cédula: <b>{{ $d['client_document'] ?? '' }}</b>
		Teléfono <b>{{ $d['client_phone'] ?? '' }}</b>, Dirección: <b>{{ $d['client_address'] ?? '' }}</b>. Se adhiere a nuestras cláusulas:
		<b>PRIMERA.</b> El cliente se compromete al uso del producto o servicio para sí mismo o un tercero bajo su nombre.
		<b>SEGUNDA.</b> El precio de la compra será según <b>ORDEN DE ENTREGA: {{ $d['delivery_order_ref'] ?? '—' }}</b>, el método de pago será en <b><u>{{ $d['payment_method'] ?? '' }}</u></b>.
		<b>TERCERA.</b> Se entregará la documentación que acredita la pertenencia del producto o servicio.
		<b>CUARTA.</b> Una vez aprobado por el cliente el presupuesto o factura proforma el mismo se compromete a realizar dichos pagos en su totalidad.
		<b>QUINTA.</b> Una vez emitidos los pagos, el cliente se compromete a realizar la compra sin derecho a devolución del dinero por causas ajenas.
		<b>SEXTA.</b> No se realizan cambios de productos pagados y recibidos por el cliente, ya que esto representa información confidencial para cada cliente, estos pasan a ser los dueños únicos del producto.
		<b>SÉPTIMA.</b> Si el cliente no cumple con las condiciones del pago de la totalidad del presupuesto se penalizará solo con la devolución del <b>50%</b> de sus abonos realizados.
		<b>OCTAVA.</b> En caso de que el cliente requiera garantía con alguno de nuestros productos, deberá comunicarse primeramente con nuestra empresa y hacer su solicitud vía correo electrónico, seguidamente el analista le indicará el procedimiento a seguir para resolver cualquier inconveniente.
		<b>NOVENA.</b> Una vez generada la nota solicitada por el cliente este se compromete a realizar el pago el mismo día, de lo contrario el vendedor podrá anularla y realizar una nueva nota con los precios actualizados.
		<b>DÉCIMA.</b> Se verifica los fondos y una vez acreditados se procede a ordenar al fabricante esto puede demorar un lapso de <b>(03) días hábiles</b>. En caso de retraso y pueda demorar unos días adicionales el vendedor deberá mantener informado al cliente e indicar los motivos.
		<b>DÉCIMA PRIMERA.</b> El cliente podrá solicitar cambio de producto siempre y cuando se demuestre vía correo electrónico que se recibió un producto diferente a lo cotizado por el vendedor de la empresa.
		<b>DÉCIMA SEGUNDA.</b> Para que la empresa pueda cubrir los efectos de garantía del cliente, el mismo deberá demostrar vía correo electrónico la adquisición del producto.
		<b>DÉCIMA TERCERA.</b> Para efectos de garantía de hardware el producto deberá probarse obligatoriamente al momento de la entrega en presencia del cliente, una vez retirados de nuestras instalaciones la garantía pasa a cubrir directamente desde el fabricante.
		<b>DÉCIMA CUARTA.</b> Para retiros personales el cliente deberá presentar su documentación demostrando ser el dueño de la compra en caso de no serlo deberá presentar una autorización debidamente firmada con copia de cédula de identidad del dueño de la misma.
		<b>DÉCIMA QUINTA.</b> Las entregas de los productos pueden demorarse desde <b>(24) horas</b> según sea la orden de compra, ya que la mercancía debe ser trasladada desde nuestros depósitos hasta nuestras sedes principales.
		<b>DÉCIMA SEXTA.</b> El lapso de entrega de los productos bajo pedido podrá ser en un plazo no mayor de <b>4 a 6 semanas</b>, este tiempo se ejecutará a partir del momento en que se reciba el pago en su totalidad, si se presentase algún inconveniente con la fecha de entrega, será justificado detalladamente el motivo del retraso sin excepción.
		<b>DÉCIMA SÉPTIMA.</b> El producto digital se entrega por correo con el código de activación y el link de descarga. Algunas versiones no incluyen contrato del fabricante, sino un certificado que avale la originalidad del producto, la garantía sigue siendo amplia con nuestra empresa.
		<b>DÉCIMA OCTAVA.</b> No se recomienda almacenar en stock las licencias digitales adquiridas, actívelas en el tiempo sugerido por la empresa.
		<b>DÉCIMA NOVENA.</b> La garantía de compra de estos productos digitales debe aplicar sólo para Venezuela. En caso de aplicar para otro país deberá manifestarlo al momento de la compra, no nos hacemos responsables por uso fuera del país sin previo aviso ya que algunos productos son territoriales y otros no.
	</p>

	<p class="body">
		Declaro que acepto los términos y condiciones de este documento, en la Ciudad <b>{{ $d['sign_city'] ?? '' }}</b>, Estado <b>{{ $d['sign_state'] ?? '' }}</b>. Venezuela
	</p>

	<p class="body">Fecha, día ({{ str_pad($now->day, 2, '0', STR_PAD_LEFT) }}) mes ({{ $MESES[$now->month] }}) {{ $now->year }}.</p>

	<div class="signature">
		<p><b>Nombre/Razón Social:</b> {{ $d['client_name'] ?? '' }}</p>
		<p><b>Firma</b> <span class="line">&nbsp;</span></p>
	</div>
</body>
</html>
