@extends('layouts.admin')

@section('styles')
	@parent
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5
	.0/dist/fancybox/fancybox.min.css" />
@endsection

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
				<div class="row">
					<div class="card w-100">
						<div class="card-header">
							Inducción
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-6 offset-md-3">
									<div class="embed-responsive embed-responsive-16by9">
										@if (Auth::user()->hasRole('Superadmin'))
											<iframe src="https://drive.google.com/file/d/1sUiBl2DJYNi0OpgcZ2RXDiojlUsrK-1c/preview" class="embed-responsive-item" allow="autoplay" allowfullscreen></iframe>
										@else
											<iframe src="https://drive.google.com/file/d/1kLb6n-ruCI1_JFoXrppxsJMExoYrVZ29/preview" class="embed-responsive-item" allow="autoplay" allowfullscreen></iframe>
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				{{-- @can('view_dashboard')
                    <div class="row">
                        <div class="card w-100">
                            <div class="card-header">
                                Dashboard
                            </div>
                            <div class="card-body">
                                <div id="accordion-dashboard">
                                    <div class="card">
                                        <div class="card-header" id="heading-dashboard-1">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-dashboard-1" aria-expanded="false" aria-controls="collapse-dashboard-1">
                                                    Métricas generales
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-dashboard-1" class="collapse" aria-labelledby="heading-dashboard-1" data-parent="#accordion-dashboard">
                                            <div class="card-body">
												<p></p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/help1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
								</div>
                            </div>
                        </div>
                    </div>
				@endcan --}}
                @can('manage_sales')
                    <div class="row">
                        <div class="card w-100">
                            <div class="card-header">
                                Ventas
                            </div>
                            <div class="card-body">
                                <div id="accordion-sales">
                                    <div class="card">
                                        <div class="card-header" id="heading-sales-1">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-sales-1" aria-expanded="false" aria-controls="collapse-sales-1">
                                                    Registrar una venta
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-sales-1" class="collapse" aria-labelledby="heading-sales-1" data-parent="#accordion-sales">
                                            <div class="card-body">
												<p>Primero, busca la opción "Ventas" en el menú principal. Está ubicado en la parte lateral izquierda de la plataforma.</p>
												<p>Una vez que encuentres la opción "Ventas", haz clic en ella. Esto te llevará a una nueva página con varias opciones. Busca y haz clic en en el botón "Registrar Venta".</p>
												<p>Esto abrirá un formulario donde podrás ingresar los detalles de la venta. Aquí es donde ingresarás la información del cliente, el producto que se vendió, la cantidad y el precio. Asegúrate de llenar todos los campos correctamente.</p>
												<p>¡No te preocupes por los números! El sistema se encargará de hacer todos los cálculos necesarios de forma automática basado en los detalles que ingreses de la venta y la información de los productos y servicios que vayas añadiendo. Al final, podrás ver los valores correspondientes a la venta realizada, incluyendo los totales, ganacia y comisión sobre la venta.</p>
												<p>Después de ingresar todos los detalles, solo tienes que hacer clic en "Registrar" y ¡listo! Has registrado una venta. Recuerda, si tienes alguna duda o algo no parece correcto, siempre puedes buscar ayuda o revisar nuestras guías de usuario.</p>
												<p>A continuación, tienes algunas imágenes de referencia.</p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/sale1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/sale1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/sale2.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/sale2.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/sale3.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/sale3.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-sales-2">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-sales-2" aria-expanded="false" aria-controls="collapse-sales-2">
                                                    Consultar el detalle de una venta
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-sales-2" class="collapse" aria-labelledby="heading-sales-2" data-parent="#accordion-sales">
                                            <div class="card-body">
												<p>¡Hola de nuevo! Ahora, vamos a aprender cómo consultar el detalle de una venta. Primero, dirígete al menú principal y busca la opción "Ventas". Al igual que antes, puede estar en la parte superior, lateral o en un menú desplegable.</p>
												<p>Una vez que encuentres la opción "Ventas", haz clic en ella. Esto te llevará a una nueva página con varias opciones. Esta vez, busca en el listado de ventas la que quieres consultar y haz clic en el botón azul en la parte derecha de la lista.</p>
												<p>Esto abrirá una nueva página o ventana con todos los detalles de la venta, incluyendo la información del cliente, los productos vendidos, los precios y cualquier nota o comentario que hayas añadido. Aquí puedes revisar todos los detalles y asegurarte de que todo esté correcto.</p>
												<p>Después de ajustar todos los detalles, solo tienes que hacer clic en "Guardar" y ¡listo! Has modificado una venta. Recuerda, si tienes alguna duda o algo no parece correcto, siempre puedes buscar ayuda o revisar nuestras guías de usuario.</p>
												<p>A continuación, tienes algunas imágenes de referencia.</p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/sale1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/sale1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/sale4.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/sale4.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/sale5.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/sale5.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-sales-3">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-sales-3" aria-expanded="false" aria-controls="collapse-sales-3">
                                                    Modificar los datos de una venta
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-sales-3" class="collapse" aria-labelledby="heading-sales-3" data-parent="#accordion-sales">
                                            <div class="card-body">
												<p>¡Muy bien! Ahora, vamos a aprender cómo modificar los datos de una venta. Primero, dirígete al menú principal y busca la opción "Ventas". Al igual que antes, puede estar en la parte superior, lateral o en un menú desplegable.</p>
												<p>Una vez que encuentres la opción "Ventas", haz clic en ella. Esto te llevará a una nueva página con varias opciones. Esta vez, busca en el listado de ventas la que quieres consultar y haz clic en el botón amarillo en la parte derecha de la lista.</p>
												<p>Esto abrirá una nueva página o ventana con todos los detalles de la venta, incluyendo la información del cliente, los productos vendidos, los precios y cualquier nota o comentario que hayas añadido. Aquí puedes revisar y modificar todos los datos de la venta.</p>
												<p>Para guardar hacer clic en el botón "Editar" o "Modificar". Recuerda, si tienes alguna duda o algo no parece correcto, siempre puedes buscar ayuda o revisar nuestras guías de usuario.</p>
												<p>A continuación, tienes algunas imágenes de referencia.</p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/sale1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/sale1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/sale6.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/sale6.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/sale3.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/sale3.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-sales-4">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-sales-4" aria-expanded="false" aria-controls="collapse-sales-4">
                                                    Generar un reporte de ventas y comisiones
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-sales-4" class="collapse" aria-labelledby="heading-sales-4" data-parent="#accordion-sales">
                                            <div class="card-body">
												<p>¡Hola de nuevo! Ahora, vamos a aprender cómo generar reportes de ventas. Primero, dirígete al menú principal y busca la opción "Ventas". Al igual que antes, puede estar en la parte superior, lateral o en un menú desplegable.</p>
												<p>Una vez que encuentres la opción "Ventas", haz clic en ella. Esto te llevará al módulo de ventas donde podrás filtrar las ventas según los criterios para tu reporte. Puedes elegir un rango de fechas, un vendedor específico, un cliente específico, o cualquier otro criterio que esté habilitado.</p>
												<p>Después de realizar la búsqueda con los criterios seleccionados, se listaran las ventas que coincidan con los parámetros de tu búsqueda, y tienes la opción de descargar el reporte en diferentes formatos, como PDF, CSV o Excel.</p>
												<p>Recuerda, si tienes alguna duda o algo no parece correcto, siempre puedes buscar ayuda o revisar nuestras guías de usuario.</p>
												<p>A continuación, tienes algunas imágenes de referencia.</p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/sale1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/sale1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/sale7.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/sale7.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
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
				{{-- @can('manage_clients')
                    <div class="row">
                        <div class="card w-100">
                            <div class="card-header">
                                Clientes
                            </div>
                            <div class="card-body">
                                <div id="accordion-clients">
                                    <div class="card">
                                        <div class="card-header" id="heading-clients-1">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-clients-1" aria-expanded="false" aria-controls="collapse-clients-1">
                                                    Creación de cliente
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-clients-1" class="collapse" aria-labelledby="heading-clients-1" data-parent="#accordion-clients">
                                            <div class="card-body">
												<p></p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/help1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help2.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help2.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help3.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help3.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
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
                                Inventario
                            </div>
                            <div class="card-body">
                                <div id="accordion-inventory">
                                    <div class="card">
                                        <div class="card-header" id="heading-inventory-1">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-inventory-1" aria-expanded="false" aria-controls="collapse-inventory-1">
                                                    Módulo de categorías
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-inventory-1" class="collapse" aria-labelledby="heading-inventory-1" data-parent="#accordion-inventory">
                                            <div class="card-body">
												<p></p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/help1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help2.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help2.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help3.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help3.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-inventory-2">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-inventory-2" aria-expanded="false" aria-controls="collapse-inventory-2">
                                                    Módulo de marcas
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-inventory-2" class="collapse" aria-labelledby="heading-inventory-2" data-parent="#accordion-inventory">
                                            <div class="card-body">
												<p></p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/help1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help2.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help2.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help3.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help3.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-inventory-3">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-inventory-3" aria-expanded="false" aria-controls="collapse-inventory-3">
                                                    Módulo de productos
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-inventory-3" class="collapse" aria-labelledby="heading-inventory-3" data-parent="#accordion-inventory">
                                            <div class="card-body">
												<p></p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/help1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help2.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help2.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help3.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help3.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
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
				@can('manage_management')
                    <div class="row">
                        <div class="card w-100">
                            <div class="card-header">
                                Administración
                            </div>
                            <div class="card-body">
                                <div id="accordion-management">
                                    <div class="card">
                                        <div class="card-header" id="heading-management-1">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-management-1" aria-expanded="false" aria-controls="collapse-management-1">
                                                    Gestión de usuarios
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-management-1" class="collapse" aria-labelledby="heading-management-1" data-parent="#accordion-management">
                                            <div class="card-body">
												<p></p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/help1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help2.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help2.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help3.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help3.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-management-2">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-management-2" aria-expanded="false" aria-controls="collapse-management-2">
                                                    Gestión de vendedores
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-management-2" class="collapse" aria-labelledby="heading-management-2" data-parent="#accordion-management">
                                            <div class="card-body">
												<p></p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/help1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help2.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help2.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help3.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help3.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-management-3">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-management-3" aria-expanded="false" aria-controls="collapse-management-3">
                                                    Gestión de permisos
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-management-3" class="collapse" aria-labelledby="heading-management-3" data-parent="#accordion-management">
                                            <div class="card-body">
												<p></p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/help1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help2.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help2.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help3.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help3.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-management-4">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-management-4" aria-expanded="false" aria-controls="collapse-management-4">
                                                    Gestión de roles
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-management-4" class="collapse" aria-labelledby="heading-management-4" data-parent="#accordion-management">
                                            <div class="card-body">
												<p></p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/help1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help2.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help2.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help3.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help3.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
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
				@can('manage_profile')
                    <div class="row">
                        <div class="card w-100">
                            <div class="card-header">
                                Cuenta
                            </div>
                            <div class="card-body">
                                <div id="accordion-profile">
                                    <div class="card">
                                        <div class="card-header" id="heading-profile-1">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-profile-1" aria-expanded="false" aria-controls="collapse-profile-1">
                                                    Cambio de contraseña
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-profile-1" class="collapse" aria-labelledby="heading-profile-1" data-parent="#accordion-profile">
                                            <div class="card-body">
												<p></p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/help1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help1.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help2.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help2.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/help3.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/help3.jpg') }}" alt="Sale" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
								</div>
                            </div>
                        </div>
                    </div>
				@endcan --}}
				@can('manage_payroll')
                    <div class="row">
                        <div class="card w-100">
                            <div class="card-header">
                                RRHH
                            </div>
                            <div class="card-body">
                                <div id="accordion-sales">
                                    <div class="card">
                                        <div class="card-header" id="heading-hours-1">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-hours-1" aria-expanded="false" aria-controls="collapse-hours-1">
                                                    Importar archivo de asistencias
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-hours-1" class="collapse" aria-labelledby="heading-hours-1" data-parent="#accordion-sales">
                                            <div class="card-body">
												<p>Primero, busca la opción "RRHH" en el menú principal. Está ubicado en la parte lateral izquierda de la plataforma.</p>
												<p>Una vez que encuentres la opción "RRHH", haz clic en ella. Esto te llevará a una nueva página con varias opciones. Busca y haz clic en en el botón "Importar Registros". Esto te permitirá bucar en tu equipo y seleccionar el archivo excel que vas a importar.</p>
												<p>El sistema se encargará de hacer todos los cálculos necesarios de forma automática basado en los registros del archivo importado. Al final, podrás ver los valores correspondiente a cada uno de los empleados.</p>
												<p>A continuación, tienes algunas imágenes de referencia.</p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/hour1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/hour1.jpg') }}" alt="Hours" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/hour2.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/hour2.jpg') }}" alt="Hours" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-hours-2">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-hours-2" aria-expanded="false" aria-controls="collapse-hours-2">
                                                    Visualizar listado de horas extras
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-hours-2" class="collapse" aria-labelledby="heading-hours-2" data-parent="#accordion-sales">
                                            <div class="card-body">
												<p>Una vez que hayas cargado por primera vez el archivo de registro de asistencias, verás la lista de empleados con su respectivo control de horas del periodo para cada uno.</p>
												<p>En este listado verás varios datos por cada empleado y 2 opciones para ejecutar.</p>
												<p>La columna "Horas Extras" muestra el total de horas extras del periodo según el registro de asistencia. Mientras que la columna "Total Extras" muestra el monto total a pagar en función de la cantidad de horas extras calculadas y la tárifa horaria de cada empleado. Este útimo dato es administrado desde la sección de empleados y DEBE existir previamente antes de importar el archivo excel.</p> 
												<p>A continuación, tienes algunas imágenes de referencia.</p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/hour1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/hour1.jpg') }}" alt="Hours" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/hour3.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/hour3.jpg') }}" alt="Hours" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/hour7.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/hour7.jpg') }}" alt="Hours" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-hours-3">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-hours-3" aria-expanded="false" aria-controls="collapse-hours-3">
                                                    Ver detalle del registro de horas extras
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-hours-3" class="collapse" aria-labelledby="heading-hours-3" data-parent="#accordion-sales">
                                            <div class="card-body">
												<p>Cuando ya tengas registros importados, vas a poder visualizar el detalle de asistencias diarias de cada empleado. Para esto, situate en la lista y ubica el botón amarillo para ver las asistencias.</p>
												<p>Una vez haces click sobre este botón, verás que se despliega un nuevo listado justo debajo del empleado seleccionado.</p>
												<p>En este listado podrás ver la fecha, la hora de entrada, la hora de salida, las horas trabajadas, las horas extras y el total a pagar por horas extras.</p>
												<p>Recuerda que este listado es solo para visualización y no permite realizar modificaciones. Si necesitas hacer ajustes, deberás hacerlo desde el archivo de registro de asistencias y volver a importarlo.</p>
												<p>A continuación, tienes algunas imágenes de referencia.</p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/hour1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/hour1.jpg') }}" alt="Hours" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/hour4.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/hour4.jpg') }}" alt="Hours" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="heading-hours-4">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-hours-4" aria-expanded="false" aria-controls="collapse-hours-4">
                                                    Añadir observaciones en el registro de asistencia
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-hours-4" class="collapse" aria-labelledby="heading-hours-4" data-parent="#accordion-sales">
                                            <div class="card-body">
												<p>Una vez tienes acceso al detalle de registros por empleado, verás un campo de texto en el que podrás añadir comentarios a cada registro de asistencia. Esto es útil para llevar un control de las horas extras que han sido justificadas o para añadir cualquier observación que consideres necesaria.</p>
												<p>Para añadir un comentario, comienza a escribir sobre el campo al lado del registro diario que quieras comentar. Una vez que hayas terminado de escribir, presiona el botón verde de al lado para guardar el comentario.</p>
												<p>Recuerda que este campo es opcional y puedes añadir comentarios a todos los registros que consideres necesarios.</p>
												<p>Ten en cuenta que debes guardar uno a uno en la medida que vas escribiendo comentarios a distintos registros.</p>
												<p>A continuación, tienes algunas imágenes de referencia.</p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/hour1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/hour1.jpg') }}" alt="Hours" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/hour4.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/hour4.jpg') }}" alt="Hours" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/hour5.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/hour5.jpg') }}" alt="Hours" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<div class="card">
                                        <div class="card-header" id="heading-hours-5">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-hours-5" aria-expanded="false" aria-controls="collapse-hours-5">
                                                    Marcar horas extras como pagadas
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapse-hours-5" class="collapse" aria-labelledby="heading-hours-5" data-parent="#accordion-sales">
                                            <div class="card-body">
												<p>En el listado de registros encontrarás un botón verde con el que podrás marcar las horas extras como pagadas. Esto es útil para llevar un control de las horas extras que ya han sido pagadas y las que aún están pendientes de pago.</p>
												<p>A continuación, tienes algunas imágenes de referencia.</p>
												<div class="row">
													<div class="col-md-4">
														<a href="{{ asset('img/help/hour1.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/hour1.jpg') }}" alt="Hours" class="my-2 img-fluid w-100">
														</a>
													</div>
													<div class="col-md-4">
														<a href="{{ asset('img/help/hour6.jpg') }}" data-fancybox data-caption="Help Image">
															<img src="{{ asset('img/help/hour6.jpg') }}" alt="Hours" class="my-2 img-fluid w-100">
														</a>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    @endsection
@section('scripts')
    @parent
	<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        $(function() {
			Fancybox.bind("[data-fancybox]", {
				// Your custom options
			});
        });
        $('[data-toggle="tooltip"]').tooltip()
    </script>
@endsection
