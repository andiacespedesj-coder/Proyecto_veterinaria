<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'Producto';
    protected $primaryKey = 'id_producto';

    protected $fillable = ['nombre', 'descripcion', 'id_categoria'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'ProductoProveedor', 'id_producto', 'id_proveedor')
                    ->withPivot('precio')
                    ->withTimestamps();
    }

    public function almacenes()
    {
        return $this->belongsToMany(Almacen::class, 'Producto_Almacen', 'id_producto', 'id_almacen')
                    ->withPivot('stock')
                    ->withTimestamps();
    }
}
