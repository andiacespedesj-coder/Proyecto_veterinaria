<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialMedico extends Model
{
    protected $table = 'HistorialMedico';
    protected $primaryKey = 'id_historial';

    protected $fillable = ['fecha', 'vacunas', 'id_mascota'];

    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota', 'id_mascota');
    }
}
