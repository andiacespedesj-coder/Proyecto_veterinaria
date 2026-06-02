<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'Rol';
    protected $primaryKey = 'id_rol';
    public $incrementing = false; // IDs are manually set (1: Admin, 2: Empleado)
    
    protected $fillable = ['id_rol', 'nombre', 'descripcion'];

    public function usuarios()
    {
        return $this->hasMany(UsuarioSistema::class, 'id_rol', 'id_rol');
    }
}
