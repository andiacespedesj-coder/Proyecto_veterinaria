<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsEmpleado
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isEmpleado()) {
            return $next($request);
        }

        return redirect()->route('login')->withErrors(['login' => 'Acceso denegado. Se requieren permisos de Empleado.']);
    }
}
