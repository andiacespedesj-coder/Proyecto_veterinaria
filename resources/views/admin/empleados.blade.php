@extends('layouts.app')

@section('title', 'Gestionar Empleados')
@section('header_title', 'Gestión de Empleados')

@section('content')
    <div class="dashboard-sections">
        <!-- Left: empleados List -->
        <div class="card">
            <div class="card-title">Lista de Empleados</div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Login</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empleados as $emp)
                            <tr>
                                <td>{{ $emp->nombre }} {{ $emp->paterno }} {{ $emp->materno }}</td>
                                <td>{{ $emp->telefono ?? 'S/N' }}</td>
                                <td>{{ $emp->direccion ?? 'S/N' }}</td>
                                <td><code>{{ $emp->login }}</code></td>
                                <td>
                                    <span class="badge {{ $emp->id_rol == 1 ? 'badge-admin' : 'badge-empleado' }}">
                                        {{ $emp->rol->nombre }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" onclick="editEmpleado({{ json_encode($emp) }})">
                                        Editar
                                    </button>
                                    @if($emp->id_usuario !== Auth::id())
                                        <form action="{{ route('admin.empleados.delete', $emp->id_usuario) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Está seguro de eliminar a este empleado?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; color:var(--color-muted);">No hay empleados registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Register / Edit Form Card -->
        <div class="card" id="form-card">
            <div class="card-title" id="form-title">Registrar Empleado</div>
            
            <form id="empleado-form" action="{{ route('admin.empleados.store') }}" method="POST">
                @csrf
                <div id="method-field"></div>
                
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Ej: Carlos">
                </div>

                <div class="flex-row">
                    <div class="form-group">
                        <label for="paterno">A. Paterno</label>
                        <input type="text" name="paterno" id="paterno" class="form-control" placeholder="Ej: Justiniano">
                    </div>
                    <div class="form-group">
                        <label for="materno">A. Materno</label>
                        <input type="text" name="materno" id="materno" class="form-control" placeholder="Ej: Roca">
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control" placeholder="Ej: 78945612">
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Ej: Av. Bush 3er Anillo">
                </div>

                <div class="form-group">
                    <label for="id_rol">Rol *</label>
                    <select name="id_rol" id="id_rol" class="form-control" required>
                        <option value="">Seleccione un Rol</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id_rol }}">{{ $rol->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="login">Nombre de Usuario (Login) *</label>
                    <input type="text" name="login" id="login" class="form-control" required placeholder="Ej: cjustiniano">
                </div>

                <div class="form-group">
                    <label for="contrasena" id="label-contrasena">Contraseña *</label>
                    <input type="password" name="contrasena" id="contrasena" class="form-control" placeholder="Dejar en blanco para mantener actual en edición">
                </div>

                <div style="display:flex; gap:0.5rem; margin-top:1.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex:1;" id="submit-btn">Guardar</button>
                    <button type="button" class="btn btn-secondary" onclick="resetForm()" style="display:none;" id="cancel-btn">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit/Registration Form State Script -->
    <script>
        const storeUrl = "{{ route('admin.empleados.store') }}";
        
        function editEmpleado(emp) {
            // Update Card Details
            document.getElementById('form-title').innerText = "Editar Empleado";
            document.getElementById('empleado-form').action = "/admin/empleados/" + emp.id_usuario;
            document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            
            // Fill Fields
            document.getElementById('nombre').value = emp.nombre || '';
            document.getElementById('paterno').value = emp.paterno || '';
            document.getElementById('materno').value = emp.materno || '';
            document.getElementById('telefono').value = emp.telefono || '';
            document.getElementById('direccion').value = emp.direccion || '';
            document.getElementById('id_rol').value = emp.id_rol || '';
            document.getElementById('login').value = emp.login || '';
            
            // Adjust Password Label and Requirements
            document.getElementById('label-contrasena').innerText = "Nueva Contraseña (Opcional)";
            document.getElementById('contrasena').required = false;

            // Show cancel button
            document.getElementById('cancel-btn').style.display = "block";
            
            // Highlight the form card
            const formCard = document.getElementById('form-card');
            formCard.style.border = '2px solid var(--accent)';
            formCard.scrollIntoView({ behavior: 'smooth' });
        }

        function resetForm() {
            document.getElementById('form-title').innerText = "Registrar Empleado";
            document.getElementById('empleado-form').action = storeUrl;
            document.getElementById('method-field').innerHTML = '';
            
            document.getElementById('empleado-form').reset();
            
            document.getElementById('label-contrasena').innerText = "Contraseña *";
            document.getElementById('contrasena').required = true;
            document.getElementById('cancel-btn').style.display = "none";
            
            const formCard = document.getElementById('form-card');
            formCard.style.border = '1px solid rgba(241, 245, 249, 0.8)';
        }

        // Set password to required initially since it is creation
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('contrasena').required = true;
        });
    </script>
@endsection
