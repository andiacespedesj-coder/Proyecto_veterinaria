@extends('layouts.app')

@section('title', 'Empleado Dashboard')
@section('header_title', 'Atención de Clientes')

@section('content')
    <!-- Dashboard Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card clients">
            <span class="stat-label">Clientes Registrados</span>
            <span class="stat-value">{{ $totalClientes }}</span>
        </div>
        <div class="stat-card services" style="--accent: #a855f7;">
            <span class="stat-label">Mascotas Registradas</span>
            <span class="stat-value">{{ $totalMascotas }}</span>
        </div>
        <div class="stat-card sales">
            <span class="stat-label">Mis Ventas de Hoy</span>
            <span class="stat-value">{{ $misVentas }} atenciones</span>
        </div>
        <div class="stat-card empleados" style="--accent: #ec4899;">
            <span class="stat-label">Mis Servicios de Hoy</span>
            <span class="stat-value">{{ $misServicios }} atenciones</span>
        </div>
    </div>

    <!-- Quick Actions POS Cards -->
    <div class="card">
        <div class="card-title">Operaciones de Caja & Atención</div>
        <p style="color:var(--color-muted); font-size:0.9rem; margin-bottom:1.5rem;">
            Accesos directos a las actividades cotidianas de la veterinaria. Seleccione una acción para iniciar.
        </p>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            <!-- Register Client & Pet -->
            <div style="border:1px solid var(--border-color); border-radius:12px; padding:1.25rem; display:flex; flex-direction:column; gap:0.5rem; justify-content:space-between;">
                <div>
                    <h3 style="font-weight:700; margin-bottom:0.25rem; display:flex; align-items:center; gap:0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        Clientes & Mascotas
                    </h3>
                    <p style="font-size:0.85rem; color:var(--color-muted);">
                        Registre nuevos clientes en el sistema y agregue sus mascotas con su historial clínico.
                    </p>
                </div>
                <a href="{{ route('empleado.clientes') }}" class="btn btn-primary" style="margin-top:1rem;">
                    Gestionar Clientes
                </a>
            </div>

            <!-- Record POS Sale -->
            <div style="border:1px solid var(--border-color); border-radius:12px; padding:1.25rem; display:flex; flex-direction:column; gap:0.5rem; justify-content:space-between;">
                <div>
                    <h3 style="font-weight:700; margin-bottom:0.25rem; display:flex; align-items:center; gap:0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        Punto de Venta (Productos)
                    </h3>
                    <p style="font-size:0.85rem; color:var(--color-muted);">
                        Facturación rápida de productos y medicamentos. Control de inventario en tiempo real.
                    </p>
                </div>
                <a href="{{ route('empleado.venta') }}" class="btn btn-primary" style="background-color:#10b981; margin-top:1rem;">
                    Nueva Venta
                </a>
            </div>

            <!-- Record POS Service -->
            <div style="border:1px solid var(--border-color); border-radius:12px; padding:1.25rem; display:flex; flex-direction:column; gap:0.5rem; justify-content:space-between;">
                <div>
                    <h3 style="font-weight:700; margin-bottom:0.25rem; display:flex; align-items:center; gap:0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        Registro de Servicios
                    </h3>
                    <p style="font-size:0.85rem; color:var(--color-muted);">
                        Registre consultas médicas, peluquería canina, vacunas aplicadas y cobros en general.
                    </p>
                </div>
                <a href="{{ route('empleado.servicio') }}" class="btn btn-primary" style="background-color:#6366f1; margin-top:1rem;">
                    Nuevo Servicio
                </a>
            </div>
        </div>
    </div>
@endsection
