<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\UsuarioSistema;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\Servicio;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles
        Rol::updateOrCreate(['id_rol' => 1], ['nombre' => 'Administrador', 'descripcion' => 'Administrador del sistema con acceso total']);
        Rol::updateOrCreate(['id_rol' => 2], ['nombre' => 'Empleado', 'descripcion' => 'Empleado de atención con permisos limitados']);

        // 2. Usuarios del sistema
        UsuarioSistema::updateOrCreate(
            ['login' => 'admin'],
            [
                'nombre' => 'Administrador',
                'paterno' => 'Veterinaria',
                'materno' => 'Demo',
                'telefono' => '77712345',
                'direccion' => 'Av. Principal 123',
                'contrasena' => Hash::make('admin123'),
                'id_rol' => 1
            ]
        );

        UsuarioSistema::updateOrCreate(
            ['login' => 'empleado'],
            [
                'nombre' => 'Juan',
                'paterno' => 'Pérez',
                'materno' => 'Gómez',
                'telefono' => '60012345',
                'direccion' => 'Calle Secundaria 456',
                'contrasena' => Hash::make('employee123'),
                'id_rol' => 2
            ]
        );

        // 3. Categorías
        $catMedicamentos = Categoria::updateOrCreate(['nombre' => 'Medicamentos'], ['descripcion' => 'Medicinas, vacunas y tratamientos', 'estado' => 'Activo']);
        $catAlimentos = Categoria::updateOrCreate(['nombre' => 'Alimentos'], ['descripcion' => 'Alimento balanceado y croquetas', 'estado' => 'Activo']);
        $catAccesorios = Categoria::updateOrCreate(['nombre' => 'Accesorios'], ['descripcion' => 'Collares, juguetes y correas', 'estado' => 'Activo']);

        // 4. Productos
        $prod1 = Producto::updateOrCreate(['nombre' => 'Paracetamol Mascota 50mg'], ['descripcion' => 'Analgésico para mascotas pequeñas', 'id_categoria' => $catMedicamentos->id_categoria]);
        $prod2 = Producto::updateOrCreate(['nombre' => 'Antibiótico Amoxicilina Vet'], ['descripcion' => 'Antibiótico de amplio espectro', 'id_categoria' => $catMedicamentos->id_categoria]);
        $prod3 = Producto::updateOrCreate(['nombre' => 'Croquetas Dog Chow 3kg'], ['descripcion' => 'Alimento para perros adultos', 'id_categoria' => $catAlimentos->id_categoria]);
        $prod4 = Producto::updateOrCreate(['nombre' => 'Shampoo Pulguicida Canino'], ['descripcion' => 'Shampoo medicado anti pulgas', 'id_categoria' => $catAccesorios->id_categoria]);

        // 5. Almacén
        $almacenPrincipal = Almacen::updateOrCreate(['descripcion' => 'Almacén Central Veterinaria']);
        $almacenSecundario = Almacen::updateOrCreate(['descripcion' => 'Almacén Vitrina Ventas']);

        // 6. Stock de Productos (Producto_Almacen)
        DB::table('Producto_Almacen')->updateOrInsert(
            ['id_producto' => $prod1->id_producto, 'id_almacen' => $almacenPrincipal->id_almacen],
            ['stock' => 50]
        );
        DB::table('Producto_Almacen')->updateOrInsert(
            ['id_producto' => $prod2->id_producto, 'id_almacen' => $almacenPrincipal->id_almacen],
            ['stock' => 30]
        );
        DB::table('Producto_Almacen')->updateOrInsert(
            ['id_producto' => $prod3->id_producto, 'id_almacen' => $almacenPrincipal->id_almacen],
            ['stock' => 20]
        );
        DB::table('Producto_Almacen')->updateOrInsert(
            ['id_producto' => $prod4->id_producto, 'id_almacen' => $almacenPrincipal->id_almacen],
            ['stock' => 15]
        );

        // 7. Servicios
        Servicio::updateOrCreate(['tipo' => 'Consulta Médica General'], ['horarioAtencion' => 'Lunes a Viernes 08:00 - 18:00']);
        Servicio::updateOrCreate(['tipo' => 'Peluquería y Baño Canino'], ['horarioAtencion' => 'Lunes a Sábado 09:00 - 17:00']);
        Servicio::updateOrCreate(['tipo' => 'Desparasitación Interna'], ['horarioAtencion' => 'Lunes a Domingo 24h']);
        Servicio::updateOrCreate(['tipo' => 'Vacunación Quintuple'], ['horarioAtencion' => 'Lunes a Viernes 08:00 - 18:00']);
    }
}
