<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Marca;
use App\Models\Tienda;
use App\Models\Empresa;
use App\Models\EmpresaTienda;
use App\Models\MovimientoInventario;
use App\Models\Venta; 
use App\Models\DetalleVenta; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class InventarioController extends Controller
{
    /**
     * Muestra una lista de todos los registros de inventario con paginación, búsqueda y filtro.
     */
    public function index(Request $request)
    {
        // Precargar relaciones
        $query = Inventario::with(['marca.producto', 'tienda', 'marca.empresa']);

        // 1. FILTRO POR BÚSQUEDA
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('marca.producto', function ($qr) use ($searchTerm) {
                    $qr->where('nombre', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('tienda', function ($qr) use ($searchTerm) {
                    $qr->where('nombre', 'like', '%' . $searchTerm . '%');
                });
            });
        }
        
        // 2. FILTRO POR TIENDA
        if ($request->has('tienda_id') && $request->tienda_id != '') {
            $query->where('tienda_id', $request->tienda_id);
        }

        $inventarios = $query->paginate(15);
        $startIndex = ($inventarios->currentPage() - 1) * $inventarios->perPage() + 1;

        if ($request->ajax()) {
            return response()->json([
                'table_rows' => view('inventario.partials.inventario_table_rows', compact('inventarios', 'startIndex'))->render(),
                'pagination_links' => $inventarios->links()->toHtml(),
                'inventarios_count' => $inventarios->total(),
            ]);
        }
        
        $marcas = Marca::all(); 
        $tiendas = Tienda::all();

        return view('inventario.index', compact('inventarios', 'marcas', 'tiendas', 'startIndex'));
    }

    /**
     * Muestra el formulario para crear un nuevo registro de inventario.
     */
    public function create()
    {
        $tiendas = Tienda::all();
        
        return view('inventario.create', compact('tiendas'));
    }

    // ------------------------------------------------------------------------------------------------------------------
    // MÉTODOS AJAX PARA LA SELECCIÓN EN CASCADA
    // ------------------------------------------------------------------------------------------------------------------
    
    public function getEmpresasPorTienda($tienda_id)
    {
        $empresaIds = EmpresaTienda::where('tienda_id', $tienda_id)->pluck('empresa_id');
        $empresas = Empresa::whereIn('id', $empresaIds)->get(['id', 'nombre_negocio']); 
        return response()->json($empresas);
    }

    public function getMarcasPorEmpresa($empresa_id, $tienda_id)
    {
        $marcasYaInventariadas = Inventario::where('tienda_id', $tienda_id)->pluck('marca_id');

        $marcasDisponibles = Marca::with('producto')
                                  ->where('empresa_id', $empresa_id)
                                  ->whereNotIn('id', $marcasYaInventariadas)
                                  ->get();

        return response()->json($marcasDisponibles);
    }

    // ------------------------------------------------------------------------------------------------------------------
    // CRUD ESTÁNDAR con Registro de Movimiento Inicial
    // ------------------------------------------------------------------------------------------------------------------

    /**
     * Almacena un nuevo registro de inventario y registra el movimiento de stock inicial.
     */
    public function store(Request $request)
    {
        $request->validate([
            'marca_id' => 'required|exists:marcas,id',
            'tienda_id' => [
                'required',
                'exists:tiendas,id',
                Rule::unique('inventarios')->where(function ($query) use ($request) {
                    return $query->where('marca_id', $request->marca_id);
                }),
            ],
            'empresa_id' => 'required|exists:empresas,id', 
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0', // El stock inicial
        ]);

        $data = $request->only(['marca_id', 'tienda_id', 'precio', 'stock']);
        $stockInicial = $data['stock'];

        DB::beginTransaction();
        try {
            // 1. Crear el registro de Inventario (stock base)
            $inventario = Inventario::create($data);

            // 2. Registrar el Movimiento de Inventario Inicial (solo si el stock es > 0)
            if ($stockInicial > 0) {
                MovimientoInventario::create([
                    'inventario_id' => $inventario->id,
                    'tipo_movimiento' => 'ENTRADA',
                    'razon' => 'Stock Inicial / Nuevo Producto',
                    'cantidad' => $stockInicial,
                    'movible_id' => $inventario->id, 
                    'movible_type' => Inventario::class,
                    'usuario_id' => auth()->id(),
                ]);
            }
            
            DB::commit();

            return redirect()->route('inventarios.index')
                ->with('success', 'Registro de inventario creado exitosamente y stock inicial registrado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el registro de inventario: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra los detalles de un registro de inventario.
     */
    public function show(Inventario $inventario)
    {
        $inventario->load(['marca.producto', 'tienda']);
        return view('inventario.show', compact('inventario'));
    }

    /**
     * Muestra el formulario para editar un registro de inventario.
     */
    public function edit(Inventario $inventario)
    {
        $inventario->load(['marca.producto', 'tienda']);
        return view('inventario.edit', compact('inventario'));
    }

    /**
     * Actualiza un registro de inventario existente en la base de datos.
     * ⭐ Solo se permite actualizar el precio para forzar la trazabilidad del stock.
     */
    public function update(Request $request, Inventario $inventario)
    {
        $request->validate([
            'precio' => 'required|numeric|min:0', 
            // El campo 'stock' debe eliminarse del formulario o ignorarse aquí.
        ]);

        // Aseguramos que solo el precio se actualice.
        $inventario->update($request->only(['precio']));
        
        
        if ($request->get('redirect_to') === 'explorar' && $request->has(['empresa_id', 'tienda_id'])) {
            return redirect()->route('inventarios.explorar.inventario', [
                'empresa' => $request->empresa_id,
                'tienda' => $request->tienda_id,
            ])->with('success', 'Precio de inventario actualizado exitosamente. ✅');
        }

        return redirect()->route('inventarios.index')
            ->with('success', 'Precio de inventario actualizado exitosamente.');
    }

    /**
     * Elimina un registro de inventario de la base de datos.
     */
    public function destroy(Inventario $inventario)
    {
        $inventario->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Registro de inventario eliminado exitosamente.']);
        }

        return redirect()->route('inventarios.index')
            ->with('success', 'Registro de inventario eliminado exitosamente.');
    }

    public function explorarTiendas()
    {
        $tiendas = Tienda::all();
        return view('inventario.explorar.index', compact('tiendas'));
    }

    /**
     * Muestra las empresas asociadas a una tienda específica. (Capa 2)
     */
    public function explorarEmpresasPorTienda(Tienda $tienda)
    {
        $tienda->load('empresas');

        return view('inventario.explorar.empresas', [
            'tienda' => $tienda,
            'empresas' => $tienda->empresas
        ]);
    }

    /**
     * Muestra la lista de inventario de una empresa específica dentro de una tienda. (Capa 3)
     */
    public function mostrarInventarioPorEmpresa(Empresa $empresa, Tienda $tienda)
    {
        $inventarios = Inventario::with(['marca.producto'])
                                 ->where('tienda_id', $tienda->id)
                                 ->whereHas('marca', function ($query) use ($empresa) {
                                     $query->where('empresa_id', $empresa->id);
                                 })
                                 ->get(); 

        return view('inventario.explorar.inventario', [
            'inventarios' => $inventarios,
            'empresa' => $empresa,
            'tienda' => $tienda,
        ]);
    }
    
    /**
     * NUEVO MÉTODO: Listar Ventas (Detalle) de una Empresa en una Tienda.
     */
    public function verVentasPorEmpresa(Request $request, Empresa $empresa, Tienda $tienda)
    {
        // 1. Obtener los IDs de inventario que pertenecen a esta empresa en esta tienda
        $inventarioIds = Inventario::where('tienda_id', $tienda->id)
                                   ->whereHas('marca', function ($q) use ($empresa) {
                                       $q->where('empresa_id', $empresa->id);
                                   })
                                   ->pluck('id');

        // 2. Consulta de DetalleVenta (LÍNEAS DE PRODUCTO)
        $query = DetalleVenta::with([
                            'venta' => fn($q) => $q->with('cliente'),
                            'inventario.marca.producto'
                        ])
                        ->whereIn('inventario_id', $inventarioIds)
                        // Asegurarse de que la venta asociada haya sido completada y no anulada/cotización
                        ->whereHas('venta', function ($q) {
                            $q->where('estado', 'COMPLETADA')
                              ->whereNotIn('tipo_documento', ['QUOTE']);
                        })
                        ->orderBy('created_at', 'desc');

        // 3. Aplicar filtros de fecha a través de la relación de Venta
        if ($request->filled('fecha_inicio')) {
            $query->whereHas('venta', fn($q) => $q->whereDate('fecha_venta', '>=', $request->fecha_inicio));
        }
        if ($request->filled('fecha_fin')) {
            $query->whereHas('venta', fn($q) => $q->whereDate('fecha_venta', '<=', $request->fecha_fin));
        }
        
        // 4. Clonar la consulta para obtener los totales de agregación
        $queryForSum = clone $query;
        
        // 🔑 CALCULAR LAS TRES SUMATORIAS
        $totalIngresosNetos = $queryForSum->sum('subtotal_final');
        $totalCantidadVendida = $queryForSum->sum('cantidad');
        $totalSubtotalBase = $queryForSum->sum('subtotal_base');


        // 5. Paginar y obtener los resultados
        $detalles = $query->paginate(15)->withQueryString();

        return view('inventario.explorar.ventas_empresa', [
            'detalles' => $detalles, 
            'empresa' => $empresa,
            'tienda' => $tienda,
            'totalVentasSum' => $totalIngresosNetos,
            'totalCantidadVendida' => $totalCantidadVendida, // 🔑 Nuevo total
            'totalSubtotalBase' => $totalSubtotalBase,       // 🔑 Nuevo total
            'fechaInicio' => $request->fecha_inicio, 
            'fechaFin' => $request->fecha_fin, 
        ]);
    }
}