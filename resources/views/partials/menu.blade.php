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