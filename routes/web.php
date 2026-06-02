<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmpleadoController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::any('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Panel Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Empleados CRUD
    Route::get('/empleados', [AdminController::class, 'empleados'])->name('admin.empleados');
    Route::post('/empleados', [AdminController::class, 'storeEmpleado'])->name('admin.empleados.store');
    Route::put('/empleados/{id}', [AdminController::class, 'updateEmpleado'])->name('admin.empleados.update');
    Route::delete('/empleados/{id}', [AdminController::class, 'deleteEmpleado'])->name('admin.empleados.delete');

    // Inventario y Stock
    Route::get('/inventario', [AdminController::class, 'inventario'])->name('admin.inventario');
    Route::post('/inventario/producto', [AdminController::class, 'storeProducto'])->name('admin.producto.store');
    Route::post('/inventario/stock', [AdminController::class, 'updateStock'])->name('admin.stock.update');

    // Reportes
    Route::get('/reportes', [AdminController::class, 'reportes'])->name('admin.reportes');
});

// empleado Panel Routes
Route::middleware(['auth', 'empleado'])->prefix('empleado')->group(function () {
    Route::get('/', [EmpleadoController::class, 'dashboard'])->name('empleado.dashboard');
    
    // Clientes y Mascotas
    Route::get('/clientes', [EmpleadoController::class, 'clientes'])->name('empleado.clientes');
    Route::post('/clientes', [EmpleadoController::class, 'storeCliente'])->name('empleado.clientes.store');
    Route::post('/mascotas', [EmpleadoController::class, 'storeMascota'])->name('empleado.mascotas.store');
    Route::post('/historial/{id}', [EmpleadoController::class, 'updateHistorial'])->name('empleado.historial.update');

    // Ventas (POS)
    Route::get('/venta', [EmpleadoController::class, 'venta'])->name('empleado.venta');
    Route::post('/venta/store', [EmpleadoController::class, 'storeVenta'])->name('empleado.venta.store');
    Route::get('/venta/ticket/{id}', [EmpleadoController::class, 'ticketVenta'])->name('empleado.venta.ticket');

    // Servicios
    Route::get('/servicio', [EmpleadoController::class, 'servicio'])->name('empleado.servicio');
    Route::post('/servicio/store', [EmpleadoController::class, 'storeServicio'])->name('empleado.servicio.store');
    Route::get('/servicio/ticket/{id}', [EmpleadoController::class, 'ticketServicio'])->name('empleado.servicio.ticket');
});
