@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('header_title', 'Panel de Administración')

@section('content')
    <!-- Dashboard Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card sales">
            <span class="stat-label">Total Ventas (Productos)</span>
            <span class="stat-value">Bs. {{ number_format($totalVentas, 2) }}</span>
        </div>
        <div class="stat-card services">
            <span class="stat-label">Total Ingreso Servicios</span>
            <span class="stat-value">Bs. {{ number_format($totalServicios, 2) }}</span>
        </div>
        <div class="stat-card clients">
            <span class="stat-label">Total Clientes</span>
            <span class="stat-value">{{ $totalClientes }}</span>
        </div>
        <div class="stat-card empleados">
            <span class="stat-label">Usuarios del Sistema</span>
            <span class="stat-value">{{ $totalEmpleados }}</span>
        </div>
    </div>

    <!-- Main Dashboard Section -->
    <div class="dashboard-sections">
        <!-- Recent Sales Card -->
        <div class="card">
            <div class="card-title">
                <span>Ventas Recientes</span>
                <span style="font-size:0.8rem; font-weight:normal; color:var(--color-muted);">Últimas 5 ventas</span>
            </div>

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Empleado</th>
                            <th>Total</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recientesVentas as $v)
                            <tr>
                                <td>#{{ $v->id_venta }}</td>
                                <td>{{ \Carbon\Carbon::parse($v->fecha)->format('d/m/Y') }}</td>
                                <td>{{ $v->cliente->nombre ?? 'N/A' }} {{ $v->cliente->paterno ?? '' }}</td>
                                <td>{{ $v->usuario->nombre ?? 'N/A' }}</td>
                                <td>Bs. {{ number_format($v->monto, 2) }}</td>
                                <td>
                                    <a href="{{ route('empleado.venta.ticket', $v->id_venta) }}" class="btn btn-secondary btn-sm" target="_blank">
                                        Ver Ticket
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--color-muted);">No hay ventas registradas recientemente.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Access Card -->
        <div class="card">
            <div class="card-title">Accesos Rápidos</div>
            <div style="display:flex; flex-direction:column; gap:0.75rem;">
                <a href="{{ route('admin.empleados') }}" class="btn btn-primary" style="justify-content:flex-start;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Gestionar Empleados
                </a>
                <a href="{{ route('admin.inventario') }}" class="btn btn-secondary" style="justify-content:flex-start;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    Control de Inventario
                </a>
                <a href="{{ route('admin.reportes') }}" class="btn btn-secondary" style="justify-content:flex-start;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    Generar Reportes
                </a>
            </div>
        </div>
    </div>
@endsection
