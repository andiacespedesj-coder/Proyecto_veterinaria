<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Mascota;
use App\Models\HistorialMedico;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\NotaVenta;
use App\Models\NotaServicio;
use Illuminate\Support\Facades\DB;

class EmpleadoController extends Controller
{
    public function dashboard()
    {
        $totalClientes = Cliente::count();
        $totalMascotas = Mascota::count();
        $misVentas = NotaVenta::where('id_usuario', auth()->id())->count();
        $misServicios = NotaServicio::where('id_usuario', auth()->id())->count();

        return view('empleado.dashboard', compact('totalClientes', 'totalMascotas', 'misVentas', 'misServicios'));
    }

    // --- Clientes y Mascotas ---
    public function clientes()
    {
        $clientes = Cliente::with('mascotas.historial')->get();
        return view('empleado.clientes', compact('clientes'));
    }

    public function storeCliente(Request $request)
    {
        $request->validate([
            'ci' => 'required|integer|unique:Cliente,ci',
            'nombre' => 'required|string|max:50',
            'paterno' => 'nullable|string|max:50',
            'materno' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:100',
        ]);

        Cliente::create($request->all());

        return redirect()->route('empleado.clientes')->with('success', 'Cliente registrado con éxito.');
    }

    public function storeMascota(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'color' => 'nullable|string|max:50',
            'raza' => 'nullable|string|max:50',
            'especie' => 'nullable|string|max:50',
            'fechaNacimiento' => 'nullable|date',
            'ci' => 'required|exists:Cliente,ci',
        ]);

        $mascota = Mascota::create($request->all());

        // Automatically initialize medical history
        HistorialMedico::create([
            'fecha' => now(),
            'vacunas' => 'Ninguna registrada',
            'id_mascota' => $mascota->id_mascota,
        ]);

        return redirect()->route('empleado.clientes')->with('success', 'Mascota registrada con éxito y su historial médico ha sido inicializado.');
    }

    public function updateHistorial(Request $request, $id_historial)
    {
        $historial = HistorialMedico::findOrFail($id_historial);

        $request->validate([
            'vacunas' => 'required|string|max:300',
            'fecha' => 'required|date'
        ]);

        $historial->update([
            'vacunas' => $request->vacunas,
            'fecha' => $request->fecha
        ]);

        return redirect()->route('empleado.clientes')->with('success', 'Historial médico actualizado.');
    }

    // --- POS Ventas ---
    public function venta()
    {
        $clientes = Cliente::all();
        // Load products that have stock in any warehouse
        $productos = Producto::with(['almacenes', 'categoria'])->get()->map(function($prod) {
            $prod->stock = $prod->almacenes->sum('pivot.stock');
            // Assuming a default price if there are no suppliers, let's look for ProductoProveedor or set default 10.00
            $prod->precio = DB::table('ProductoProveedor')->where('id_producto', $prod->id_producto)->value('precio') ?? 15.00;
            return $prod;
        });

        return view('empleado.venta', compact('clientes', 'productos'));
    }

    public function storeVenta(Request $request)
    {
        $request->validate([
            'ci' => 'nullable|exists:Cliente,ci',
            'metodo_pago' => 'required|in:efectivo,qr',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:Producto,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function() use ($request) {
            $totalMonto = 0;
            
            // 1. Validate stocks first
            foreach ($request->productos as $p) {
                $prodId = $p['id_producto'];
                $qty = $p['cantidad'];

                $availableStock = DB::table('Producto_Almacen')
                    ->where('id_producto', $prodId)
                    ->sum('stock');

                if ($availableStock < $qty) {
                    $producto = Producto::find($prodId);
                    throw new \Exception("Stock insuficiente para el producto: {$producto->nombre}. Stock disponible: {$availableStock}");
                }

                $totalMonto += $qty * $p['precio'];
            }

            // 2. Create NotaVenta
            $notaVenta = NotaVenta::create([
                'fecha' => now(),
                'monto' => $totalMonto,
                'id_usuario' => auth()->id(),
                'ci' => $request->ci,
            ]);

            // 3. Save DetalleVenta & Deduct Stock
            foreach ($request->productos as $p) {
                $prodId = $p['id_producto'];
                $qty = $p['cantidad'];

                // Insert detail
                DB::table('DetalleVenta')->insert([
                    'id_venta' => $notaVenta->id_venta,
                    'id_producto' => $prodId,
                    'cantidad' => $qty,
                    'precio' => $p['precio'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Deduct stock from warehouses (greedy deduction)
                $stockDeduction = $qty;
                $almacenes = DB::table('Producto_Almacen')
                    ->where('id_producto', $prodId)
                    ->where('stock', '>', 0)
                    ->get();

                foreach ($almacenes as $alm) {
                    if ($stockDeduction <= 0) break;

                    if ($alm->stock >= $stockDeduction) {
                        DB::table('Producto_Almacen')
                            ->where('id_producto', $prodId)
                            ->where('id_almacen', $alm->id_almacen)
                            ->update(['stock' => $alm->stock - $stockDeduction]);
                        $stockDeduction = 0;
                    } else {
                        DB::table('Producto_Almacen')
                            ->where('id_producto', $prodId)
                            ->where('id_almacen', $alm->id_almacen)
                            ->update(['stock' => 0]);
                        $stockDeduction -= $alm->stock;
                    }
                }
            }

            // Redirect details
            return response()->json([
                'success' => true,
                'message' => 'Venta registrada con éxito.',
                'id_venta' => $notaVenta->id_venta,
                'metodo_pago' => $request->metodo_pago
            ]);
        });
    }

    public function ticketVenta($id)
    {
        $venta = NotaVenta::with(['cliente', 'usuario', 'productos'])->findOrFail($id);
        return view('empleado.ticket_venta', compact('venta'));
    }

    // --- POS Servicios ---
    public function servicio()
    {
        $clientes = Cliente::with('mascotas')->get();
        $servicios = Servicio::all();
        return view('empleado.servicio', compact('clientes', 'servicios'));
    }

    public function storeServicio(Request $request)
    {
        $request->validate([
            'ci' => 'required|exists:Cliente,ci',
            'id_mascota' => 'required|exists:Mascota,id_mascota',
            'metodo_pago' => 'required|in:efectivo,qr',
            'servicios' => 'required|array|min:1',
            'servicios.*.id_servicio' => 'required|exists:Servicio,id_servicio',
            'servicios.*.cantidad' => 'required|integer|min:1',
            'servicios.*.precio' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function() use ($request) {
            $totalMonto = 0;
            foreach ($request->servicios as $s) {
                $totalMonto += $s['cantidad'] * $s['precio'];
            }

            // Create NotaServicio
            $notaServicio = NotaServicio::create([
                'fecha' => now(),
                'monto' => $totalMonto,
                'id_usuario' => auth()->id(),
                'ci' => $request->ci,
                'id_mascota' => $request->id_mascota,
            ]);

            // Save DetalleServicio
            foreach ($request->servicios as $s) {
                DB::table('DetalleServicio')->insert([
                    'id_nota' => $notaServicio->id_nota,
                    'id_servicio' => $s['id_servicio'],
                    'cantidad' => $s['cantidad'],
                    'precio' => $s['precio'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Servicio registrado con éxito.',
                'id_nota' => $notaServicio->id_nota,
                'metodo_pago' => $request->metodo_pago
            ]);
        });
    }

    public function ticketServicio($id)
    {
        $servicio = NotaServicio::with(['cliente', 'usuario', 'mascota', 'servicios'])->findOrFail($id);
        return view('empleado.ticket_servicio', compact('servicio'));
    }
}
