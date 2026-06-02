<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Proyecto_Veterinaria2</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo">
                <h2>Veterinaria<span>Porvenir</span></h2>
                <p>Gestión de VeterinariaDB</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" style="font-size:0.85rem; padding: 0.75rem;">
                    <div>
                        @foreach ($errors->all() as $error)
                            <p style="margin:0;">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="login">Usuario de Sistema</label>
                    <input type="text" name="login" id="login" class="form-control" placeholder="Ingrese su login" value="{{ old('login') }}" required autofocus autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" name="contrasena" id="contrasena" class="form-control" placeholder="Ingrese su contraseña" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.8rem;">
                    Iniciar Sesión
                </button>
            </form>
        </div>
    </div>

</body>
</html>
