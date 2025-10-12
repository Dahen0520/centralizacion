<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Tienda;
use App\Models\Inventario;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    /**
     * Mostrar la interfaz del Punto de Venta.
     */
    public function create()
    {
        $tiendas = Tienda::all();
        return view('ventas.pos', compact('tiendas'));
    }

    /**
     * Obtener productos disponibles en una tienda.
     */
    public function getProductosParaVenta($tienda_id)
    {
        $inventarios = Inventario::with(['marca.producto'])
            ->where('tienda_id', $tienda_id)
            ->where('stock', '>', 0)
            ->get();

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
     * Guardar una nueva venta.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tienda_id' => 'required|exists:tiendas,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'total_venta' => 'required|numeric|min:0',
            'detalles' => 'required|array|min:1',
            'detalles.*.inventario_id' => 'required|exists:inventarios,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $clienteId = $request->cliente_id > 0 ? $request->cliente_id : null;

            $venta = Venta::create([
                'tienda_id' => $request->tienda_id,
                'cliente_id' => $clienteId,
                'fecha_venta' => now(),
                'total_venta' => $request->total_venta,
                'usuario_id' => auth()->id(),
            ]);

            foreach ($request->detalles as $detalle) {
                $inventario = Inventario::lockForUpdate()->find($detalle['inventario_id']);

                if (!$inventario || $inventario->stock < $detalle['cantidad']) {
                    DB::rollBack();
                    $nombre = $inventario ? ($inventario->marca->producto->nombre ?? 'Producto sin nombre') : 'Desconocido';
                    throw ValidationException::withMessages([
                        'stock' => "Stock insuficiente para '{$nombre}'. Disponible: {$inventario->stock}, solicitado: {$detalle['cantidad']}.",
                    ]);
                }

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'inventario_id' => $detalle['inventario_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $detalle['cantidad'] * $detalle['precio_unitario'],
                ]);

                $inventario->decrement('stock', $detalle['cantidad']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada exitosamente.',
                'venta_id' => $venta->id
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al procesar la venta: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Registrar un nuevo cliente desde el POS.
     */
    public function storeCliente(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'identificacion' => 'nullable|string|max:50|unique:clientes,identificacion',
            'email' => 'nullable|email|max:255|unique:clientes,email',
            'telefono' => 'nullable|string|max:20',
        ]);

        try {
            $cliente = Cliente::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cliente guardado correctamente.',
                'cliente' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'identificacion' => $cliente->identificacion ?? 'N/A',
                ],
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = $e->getCode() == 23000
                ? 'La Identificación o el Email ya están registrados.'
                : 'Error al guardar el cliente.';
            return response()->json(['success' => false, 'message' => $msg], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Buscar clientes por nombre o RTN/identificación.
     */
    public function buscarClientes(Request $request)
    {
        $query = $request->get('query');

        if (empty($query)) {
            return response()->json([]);
        }

        $clientes = Cliente::where('nombre', 'like', "%$query%")
            ->orWhere('identificacion', 'like', "%$query%")
            ->limit(10)
            ->get(['id', 'nombre', 'identificacion']);

        if ($clientes->isEmpty()) {
            return response()->json([[
                'id' => 0,
                'nombre' => 'Cliente Genérico / Sin Registro',
                'identificacion' => 'N/A',
            ]]);
        }

        return response()->json($clientes);
    }

    /**
     * Listar ventas (historial).
     */
    public function index()
    {
        $ventas = Venta::with(['tienda', 'usuario', 'cliente'])
            ->orderBy('fecha_venta', 'desc')
            ->paginate(15);

        return view('ventas.index', compact('ventas'));
    }

    /**
     * Mostrar detalles de una venta.
     */
    public function show(Venta $venta)
    {
        $venta->load([
            'tienda',
            'usuario',
            'cliente',
            'detalles.inventario.marca.producto',
        ]);

        return view('ventas.show', compact('venta'));
    }

    /**
     * Anular una venta y devolver stock.
     */
    public function destroy(Venta $venta)
    {
        DB::beginTransaction();

        try {
            $venta->load('detalles');

            foreach ($venta->detalles as $detalle) {
                $inventario = Inventario::lockForUpdate()->find($detalle->inventario_id);
                if ($inventario) {
                    $inventario->increment('stock', $detalle->cantidad);
                }
            }

            $venta->delete();
            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', "Venta #{$venta->id} anulada y stock devuelto.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ventas.index')
                ->with('error', 'Error al anular la venta: ' . $e->getMessage());
        }
    }
}
