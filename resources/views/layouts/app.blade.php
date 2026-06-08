<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Veterinaria') - Veterinaria</title>
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar no-print">
        <div class="sidebar-header">
            <!-- Simple Vet icon -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--accent);">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            <h1>Veterinaria<span>Porvenir</span></h1>
        </div>
        
        <ul class="sidebar-menu">
            @if(Auth::check())
                @if(Auth::user()->isAdmin())
                    <!-- Admin Sidebar Links -->
                    <li class="sidebar-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Route::is('admin.empleados') ? 'active' : '' }}">
                        <a href="{{ route('admin.empleados') }}">
                            <span>Gestionar Empleados</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Route::is('admin.inventario') ? 'active' : '' }}">
                        <a href="{{ route('admin.inventario') }}">
                            <span>Inventario & Stock</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Route::is('admin.reportes') ? 'active' : '' }}">
                        <a href="{{ route('admin.reportes') }}">
                            <span>Reportes</span>
                        </a>
                    </li>
                @elseif(Auth::user()->isEmpleado())
                    <!-- empleado Sidebar Links -->
                    <li class="sidebar-item {{ Route::is('empleado.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('empleado.dashboard') }}">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Route::is('empleado.clientes') ? 'active' : '' }}">
                        <a href="{{ route('empleado.clientes') }}">
                            <span>Clientes & Mascotas</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Route::is('empleado.venta') ? 'active' : '' }}">
                        <a href="{{ route('empleado.venta') }}">
                            <span>Registrar Venta</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Route::is('empleado.servicio') ? 'active' : '' }}">
                        <a href="{{ route('empleado.servicio') }}">
                            <span>Registrar Servicio</span>
                        </a>
                    </li>
                @endif
            @endif
        </ul>
        
        <div class="sidebar-footer">
            @if(Auth::check())
                <div class="user-info">
                    <span>Sesión iniciada:</span>
                    <strong>{{ Auth::user()->nombre }} {{ Auth::user()->paterno }}</strong>
                    <small style="color: var(--accent); font-weight:600;">{{ Auth::user()->rol->nombre }}</small>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                        Cerrar Sesión
                    </button>
                </form>
            @endif
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <header class="top-bar no-print">
            <div class="page-title">
                <h2>@yield('header_title', 'Panel de Control')</h2>
            </div>
            <div>
                <span style="color: var(--color-muted); font-size:0.9rem;">
                    Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                </span>
            </div>
        </header>

        <main class="content-container">
            <!-- Toast alerts -->
            @if(session('success'))
                <div class="alert alert-success">
                    <span>{{ session('success') }}</span>
                    <button class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <span>{{ session('error') }}</span>
                    <button class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
