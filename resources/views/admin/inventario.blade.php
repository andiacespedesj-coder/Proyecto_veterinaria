@extends('layouts.app')

@section('title', 'Inventario & Stock')
@section('header_title', 'Control de Inventario')

@section('content')
    <div class="dashboard-sections" style="grid-template-columns: 2fr 1fr;">
        <!-- Left: Product stock details table -->
        <div class="card">
            <div class="card-title">Stock por Producto</div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Stock por Almacén</th>
                            <th>Stock Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $prod)
                            @php
                                $totalStock = $prod->almacenes->sum('pivot.stock');
                            @endphp
                            <tr>
                                <td><strong>{{ $prod->nombre }}</strong></td>
                                <td>{{ $prod->categoria->nombre ?? 'Sin categoría' }}</td>
                                <td>{{ $prod->descripcion ?? '-' }}</td>
                                <td>
                                    <ul style="list-style:none; padding:0; font-size:0.85rem;">
                                        @forelse($prod->almacenes as $alm)
                                            <li style="margin-bottom: 2px;">
                                                <span style="color:var(--color-muted);">{{ $alm->descripcion }}:</span> 
                                                <strong>{{ $alm->pivot->stock }} uds</strong>
                                            </li>
                                        @empty
                                            <span style="color:var(--danger); font-size:0.8rem; font-weight:600;">Sin stock registrado</span>
                                        @endforelse
                                    </ul>
                                </td>
                                <td>
                                    @if($totalStock == 0)
                                        <span class="badge badge-stock-none">Agotado</span>
                                    @elseif($totalStock < 10)
                                        <span class="badge badge-stock-low">{{ $totalStock }} uds (Bajo)</span>
                                    @else
                                        <span class="badge badge-stock-good">{{ $totalStock }} uds</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; color:var(--color-muted);">No hay productos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side: Forms for Product and Stock updates -->
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <!-- Register Product -->
            <div class="card">
                <div class="card-title">Registrar Producto</div>
                <form action="{{ route('admin.producto.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nombre">Nombre del Producto *</label>
                        <input type="text" name="nombre" id="nombre" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="id_categoria">Categoría *</label>
                        <select name="id_categoria" id="id_categoria" class="form-control" required>
                            <option value="">Seleccione Categoría</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" placeholder="Detalles de presentación o uso..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;">Registrar Producto</button>
                </form>
            </div>

            <!-- Ingrese/Modificar Stock -->
            <div class="card">
                <div class="card-title">Actualizar Stock</div>
                <form action="{{ route('admin.stock.update') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="id_producto">Producto *</label>
                        <select name="id_producto" id="id_producto" class="form-control" required>
                            <option value="">Seleccione un Producto</option>
                            @foreach($productos as $prod)
                                <option value="{{ $prod->id_producto }}">{{ $prod->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_almacen">Almacén *</label>
                        <select name="id_almacen" id="id_almacen" class="form-control" required>
                            <option value="">Seleccione Almacén</option>
                            @foreach($almacenes as $alm)
                                <option value="{{ $alm->id_almacen }}">{{ $alm->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="stock">Cantidad en Stock *</label>
                        <input type="text" name="stock" id="stock" class="form-control" required min="0">
                    </div>

                    <button type="submit" class="btn btn-secondary" style="width:100%; border-color:var(--accent); color:var(--accent); font-weight:700;">
                        Actualizar Stock
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
