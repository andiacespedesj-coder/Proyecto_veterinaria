<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'Servicio';
    protected $primaryKey = 'id_servicio';

    protected $fillable = ['horarioAtencion', 'tipo'];
}
