<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rol;
use App\Models\UsuarioSistema;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\NotaVenta;
use App\Models\NotaServicio;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalVentas = NotaVenta::sum('monto');
        $totalServicios = NotaServicio::sum('monto');
        $totalClientes = Cliente::count();
        $totalEmpleados = UsuarioSistema::count();

        // Recent sales
        $recientesVentas = NotaVenta::with('cliente', 'usuario')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalVentas', 
            'totalServicios', 
            'totalClientes', 
            'totalEmpleados',
            'recientesVentas'
        ));
    }

    // --- Empleados CRUD ---
    public function empleados()
    {
        $empleados = UsuarioSistema::with('rol')->get();
        $roles = Rol::all();
        return view('admin.empleados', compact('empleados', 'roles'));
    }

    public function storeEmpleado(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'paterno' => 'nullable|string|max:50',
            'materno' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:100',
            'login' => 'required|string|max:50|unique:Usuario_Sistema,login',
            'contrasena' => 'required|string|min:4',
            'id_rol' => 'required|exists:Rol,id_rol',
        ]);

        UsuarioSistema::create([
            'nombre' => $request->nombre,
            'paterno' => $request->paterno,
            'materno' => $request->materno,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'login' => $request->login,
            'contrasena' => Hash::make($request->contrasena),
            'id_rol' => $request->id_rol,
        ]);

        return redirect()->route('admin.empleados')->with('success', 'Empleado registrado con éxito.');
    }

    public function updateEmpleado(Request $request, $id)
    {
        $empleado = UsuarioSistema::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:50',
            'paterno' => 'nullable|string|max:50',
            'materno' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:100',
            'login' => 'required|string|max:50|unique:Usuario_Sistema,login,' . $id . ',id_usuario',
            'contrasena' => 'nullable|string|min:4',
            'id_rol' => 'required|exists:Rol,id_rol',
        ]);

        $data = [
            'nombre' => $request->nombre,
            'paterno' => $request->paterno,
            'materno' => $request->materno,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'login' => $request->login,
            'id_rol' => $request->id_rol,
        ];

        if ($request->filled('contrasena')) {
            $data['contrasena'] = Hash::make($request->contrasena);
        }

        $empleado->update($data);

        return redirect()->route('admin.empleados')->with('success', 'Empleado actualizado con éxito.');
    }

    public function deleteEmpleado($id)
    {
        $empleado = UsuarioSistema::findOrFail($id);
        
        // Prevent self-deletion
        if ($empleado->id_usuario === auth()->id()) {
            return redirect()->route('admin.empleados')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $empleado->delete();
        return redirect()->route('admin.empleados')->with('success', 'Empleado eliminado con éxito.');
    }

    // --- Stock & Inventario ---
    public function inventario()
    {
        $productos = Producto::with(['categoria', 'almacenes'])->get();
        $almacenes = Almacen::all();
        $categorias = Categoria::all();
        return view('admin.inventario', compact('productos', 'almacenes', 'categorias'));
    }

    public function storeProducto(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:200',
            'id_categoria' => 'required|exists:Categoria,id_categoria',
        ]);

        Producto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'id_categoria' => $request->id_categoria,
        ]);

        return redirect()->route('admin.inventario')->with('success', 'Producto registrado con éxito.');
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:Producto,id_producto',
            'id_almacen' => 'required|exists:Almacen,id_almacen',
            'stock' => 'required|integer|min:0',
        ]);

        // Using updateOrInsert for composite key relationship Producto_Almacen
        DB::table('Producto_Almacen')->updateOrInsert(
            ['id_producto' => $request->id_producto, 'id_almacen' => $request->id_almacen],
            ['stock' => $request->stock, 'updated_at' => now()]
        );

        return redirect()->route('admin.inventario')->with('success', 'Stock actualizado con éxito.');
    }

    // --- Reportes ---
    public function reportes()
    {
        // 1. Reporte de Productos
        $reporteProductos = Producto::with(['categoria', 'almacenes'])
            ->get()
            ->map(function ($producto) {
                $totalStock = $producto->almacenes->sum('pivot.stock');
                return [
                    'id_producto' => $producto->id_producto,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'categoria' => $producto->categoria->nombre ?? 'Sin Categoría',
                    'stock' => $totalStock
                ];
            });

        // 2. Reporte de Medicamentos (Categoría: Medicamentos)
        $reporteMedicamentos = Producto::whereHas('categoria', function ($q) {
                $q->where('nombre', 'Medicamentos');
            })
            ->with(['almacenes'])
            ->get()
            ->map(function ($producto) {
                $totalStock = $producto->almacenes->sum('pivot.stock');
                return [
                    'id_producto' => $producto->id_producto,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'stock' => $totalStock
                ];
            });

        // 3. Reporte de Servicios
        $reporteServicios = DB::table('DetalleServicio')
            ->join('Servicio', 'DetalleServicio.id_servicio', '=', 'Servicio.id_servicio')
            ->select(
                'Servicio.tipo',
                'Servicio.horarioAtencion',
                DB::raw('SUM(DetalleServicio.cantidad) as total_servicios'),
                DB::raw('SUM(DetalleServicio.cantidad * DetalleServicio.precio) as total_monto')
            )
            ->groupBy('Servicio.id_servicio', 'Servicio.tipo', 'Servicio.horarioAtencion')
            ->get();

        return view('admin.reportes', compact('reporteProductos', 'reporteMedicamentos', 'reporteServicios'));
    }
}
