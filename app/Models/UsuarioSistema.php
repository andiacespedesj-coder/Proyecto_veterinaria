<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UsuarioSistema extends Authenticatable
{
    use Notifiable;

    protected $table = 'Usuario_Sistema';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'paterno',
        'materno',
        'telefono',
        'direccion',
        'login',
        'contrasena',
        'id_rol'
    ];

    protected $hidden = [
        'contrasena',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function isAdmin()
    {
        return (int)$this->id_rol === 1;
    }

    public function isEmpleado()
    {
        return (int)$this->id_rol === 2;
    }
}
