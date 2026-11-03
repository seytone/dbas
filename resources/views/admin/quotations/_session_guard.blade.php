{{-- Guardián de sesión para los formularios de cotización.

     Problema que resuelve: con el formulario mucho rato abierto la sesión
     vencía, el token CSRF quedaba obsoleto y al guardar se perdía todo con un
     419 "Page Expired" sin mensaje ni rastro en el log.

     Tres defensas, de la más suave a la más firme:

       1. Latido periódico que mantiene viva la sesión y refresca el _token.
       2. Latido extra al volver a la pestaña: el navegador congela los timers
          en pestañas de fondo y al suspender el equipo, así que el latido
          periódico por sí solo no basta.
       3. Comprobación justo antes de enviar. Si la sesión murió se frena el
          envío y se avisa ANTES de que el usuario pierda nada.

     Detalle importante: la ruta del latido está detrás del middleware 'auth'.
     Si la sesión murió, Laravel responde un redirect al login que XHR sigue
     en silencio y entrega un 200 con HTML. Por eso no alcanza con mirar si la
     petición falló — hay que confirmar que la respuesta sea JSON con
     alive=true. Ese era el fallo del latido anterior, que además se tragaba
     los errores con un .fail() vacío. --}}
<script>
(function() {
	var HEARTBEAT_MS = 5 * 60 * 1000;
	var avisoAbierto = false;

	function refrescarToken(token) {
		if (!token) return;
		$('input[name="_token"]').val(token);
		$('meta[name="csrf-token"]').attr('content', token);
	}

	function pingSession(onResult) {
		$.ajax({
			url: "{{ route('admin.quotations.heartbeat') }}",
			dataType: 'text',
			cache: false,
			timeout: 15000
		}).done(function(raw) {
			var data = null;
			try { data = JSON.parse(raw); } catch (err) { data = null; }
			// Si no es JSON válido, lo que llegó es la página de login:
			// XHR siguió el redirect y la sesión está muerta.
			var viva = !!(data && data.alive);
			if (viva) refrescarToken(data.token);
			onResult(viva);
		}).fail(function(xhr) {
			if (xhr.status === 401 || xhr.status === 419) { onResult(false); return; }
			// Un fallo de red o del servidor no prueba que la sesión haya
			// muerto. Ante la duda dejamos pasar el envío: frenar uno válido
			// es peor que intentarlo, porque ahora el servidor devuelve los
			// datos si el token resulta estar vencido.
			onResult(true);
		});
	}

	function avisarSesionCaida(alReintentar) {
		if (avisoAbierto) return;
		avisoAbierto = true;
		Swal.fire({
			type: 'warning',
			title: 'Tu sesión expiró',
			html: 'No se envió nada y <b>los datos siguen cargados en esta pantalla</b>.<br><br>'
				+ 'Inicia sesión de nuevo en otra pestaña y vuelve aquí para guardar.',
			showCancelButton: true,
			confirmButtonText: 'Ya inicié sesión, reintentar',
			cancelButtonText: 'Cerrar',
			allowOutsideClick: false
		}).then(function(result) {
			avisoAbierto = false;
			// SweetAlert2 v8: la confirmación llega en result.value.
			if (!result.value) return;
			pingSession(function(viva) {
				if (!viva) { avisarSesionCaida(alReintentar); return; }
				Swal.fire({
					type: 'success',
					title: 'Sesión recuperada',
					text: 'Ya puedes guardar la cotización.',
					timer: 2200,
					showConfirmButton: false
				});
				if (typeof alReintentar === 'function') alReintentar();
			});
		});
	}

	// 1. Latido periódico.
	setInterval(function() { pingSession(function() {}); }, HEARTBEAT_MS);

	// 2. Al volver a la pestaña.
	document.addEventListener('visibilitychange', function() {
		if (document.hidden) return;
		pingSession(function(viva) { if (!viva) avisarSesionCaida(null); });
	});

	// 3. Antes de enviar. Este partial se incluye al final de la sección de
	//    scripts, así que este handler corre DESPUÉS del de validación del
	//    formulario: cuando llega aquí, la preparación de campos ya se hizo.
	var $form = $('#quotation-form');
	$form.on('submit', function(e) {
		// El handler de validación ya frenó el envío por su cuenta.
		if (e.isDefaultPrevented()) return;

		e.preventDefault();
		var $btn = $form.find('button[type="submit"]');
		$btn.prop('disabled', true);

		pingSession(function(viva) {
			$btn.prop('disabled', false);
			if (!viva) {
				avisarSesionCaida(function() { $form[0].submit(); });
				return;
			}
			// Envío nativo: no vuelve a disparar los handlers de jQuery, así
			// la preparación del formulario no se ejecuta dos veces.
			$form[0].submit();
		});
	});
})();
</script>
