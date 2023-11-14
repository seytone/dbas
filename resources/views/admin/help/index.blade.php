@extends('layouts.admin')
@section('content')
	<div class="row mb-4 filters">
        <div class="col-12">
            <h1>Centro de Ayuda</h1>
		</div>
		<div class="col-12"><hr></div>
	</div>
    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                @can('manage_sales')
                    <div class="row">
                        <div class="card w-100">
                            <div class="card-header">
                                Gestión de ventas
                            </div>
                            <div class="card-body">
                                <div id="accordion">
                                    <div class="card">
                                        <div class="card-header" id="heading-1">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-1" aria-expanded="true" aria-controls="collapse-1">
                                                    Registrar una venta
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-1" class="collapse show" aria-labelledby="heading-1" data-parent="#accordion">
                                            <div class="card-body">
												<p>Primero, busca la opción "Ventas" en el menú principal. Está ubicado en la parte lateral izquierda de la plataforma.</p>
												<p>Una vez que encuentres la opción "Ventas", haz clic en ella. Esto te llevará a una nueva página con varias opciones. Busca y haz clic en en el botón "Registrar Venta".</p>
												<p>Esto abrirá un formulario donde podrás ingresar los detalles de la venta. Aquí es donde ingresarás la información del cliente, el producto que se vendió, la cantidad y el precio. Asegúrate de llenar todos los campos correctamente.</p>
												<p>¡No te preocupes por los números! El sistema se encargará de hacer todos los cálculos necesarios de forma automática basado en los detalles que ingreses de la venta y la información de los productos y servicios que vayas añadiendo. Al final, podrás ver los valores correspondientes a la venta realizada, incluyendo los totales, ganacia y comisión sobre la venta.</p>
												<p>Después de ingresar todos los detalles, solo tienes que hacer clic en "Registrar" y ¡listo! Has registrado una venta. Recuerda, si tienes alguna duda o algo no parece correcto, siempre puedes buscar ayuda o revisar nuestras guías de usuario.</p>
												<p>A continuación, tienes algunas imágenes de referencia.</p>
												<div class="row">
													<div class="col-md-4">
														<img src="{{ asset('img/help/sale1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
													</div>
													<div class="col-md-4">
														<img src="{{ asset('img/help/sale2.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
													</div>
													<div class="col-md-4">
														<img src="{{ asset('img/help/sale3.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-2">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-2" aria-expanded="false" aria-controls="collapse-2">
                                                    Consultar el detalle de una venta
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-2" class="collapse" aria-labelledby="heading-2" data-parent="#accordion">
                                            <div class="card-body">
												<p>¡Hola de nuevo! Ahora, vamos a aprender cómo consultar el detalle de una venta. Primero, dirígete al menú principal y busca la opción "Ventas". Al igual que antes, puede estar en la parte superior, lateral o en un menú desplegable.</p>
												<p>Una vez que encuentres la opción "Ventas", haz clic en ella. Esto te llevará a una nueva página con varias opciones. Esta vez, busca en el listado de ventas la que quieres consultar y haz clic en el botón azul en la parte derecha de la lista.</p>
												<p>Esto abrirá una nueva página o ventana con todos los detalles de la venta, incluyendo la información del cliente, los productos vendidos, los precios y cualquier nota o comentario que hayas añadido. Aquí puedes revisar todos los detalles y asegurarte de que todo esté correcto.</p>
												<p>Después de ajustar todos los detalles, solo tienes que hacer clic en "Guardar" y ¡listo! Has modificado una venta. Recuerda, si tienes alguna duda o algo no parece correcto, siempre puedes buscar ayuda o revisar nuestras guías de usuario.</p>
												<p>A continuación, tienes algunas imágenes de referencia.</p>
												<div class="row">
													<div class="col-md-4">
														<img src="{{ asset('img/help/sale1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
													</div>
													<div class="col-md-4">
														<img src="{{ asset('img/help/sale4.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
													</div>
													<div class="col-md-4">
														<img src="{{ asset('img/help/sale5.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-3">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-3" aria-expanded="false" aria-controls="collapse-3">
                                                    Modificar los datos de una venta
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-3" class="collapse" aria-labelledby="heading-3" data-parent="#accordion">
                                            <div class="card-body">
												<p>¡Muy bien! Ahora, vamos a aprender cómo modificar los datos de una venta. Primero, dirígete al menú principal y busca la opción "Ventas". Al igual que antes, puede estar en la parte superior, lateral o en un menú desplegable.</p>
												<p>Una vez que encuentres la opción "Ventas", haz clic en ella. Esto te llevará a una nueva página con varias opciones. Esta vez, busca en el listado de ventas la que quieres consultar y haz clic en el botón amarillo en la parte derecha de la lista.</p>
												<p>Esto abrirá una nueva página o ventana con todos los detalles de la venta, incluyendo la información del cliente, los productos vendidos, los precios y cualquier nota o comentario que hayas añadido. Aquí puedes revisar y modificar todos los datos de la venta.</p>
												<p>Para guardar hacer clic en el botón "Editar" o "Modificar". Recuerda, si tienes alguna duda o algo no parece correcto, siempre puedes buscar ayuda o revisar nuestras guías de usuario.</p>
												<p>A continuación, tienes algunas imágenes de referencia.</p>
												<div class="row">
													<div class="col-md-4">
														<img src="{{ asset('img/help/sale1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
													</div>
													<div class="col-md-4">
														<img src="{{ asset('img/help/sale6.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
													</div>
													<div class="col-md-4">
														<img src="{{ asset('img/help/sale3.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-4">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-4" aria-expanded="false" aria-controls="collapse-4">
                                                    Generar un reporte de ventas y comisiones
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-4" class="collapse" aria-labelledby="heading-4" data-parent="#accordion">
                                            <div class="card-body">
												<p>¡Hola de nuevo! Ahora, vamos a aprender cómo generar reportes de ventas. Primero, dirígete al menú principal y busca la opción "Ventas". Al igual que antes, puede estar en la parte superior, lateral o en un menú desplegable.</p>
												<p>Una vez que encuentres la opción "Ventas", haz clic en ella. Esto te llevará al módulo de ventas donde podrás filtrar las ventas según los criterios para tu reporte. Puedes elegir un rango de fechas, un vendedor específico, un cliente específico, o cualquier otro criterio que esté habilitado.</p>
												<p>Después de realizar la búsqueda con los criterios seleccionados, se listaran las ventas que coincidan con los parámetros de tu búsqueda, y tienes la opción de descargar el reporte en diferentes formatos, como PDF, CSV o Excel.</p>
												<p>Recuerda, si tienes alguna duda o algo no parece correcto, siempre puedes buscar ayuda o revisar nuestras guías de usuario.</p>
												<p>A continuación, tienes algunas imágenes de referencia.</p>
												<div class="row">
													<div class="col-md-4">
														<img src="{{ asset('img/help/sale1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
													</div>
													<div class="col-md-4">
														<img src="{{ asset('img/help/sale7.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('manage_products')
                    <div class="row">
                        <div class="card w-100">
                            <div class="card-header">
                                Gestión de inventario
                            </div>
                            <div class="card-body">
                                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Maxime, pariatur incidunt adipisci minus perspiciatis odio ipsam iure blanditiis enim doloremque veritatis necessitatibus quam ea temporibus odit, optio quia dolorem vel?</p>
                            </div>
                        </div>
                    </div>
				@endcan
				@can('manage_management')
                    <div class="row">
                        <div class="card w-100">
                            <div class="card-header">
                                Gestión de usuarios
                            </div>
                            <div class="card-body">
                                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Maxime, pariatur incidunt adipisci minus perspiciatis odio ipsam iure blanditiis enim doloremque veritatis necessitatibus quam ea temporibus odit, optio quia dolorem vel?</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card w-100">
                            <div class="card-header">
                                Gestión de permisos
                            </div>
                            <div class="card-body">
                                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Maxime, pariatur incidunt adipisci minus perspiciatis odio ipsam iure blanditiis enim doloremque veritatis necessitatibus quam ea temporibus odit, optio quia dolorem vel?</p>
                            </div>
                        </div>
                    </div>
				@endcan
            </div>
        </div>
    @endsection
@section('scripts')
    @parent
    <script>
        $(function() {

        });
        $('[data-toggle="tooltip"]').tooltip()
    </script>
@endsection
