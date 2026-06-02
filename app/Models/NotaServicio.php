<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaServicio extends Model
{
    protected $table = 'NotaServicio';
    protected $primaryKey = 'id_nota';

    protected $fillable = ['monto', 'fecha', 'id_usuario', 'ci', 'id_mascota'];

    public function usuario()
    {
        return $this->belongsTo(UsuarioSistema::class, 'id_usuario', 'id_usuario');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'ci', 'ci');
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota', 'id_mascota');
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'DetalleServicio', 'id_nota', 'id_servicio')
                    ->withPivot('cantidad', 'precio')
                    ->withTimestamps();
    }
}
