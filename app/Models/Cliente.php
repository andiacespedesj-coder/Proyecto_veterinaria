<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'Cliente';
    protected $primaryKey = 'ci';
    public $incrementing = false; // CI is Carnet de Identidad (manual integer)

    protected $fillable = ['ci', 'nombre', 'paterno', 'materno', 'telefono', 'direccion'];

    public function mascotas()
    {
        return $this->hasMany(Mascota::class, 'ci', 'ci');
    }
}
