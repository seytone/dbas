{{-- Guardián de sesión para los formularios de cotización.

     Mantiene viva la sesión y el token CSRF mientras el formulario está
     abierto, que era el problema original: con el formulario mucho rato
     abierto la sesión vencía y al guardar se perdía todo con un 419.

     REGLA DE ORO: esto NUNCA bloquea el guardado.

     La primera versión comprobaba la sesión antes de enviar y frenaba el
     envío si el chequeo decía que había muerto. En producción ese chequeo
     se equivocaba y dejó al equipo sin poder guardar ninguna cotización.
     El error de diseño fue darle poder de veto a una petición lateral que
     puede fallar por mil motivos ajenos a la sesión (red, proxy, dominio
     mal configurado, un balanceador).

     La red de seguridad real es del lado del servidor: si el token llega
     vencido, App\Exceptions\Handler devuelve al formulario con todos los
     datos y un mensaje claro. Por eso aquí basta con mantener la sesión
     viva y, como mucho, avisar sin estorbar.

     La URL se arma relativa (route con $absolute=false) a propósito: con la
     absoluta, un APP_URL que no coincida con el dominio o el esquema real
     manda el XHR a otro origen, la cookie de sesión no viaja y el servidor
     responde 401 aunque la sesión del usuario esté perfectamente viva. --}}
<script>
(function() {
	var HEARTBEAT_MS = 5 * 60 * 1000;
	var URL_LATIDO = "{{ route('admin.quotations.heartbeat', [], false) }}";
	var avisoMostrado = false;

	function refrescarToken(token) {
		if (!token) return;
		$('input[name="_token"]').val(token);
		$('meta[name="csrf-token"]').attr('content', token);
	}

	/**
	 * Avisa una sola vez, sin bloquear ni interrumpir. El usuario puede
	 * seguir trabajando y guardando: si el token está vencido de verdad, el
	 * servidor le devuelve los datos con su mensaje.
	 */
	function avisarSesionDudosa() {
		if (avisoMostrado) return;
		avisoMostrado = true;
		Swal.fire({
			toast: true,
			position: 'top-end',
			type: 'warning',
			title: 'Tu sesión pudo haber expirado',
			text: 'Guarda cuanto antes. Si falla, no perderás lo que llevas cargado.',
			showConfirmButton: false,
			timer: 8000
		});
	}

	function latir() {
		$.ajax({
			url: URL_LATIDO,
			dataType: 'json',
			cache: false,
			timeout: 15000
		}).done(function(data) {
			if (data && data.alive) refrescarToken(data.token);
		}).fail(function(xhr) {
			// Rastro para diagnosticar desde la consola del navegador si
			// esto vuelve a comportarse raro en producción.
			console.warn('[cotizaciones] latido de sesión falló',
				{ url: URL_LATIDO, status: xhr.status, respuesta: (xhr.responseText || '').slice(0, 200) });

			// Solo un 401/419 explícito sugiere que la sesión murió.
			// Cualquier otra cosa (0 por red o CORS, 5xx, timeout) no dice
			// nada sobre la sesión y se ignora.
			if (xhr.status === 401 || xhr.status === 419) avisarSesionDudosa();
		});
	}

	// Latido periódico: mantiene viva la sesión y el token al día.
	setInterval(latir, HEARTBEAT_MS);

	// Al volver a la pestaña. El navegador congela los timers en pestañas de
	// fondo y al suspender el equipo, así que el periódico por sí solo no
	// cubre el caso de dejar la cotización abierta toda la noche.
	document.addEventListener('visibilitychange', function() {
		if (!document.hidden) latir();
	});

	// Un latido al cargar deja el token fresco desde el principio, por si la
	// página llevaba rato en caché del navegador.
	latir();
})();
</script>
