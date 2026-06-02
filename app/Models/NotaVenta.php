<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaVenta extends Model
{
    protected $table = 'NotaVenta';
    protected $primaryKey = 'id_venta';

    protected $fillable = ['fecha', 'monto', 'id_usuario', 'ci'];

    public function usuario()
    {
        return $this->belongsTo(UsuarioSistema::class, 'id_usuario', 'id_usuario');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'ci', 'ci');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'DetalleVenta', 'id_venta', 'id_producto')
                    ->withPivot('cantidad', 'precio')
                    ->withTimestamps();
    }
}
