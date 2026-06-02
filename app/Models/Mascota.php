<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mascota extends Model
{
    protected $table = 'Mascota';
    protected $primaryKey = 'id_mascota';

    protected $fillable = ['nombre', 'color', 'raza', 'especie', 'fechaNacimiento', 'ci'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'ci', 'ci');
    }

    public function historial()
    {
        return $this->hasOne(HistorialMedico::class, 'id_mascota', 'id_mascota');
    }
}
