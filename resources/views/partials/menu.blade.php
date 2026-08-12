<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
			@can('view_dashboard')
				<li class="nav-item">
					<a href="{{ route("admin.dashboard") }}" class="nav-link" {{ request()->is('admin/dashboard') || request()->is('admin/dashboard/*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-fw fa-tachometer-alt"></i>
						Dashboard
					</a>
				</li>
			@endcan
            @can('manage_sales')
                <li class="nav-item">
                    <a href="{{ route('admin.sales.index') }}" class="nav-link" {{ request()->is('admin/sales') || request()->is('admin/sales/*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-fw fa-line-chart"></i>
                        Ventas
                    </a>
                </li>
            @endcan
			@can('manage_quotations')
				<li class="nav-item">
					<a href="{{ route('admin.quotations.index') }}" class="nav-link {{ request()->is('admin/quotations') || request()->is('admin/quotations/*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-fw fa-file-alt"></i>
						Cotizaciones
					</a>
				</li>
			@endcan
			@can('manage_quotations')
				{{-- Explicit `open` class when any admin_docs route is active so
				     the dropdown stays expanded on /create, /show, /edit etc.,
				     not just on the exact list URL. --}}
				<li class="nav-item nav-dropdown {{ request()->is('admin/admin-docs/*') ? 'open' : '' }}">
					<a class="nav-link nav-dropdown-toggle" href="#">
						<i class="fa-fw fas fa-folder-open nav-icon"></i>
						Administrativo
					</a>
					<ul class="nav-dropdown-items">
						<li class="nav-item">
							<a href="{{ route('admin.admin_docs.index', 'invoice') }}" class="nav-link {{ request()->is('admin/admin-docs/invoice*') ? 'active' : '' }}">
								<i class="fa-fw fas fa-file-invoice nav-icon ml-4"></i>
								Nota de Entrega
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ route('admin.admin_docs.index', 'delivery_order') }}" class="nav-link {{ request()->is('admin/admin-docs/delivery_order*') ? 'active' : '' }}">
								<i class="fa-fw fas fa-truck nav-icon ml-4"></i>
								Orden de Entrega
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ route('admin.admin_docs.index', 'credit_note') }}" class="nav-link {{ request()->is('admin/admin-docs/credit_note*') ? 'active' : '' }}">
								<i class="fa-fw fas fa-undo nav-icon ml-4"></i>
								Nota de Crédito
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ route('admin.admin_docs.index', 'terms') }}" class="nav-link {{ request()->is('admin/admin-docs/terms*') ? 'active' : '' }}">
								<i class="fa-fw fas fa-file-signature nav-icon ml-4"></i>
								Términos
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ route('admin.admin_docs.index', 'exit_order') }}" class="nav-link {{ request()->is('admin/admin-docs/exit_order*') ? 'active' : '' }}">
								<i class="fa-fw fas fa-sign-out-alt nav-icon ml-4"></i>
								Orden de Salida
							</a>
						</li>
					</ul>
				</li>
			@endcan
            @can('manage_clients')
                <li class="nav-item">
					<a href="{{ route("admin.clients.index") }}" class="nav-link {{ request()->is('admin/clients') || request()->is('admin/clients/*') ? 'active' : '' }}">
						<i class="fa-fw fas fa-thumbs-up nav-icon"></i>
						Clientes
					</a>
				</li>
			@endcan
            @can('manage_products')
				<li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-list-ul nav-icon"></i>
                        Inventario
                    </a>
					<ul class="nav-dropdown-items">
						<li class="nav-item">
							<a href="{{ route("admin.categories.index") }}" class="nav-link {{ request()->is('admin/categories') || request()->is('admin/categories/*') ? 'active' : '' }}">
								<i class="fa-fw fas fa-tags nav-icon ml-4"></i>
								Categorias
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ route("admin.brands.index") }}" class="nav-link {{ request()->is('admin/brands') || request()->is('admin/brands/*') ? 'active' : '' }}">
								<i class="fa-fw fas fa-cube nav-icon ml-4"></i>
								Marcas
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ route("admin.products.index") }}" class="nav-link {{ request()->is('admin/products') || request()->is('admin/products/*') ? 'active' : '' }}">
								<i class="fa-fw fas fa-trophy nav-icon ml-4"></i>
								Productos
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ route("admin.services.index") }}" class="nav-link {{ request()->is('admin/services') || request()->is('admin/services/*') ? 'active' : '' }}">
								<i class="fa-fw fas fa-cubes nav-icon ml-4"></i>
								Servicios
							</a>
						</li>
					</ul>
				</li>
			@endcan
			@can('manage_payroll')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon"></i>
                        RRHH
                    </a>
					<ul class="nav-dropdown-items">
						@can('manage_employees')
							<li class="nav-item">
								<a href="{{ route("admin.employees.index") }}" class="nav-link {{ request()->is('admin/employees') || request()->is('admin/employees/*') ? 'active' : '' }}">
									<i class="fa-fw fas fa-user-tie nav-icon ml-4"></i>
									Empleados
								</a>
							</li>
						@endcan
						{{-- <li class="nav-item">
							<a href="{{ route("admin.brands.index") }}" class="nav-link {{ request()->is('admin/brands') || request()->is('admin/brands/*') ? 'active' : '' }}">
								<i class="fa-fw fas fa-receipt nav-icon ml-4"></i>
								Pagos
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ route("admin.products.index") }}" class="nav-link {{ request()->is('admin/products') || request()->is('admin/products/*') ? 'active' : '' }}">
								<i class="fa-fw fas fa-calendar-check nav-icon ml-4"></i>
								Asistencia
							</a>
						</li> --}}
						<li class="nav-item">
							<a href="{{ route("admin.hours.index") }}" class="nav-link {{ request()->is('admin/services') || request()->is('admin/services/*') ? 'active' : '' }}">
								<i class="fa-fw fas fa-stopwatch nav-icon ml-4"></i>
								Horas Extras
							</a>
						</li>
					</ul>
				</li>
			@endcan
            @can('manage_management')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-cogs nav-icon"></i>
                        Administración
                    </a>
                    <ul class="nav-dropdown-items">
						@can('manage_security')
							<li class="nav-item">
								<a href="{{ route("admin.permissions.index") }}" class="nav-link {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : '' }}">
									<i class="fa-fw fas fa-unlock-alt nav-icon ml-4"></i>
									Permisos
								</a>
							</li>
							<li class="nav-item">
								<a href="{{ route("admin.roles.index") }}" class="nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
									<i class="fa-fw fas fa-briefcase nav-icon ml-4"></i>
									Roles
								</a>
							</li>
						@endcan
						@can('manage_users')
							<li class="nav-item">
								<a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
									<i class="fa-fw fas fa-users nav-icon ml-4"></i>
									Usuarios
								</a>
							</li>
						@endcan
						@can('manage_sellers')
							<li class="nav-item">
								<a href="{{ route("admin.sellers.index") }}" class="nav-link {{ request()->is('admin/sellers') || request()->is('admin/sellers/*') ? 'active' : '' }}">
									<i class="fa-fw fas fa-user-circle nav-icon ml-4"></i>
									Vendedores
								</a>
							</li>
						@endcan
                    </ul>
                </li>
            @endcan
            @can('manage_profile')
				<li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-user nav-icon"></i>
                        Cuenta
                    </a>
					<ul class="nav-dropdown-items">
						<li class="nav-item">
							<a href="{{ route('auth.change_password') }}" class="nav-link" {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : '' }}">
								<i class="nav-icon fas fa-fw fa-key ml-4"></i>
								Contraseña
							</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
								<i class="nav-icon fas fa-fw fa-sign-out-alt ml-4"></i>
								Salir
							</a>
						</li>
					</ul>
				</li>
            @endcan
			<li class="nav-item">
				<a href="{{ route('admin.help') }}" class="nav-link" {{ request()->is('admin/help') || request()->is('admin/help/*') ? 'active' : '' }}">
					<i class="nav-icon fas fa-fw fa-question-circle"></i>
					Ayuda
				</a>
			</li>
            {{-- <li class="nav-item d-block d-md-none">
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <i class="nav-icon fas fa-fw fa-sign-out-alt"></i>
                    Salir
                </a>
            </li> --}}
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>