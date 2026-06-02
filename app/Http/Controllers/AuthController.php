<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'contrasena' => 'required|string',
        ]);

        // Map custom password field 'contrasena' to Laravel's expected 'password'
        $authCredentials = [
            'login' => $credentials['login'],
            'password' => $credentials['contrasena']
        ];

        if (Auth::attempt($authCredentials)) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'login' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectBasedOnRole($user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isEmpleado()) {
            return redirect()->route('empleado.dashboard');
        }
        
        Auth::logout();
        return redirect()->route('login')->withErrors(['login' => 'Usuario sin rol asignado o no autorizado.']);
    }
}
