<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Tienda;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    /**
     * Muestra la interfaz del Punto de Venta (POS).
     */
    public function create()
    {
        $tiendas = Tienda::all();
        return view('ventas.pos', compact('tiendas'));
    }

    /**
     * Devuelve los productos de inventario (Marca/Producto) disponibles para la venta en una tienda.
     */
    public function getProductosParaVenta($tienda_id)
    {
        // Trae todos los productos inventariados en esa tienda CON stock > 0
        $inventarios = Inventario::with(['marca.producto'])
                                 ->where('tienda_id', $tienda_id)
                                 ->where('stock', '>', 0)
                                 ->get();

        // Mapeamos los resultados a un formato simple para JavaScript
        $productos = $inventarios->map(function ($item) {
            return [
                'inventario_id' => $item->id,
                'producto_nombre' => $item->marca->producto->nombre ?? 'N/A',
                'codigo_marca' => $item->marca->codigo_marca,
                'stock_actual' => (int) $item->stock,
                'precio' => (float) $item->precio,
            ];
        });

        return response()->json($productos);
    }

    /**
     * Almacena una nueva venta (Transacción) y actualiza el inventario.
     */
    public function store(Request $request)
    {
        // 1. Validar la estructura de la venta
        $request->validate([
            'tienda_id' => 'required|exists:tiendas,id',
            'total_venta' => 'required|numeric|min:0',
            'detalles' => 'required|array|min:1',
            'detalles.*.inventario_id' => 'required|exists:inventarios,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 2. Crear el encabezado de la venta
            $venta = Venta::create([
                'tienda_id' => $request->tienda_id,
                'fecha_venta' => now(),
                'total_venta' => $request->total_venta,
                'usuario_id' => auth()->id(), 
            ]);

            // 3. Procesar los detalles y actualizar inventario
            foreach ($request->detalles as $detalle) {
                // Bloqueamos la fila de inventario (crucial para stock)
                $inventario = Inventario::lockForUpdate()->find($detalle['inventario_id']);

                if (!$inventario || $inventario->stock < $detalle['cantidad']) {
                    DB::rollBack();
                    $productoNombre = $inventario ? ($inventario->marca->producto->nombre ?? 'Producto sin nombre') : 'Producto Desconocido';
                    throw ValidationException::withMessages([
                        'stock' => "Stock insuficiente para el producto '{$productoNombre}'. Stock disponible: {$inventario->stock}, Cantidad solicitada: {$detalle['cantidad']}."
                    ]);
                }

                // Registrar el detalle de la venta
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'inventario_id' => $detalle['inventario_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $detalle['cantidad'] * $detalle['precio_unitario'],
                ]);

                // 4. Actualizar el stock: descontar la cantidad vendida
                $inventario->decrement('stock', $detalle['cantidad']);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Venta registrada exitosamente.', 'venta_id' => $venta->id]);

        } catch (ValidationException $e) {
             DB::rollBack();
             return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al procesar la venta: ' . $e->getMessage()], 500);
        }
    }
    
    // Métodos estándar para la gestión del historial de ventas
    public function index()
    {
        // Precargamos la tienda y el usuario que registró la venta
        $ventas = Venta::with(['tienda', 'usuario'])
                       ->orderBy('fecha_venta', 'desc')
                       ->paginate(15);
        
        return view('ventas.index', compact('ventas'));
    }
    public function show(Venta $venta)
    {
        // Precargamos los detalles de la venta y las relaciones anidadas
        $venta->load([
            'tienda', 
            'usuario',
            'detalles.inventario.marca.producto' // Accede al Producto desde el DetalleVenta
        ]);
        
        return view('ventas.show', compact('venta'));
    }

        public function destroy(Venta $venta)
    {
        // Usamos una transacción para asegurar que, si el stock se revierte, la venta también se anule.
        DB::beginTransaction();

        try {
            // 1. Cargar los detalles de la venta para saber qué stock devolver
            $venta->load('detalles');

            // 2. Devolver el stock por cada detalle de venta
            foreach ($venta->detalles as $detalle) {
                // Bloqueamos la fila de inventario para asegurar la consistencia del stock
                $inventario = Inventario::lockForUpdate()->find($detalle->inventario_id);

                if ($inventario) {
                    // Aumentar el stock con la cantidad vendida
                    $inventario->increment('stock', $detalle->cantidad);
                }
                // Si el inventario no se encuentra, se asume que fue eliminado, pero la venta debe anularse.
            }

            // 3. Eliminar (Anular) el registro de la venta (eliminará los detalles por 'onDelete: cascade')
            $venta->delete();

            DB::commit();

            // Redirección al historial de ventas
            return redirect()->route('ventas.index')
                ->with('success', 'Venta #' . $venta->id . ' ha sido ANULADA y el stock devuelto exitosamente. 🔄');

        } catch (\Exception $e) {
            DB::rollBack();

            // Si falla, se redirige con un mensaje de error detallado.
            return redirect()->route('ventas.index')
                ->with('error', 'Error al anular la venta. La transacción fue cancelada. Detalle: ' . $e->getMessage());
        }
    }
}