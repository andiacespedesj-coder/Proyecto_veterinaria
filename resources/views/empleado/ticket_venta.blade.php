@extends('layouts.app')

@section('title', 'Ticket de Venta')
@section('header_title', 'Ticket de Venta #' . $venta->id_venta)

@section('content')
    <div class="no-print" style="margin-bottom:1.5rem; display:flex; gap:0.5rem; justify-content:center;">
        <button class="btn btn-primary" onclick="window.print()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:2px;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Imprimir Ticket
        </button>
        <a href="{{ route('empleado.venta') }}" class="btn btn-secondary">Volver al POS</a>
    </div>

    <div class="ticket-container">
        <div class="ticket-header">
            <div class="ticket-title">VETERINARIA "PORVENIR"</div>
            <div class="ticket-subtitle">Santa Cruz - Bolivia</div>
        </div>

        <div class="ticket-info">
            <strong>Ticket Venta #:</strong> {{ $venta->id_venta }}<br>
            <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}<br>
            <strong>Cliente:</strong> {{ $venta->cliente ? $venta->cliente->nombre . ' ' . $venta->cliente->paterno . ' (CI: ' . $venta->ci . ')' : 'Cliente ocasional' }}<br>
            <strong>Atendido por:</strong> {{ $venta->usuario->nombre }} {{ $venta->usuario->paterno }}
        </div>

        <div class="ticket-items">
            @foreach($venta->productos as $p)
                <div class="ticket-item-line">
                    <span>{{ $p->nombre }} (x{{ $p->pivot->cantidad }})</span>
                    <span>Bs. {{ number_format($p->pivot->cantidad * $p->pivot->precio, 2) }}</span>
                </div>
            @endforeach
        </div>

        <div class="ticket-total">
            <span>TOTAL:</span>
            <span>Bs. {{ number_format($venta->monto, 2) }}</span>
        </div>

        <div class="ticket-footer">
            <p>¡Gracias por su preferencia!</p>
            <p>Salud y bienestar para su mascota</p>
        </div>
    </div>
@endsection
