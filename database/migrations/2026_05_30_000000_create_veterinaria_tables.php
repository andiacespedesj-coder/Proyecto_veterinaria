<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rol
        Schema::create('Rol', function (Blueprint $table) {
            $table->integer('id_rol')->primary(); // Manual primary key as in SQL schema (1: Admin, 2: Empleado)
            $table->string('nombre', 50);
            $table->string('descripcion', 200)->nullable();
            $table->timestamps();
        });

        // 2. Usuario_Sistema
        Schema::create('Usuario_Sistema', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->string('nombre', 50);
            $table->string('paterno', 50)->nullable();
            $table->string('materno', 50)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('direccion', 100)->nullable();
            $table->string('login', 50)->unique();
            $table->string('contrasena', 100);
            $table->integer('id_rol');
            $table->foreign('id_rol')->references('id_rol')->on('Rol')->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Cliente
        Schema::create('Cliente', function (Blueprint $table) {
            $table->integer('ci')->primary(); // Carnet de Identidad, manually entered primary key
            $table->string('nombre', 50);
            $table->string('paterno', 50)->nullable();
            $table->string('materno', 50)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('direccion', 100)->nullable();
            $table->timestamps();
        });

        // 4. Mascota
        Schema::create('Mascota', function (Blueprint $table) {
            $table->increments('id_mascota');
            $table->string('nombre', 50);
            $table->string('color', 50)->nullable();
            $table->string('raza', 50)->nullable();
            $table->string('especie', 50)->nullable();
            $table->date('fechaNacimiento')->nullable();
            $table->integer('ci')->nullable();
            $table->foreign('ci')->references('ci')->on('Cliente')->onDelete('set null');
            $table->timestamps();
        });

        // 5. HistorialMedico
        Schema::create('HistorialMedico', function (Blueprint $table) {
            $table->increments('id_historial');
            $table->date('fecha');
            $table->string('vacunas', 300)->nullable();
            $table->unsignedInteger('id_mascota')->unique();
            $table->foreign('id_mascota')->references('id_mascota')->on('Mascota')->onDelete('cascade');
            $table->timestamps();
        });

        // 6. Servicio
        Schema::create('Servicio', function (Blueprint $table) {
            $table->increments('id_servicio');
            $table->string('horarioAtencion', 50)->nullable();
            $table->string('tipo', 50);
            $table->timestamps();
        });

        // 7. NotaServicio
        Schema::create('NotaServicio', function (Blueprint $table) {
            $table->increments('id_nota');
            $table->decimal('monto', 10, 2);
            $table->date('fecha');
            $table->unsignedInteger('id_usuario')->nullable();
            $table->integer('ci')->nullable();
            $table->unsignedInteger('id_mascota')->nullable();
            $table->foreign('id_usuario')->references('id_usuario')->on('Usuario_Sistema')->onDelete('set null');
            $table->foreign('ci')->references('ci')->on('Cliente')->onDelete('set null');
            $table->foreign('id_mascota')->references('id_mascota')->on('Mascota')->onDelete('set null');
            $table->timestamps();
        });

        // 8. DetalleServicio
        Schema::create('DetalleServicio', function (Blueprint $table) {
            $table->unsignedInteger('id_nota');
            $table->unsignedInteger('id_servicio');
            $table->integer('cantidad');
            $table->decimal('precio', 10, 2);
            $table->primary(['id_nota', 'id_servicio']);
            $table->foreign('id_nota')->references('id_nota')->on('NotaServicio')->onDelete('cascade');
            $table->foreign('id_servicio')->references('id_servicio')->on('Servicio')->onDelete('cascade');
            $table->timestamps();
        });

        // 9. Categoria
        Schema::create('Categoria', function (Blueprint $table) {
            $table->increments('id_categoria');
            $table->string('nombre', 50);
            $table->string('descripcion', 200)->nullable();
            $table->string('estado', 20)->nullable();
            $table->timestamps();
        });

        // 10. Producto
        Schema::create('Producto', function (Blueprint $table) {
            $table->increments('id_producto');
            $table->string('nombre', 50);
            $table->string('descripcion', 200)->nullable();
            $table->unsignedInteger('id_categoria')->nullable();
            $table->foreign('id_categoria')->references('id_categoria')->on('Categoria')->onDelete('set null');
            $table->timestamps();
        });

        // 11. Proveedor
        Schema::create('Proveedor', function (Blueprint $table) {
            $table->increments('id_proveedor');
            $table->string('nombre', 50);
            $table->string('telefono', 20)->nullable();
            $table->string('nit', 30)->nullable();
            $table->string('direccion', 100)->nullable();
            $table->timestamps();
        });

        // 12. ProductoProveedor
        Schema::create('ProductoProveedor', function (Blueprint $table) {
            $table->unsignedInteger('id_producto');
            $table->unsignedInteger('id_proveedor');
            $table->decimal('precio', 10, 2);
            $table->primary(['id_producto', 'id_proveedor']);
            $table->foreign('id_producto')->references('id_producto')->on('Producto')->onDelete('cascade');
            $table->foreign('id_proveedor')->references('id_proveedor')->on('Proveedor')->onDelete('cascade');
            $table->timestamps();
        });

        // 13. Almacen
        Schema::create('Almacen', function (Blueprint $table) {
            $table->increments('id_almacen');
            $table->string('descripcion', 200)->nullable();
            $table->timestamps();
        });

        // 14. Producto_Almacen
        Schema::create('Producto_Almacen', function (Blueprint $table) {
            $table->unsignedInteger('id_producto');
            $table->unsignedInteger('id_almacen');
            $table->integer('stock');
            $table->primary(['id_producto', 'id_almacen']);
            $table->foreign('id_producto')->references('id_producto')->on('Producto')->onDelete('cascade');
            $table->foreign('id_almacen')->references('id_almacen')->on('Almacen')->onDelete('cascade');
            $table->timestamps();
        });

        // 15. NotaVenta
        Schema::create('NotaVenta', function (Blueprint $table) {
            $table->increments('id_venta');
            $table->date('fecha');
            $table->decimal('monto', 10, 2);
            $table->unsignedInteger('id_usuario');
            $table->integer('ci')->nullable();
            $table->foreign('id_usuario')->references('id_usuario')->on('Usuario_Sistema')->onDelete('cascade');
            $table->foreign('ci')->references('ci')->on('Cliente')->onDelete('set null');
            $table->timestamps();
        });

        // 16. DetalleVenta
        Schema::create('DetalleVenta', function (Blueprint $table) {
            $table->unsignedInteger('id_venta');
            $table->unsignedInteger('id_producto');
            $table->integer('cantidad');
            $table->decimal('precio', 10, 2);
            $table->primary(['id_venta', 'id_producto']);
            $table->foreign('id_venta')->references('id_venta')->on('NotaVenta')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('Producto')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DetalleVenta');
        Schema::dropIfExists('NotaVenta');
        Schema::dropIfExists('Producto_Almacen');
        Schema::dropIfExists('Almacen');
        Schema::dropIfExists('ProductoProveedor');
        Schema::dropIfExists('Proveedor');
        Schema::dropIfExists('Producto');
        Schema::dropIfExists('Categoria');
        Schema::dropIfExists('DetalleServicio');
        Schema::dropIfExists('NotaServicio');
        Schema::dropIfExists('Servicio');
        Schema::dropIfExists('HistorialMedico');
        Schema::dropIfExists('Mascota');
        Schema::dropIfExists('Cliente');
        Schema::dropIfExists('Usuario_Sistema');
        Schema::dropIfExists('Rol');
    }
};
