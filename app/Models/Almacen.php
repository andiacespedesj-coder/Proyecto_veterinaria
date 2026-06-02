<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'Almacen';
    protected $primaryKey = 'id_almacen';

    protected $fillable = ['descripcion'];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'Producto_Almacen', 'id_almacen', 'id_producto')
                    ->withPivot('stock')
                    ->withTimestamps();
    }
}
