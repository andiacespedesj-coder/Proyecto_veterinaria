@extends('layouts.app')

@section('title', 'Clientes & Mascotas')
@section('header_title', 'Gestión de Clientes y Mascotas')

@section('content')
    <div class="dashboard-sections" style="grid-template-columns: 2fr 1fr;">
        <!-- Left: Clients and Pets Expandable Details List -->
        <div class="card">
            <div class="card-title">Clientes Registrados</div>
            <p style="color:var(--color-muted); font-size:0.85rem; margin-bottom:1rem;">
                Haga clic sobre un cliente para ver sus mascotas asociadas e historial médico.
            </p>

            <div style="display:flex; flex-direction:column; gap:0.75rem;">
                @forelse($clientes as $cli)
                    <div style="border: 1px solid var(--border-color); border-radius:12px; overflow:hidden;">
                        <!-- Client Header -->
                        <div onclick="toggleClientDetails('cli-{{ $cli->ci }}')" style="background-color:#f8fafc; padding:1rem; cursor:pointer; display:flex; justify-content:space-between; align-items:center; hover:background:#f1f5f9;">
                            <div>
                                <strong style="font-size:1.05rem; color:var(--accent);">
                                    {{ $cli->nombre }} {{ $cli->paterno }} {{ $cli->materno }}
                                </strong>
                                <div style="font-size:0.8rem; color:var(--color-muted); margin-top:0.15rem;">
                                    CI: {{ $cli->ci }} | Tel: {{ $cli->telefono ?? 'S/N' }} | Dir: {{ $cli->direccion ?? 'S/N' }}
                                </div>
                            </div>
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <span class="badge badge-admin" style="font-size:0.7rem;">
                                    {{ $cli->mascotas->count() }} Mascotas
                                </span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </div>

                        <!-- Client Details (Pets List) -->
                        <div id="cli-{{ $cli->ci }}" style="display:none; padding:1.25rem; border-top:1px solid var(--border-color); background:#ffffff;">
                            <h4 style="font-weight:700; margin-bottom:0.75rem; font-size:0.95rem; text-transform:uppercase; letter-spacing:0.5px; color:var(--color-primary);">
                                Mascotas de este Cliente
                            </h4>

                            @forelse($cli->mascotas as $mascota)
                                <div class="pet-card">
                                    <div class="pet-card-title">
                                        <span>🐾 {{ $mascota->nombre }}</span>
                                        <span style="font-size:0.8rem; color:var(--color-muted); font-weight:normal;">
                                            {{ $mascota->especie }} / {{ $mascota->raza ?? 'Mestizo' }} ({{ $mascota->color ?? 'Sin color' }})
                                        </span>
                                    </div>
                                    <div style="font-size:0.85rem; color:var(--color-muted);">
                                        Nacimiento: {{ $mascota->fechaNacimiento ? \Carbon\Carbon::parse($mascota->fechaNacimiento)->format('d/m/Y') : 'Desconocido' }}
                                    </div>

                                    <!-- Medical History -->
                                    @if($mascota->historial)
                                        <div class="pet-history-box">
                                            <div style="font-weight:600; margin-bottom:0.25rem; color:var(--color-primary);">
                                                Historial de Vacunas (Última actualización: {{ \Carbon\Carbon::parse($mascota->historial->fecha)->format('d/m/Y') }})
                                            </div>
                                            <p style="background:#f1f5f9; padding:0.5rem; border-radius:6px; font-family:monospace; margin-bottom:0.5rem;">
                                                {{ $mascota->historial->vacunas }}
                                            </p>

                                            <!-- Form to Update Vaccines -->
                                            <form action="{{ route('empleado.historial.update', $mascota->historial->id_historial) }}" method="POST" style="display:flex; gap:0.5rem; margin-top:0.5rem;">
                                                @csrf
                                                <input type="hidden" name="fecha" value="{{ date('Y-m-d') }}">
                                                <input type="text" name="vacunas" class="form-control" style="flex:1; padding:0.4rem; font-size:0.85rem;" placeholder="Modificar vacunas..." value="{{ $mascota->historial->vacunas }}" required>
                                                <button type="submit" class="btn btn-secondary btn-sm" style="font-size:0.8rem;">Actualizar</button>
                                            </form>
                                        </div>
                                    @else
                                        <div style="font-size:0.8rem; color:var(--danger); font-weight:600; margin-top:0.5rem;">
                                            Sin historial clínico vinculado.
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p style="font-size:0.85rem; color:var(--color-muted); text-align:center;">No tiene mascotas registradas.</p>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <p style="color:var(--color-muted); text-align:center;">No hay clientes registrados en el sistema.</p>
                @endforelse
            </div>
        </div>

        <!-- Right: Register Forms -->
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <!-- Register Client -->
            <div class="card">
                <div class="card-title">Registrar Cliente</div>
                <form action="{{ route('empleado.clientes.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="ci">Cédula de Identidad (CI) *</label>
                        <input type="text" name="ci" id="ci" class="form-control" required placeholder="Ej: 12345678">
                    </div>

                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" name="nombre" id="nombre" class="form-control">
                    </div>

                    <div class="flex-row">
                        <div class="form-group">
                            <label for="paterno">A. Paterno</label>
                            <input type="text" name="paterno" id="paterno" class="form-control" >
                        </div>
                        <div class="form-group">
                            <label for="materno">A. Materno</label>
                            <input type="text" name="materno" id="materno" class="form-control" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control" placeholder="Ej: 78563214">
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Ej: B/ Urkupiña">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;">Registrar Cliente</button>
                </form>
            </div>

            <!-- Registrar mascota -->
            <div class="card">
                <div class="card-title">Registrar Mascota</div>
                <form action="{{ route('empleado.mascotas.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="ci_mascota">Dueño (Cliente) *</label>
                        <select name="ci" id="ci_mascota" class="form-control" required>
                            <option value="">Seleccione Dueño</option>
                            @foreach($clientes as $cli)
                                <option value="{{ $cli->ci }}">{{ $cli->nombre }} {{ $cli->paterno }} (CI: {{ $cli->ci }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nombre_mascota">Nombre de la Mascota *</label>
                        <input type="text" name="nombre" id="nombre_mascota" class="form-control" required placeholder="Ej: Firulais">
                    </div>

                    <div class="flex-row">
                        <div class="form-group">
                            <label for="especie">Especie</label>
                            <input type="text" name="especie" id="especie" class="form-control" placeholder="Ej:Canino">
                        </div>
                        <div class="form-group">
                            <label for="raza">Raza</label>
                            <input type="text" name="raza" id="raza" class="form-control" placeholder="Ej:Criollo">
                        </div>
                    </div>

                    <div class="flex-row">
                        <div class="form-group">
                            <label for="color">Color</label>
                            <input type="text" name="color" id="color" class="form-control" >
                        </div>
                        <div class="form-group">
                            <label for="fechaNacimiento">Nacimiento</label>
                            <input type="date" name="fechaNacimiento" id="fechaNacimiento" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-secondary" style="width:100%; border-color:var(--accent); color:var(--accent); font-weight:700;">
                        Registrar Mascota
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Toggle Details Script -->
    <script>
        function toggleClientDetails(id) {
            const el = document.getElementById(id);
            if (el.style.display === "none") {
                el.style.display = "block";
            } else {
                el.style.display = "none";
            }
        }
    </script>
@endsection
