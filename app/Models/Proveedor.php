<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'Proveedor';
    protected $primaryKey = 'id_proveedor';

    protected $fillable = ['nombre', 'telefono', 'nit', 'direccion'];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'ProductoProveedor', 'id_proveedor', 'id_producto')
                    ->withPivot('precio')
                    ->withTimestamps();
    }
}
