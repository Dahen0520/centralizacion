<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Marca;
use App\Models\Tienda;
use App\Models\Empresa;         // Necesario para obtener los modelos de Empresa
use App\Models\EmpresaTienda;   // Necesario para la asociación Tienda-Empresa
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventarioController extends Controller
{
    /**
     * Muestra una lista de todos los registros de inventario con paginación, búsqueda y filtro.
     */
    public function index(Request $request)
    {
        // Precargar relaciones: marca, marca.producto, tienda, Y AHORA marca.empresa
        $query = Inventario::with(['marca.producto', 'tienda', 'marca.empresa']);

        // 1. FILTRO POR BÚSQUEDA (Producto o Tienda)
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            
            $query->where(function ($q) use ($searchTerm) {
                // Búsqueda por Nombre de Producto (a través de Marca)
                $q->whereHas('marca.producto', function ($qr) use ($searchTerm) {
                    $qr->where('nombre', 'like', '%' . $searchTerm . '%');
                })
                // O Búsqueda por Nombre de Tienda
                ->orWhereHas('tienda', function ($qr) use ($searchTerm) {
                    $qr->where('nombre', 'like', '%' . $searchTerm . '%');
                });
            });
        }
        
        // 2. FILTRO POR TIENDA (Select)
        if ($request->has('tienda_id') && $request->tienda_id != '') {
            $query->where('tienda_id', $request->tienda_id);
        }

        $inventarios = $query->paginate(15);
        $startIndex = ($inventarios->currentPage() - 1) * $inventarios->perPage() + 1;

        if ($request->ajax()) {
            return response()->json([
                // Las vistas parciales deben manejar el nombre de la empresa
                'table_rows' => view('inventario.partials.inventario_table_rows', compact('inventarios', 'startIndex'))->render(),
                'pagination_links' => $inventarios->links()->toHtml(),
                'inventarios_count' => $inventarios->total(),
            ]);
        }
        
        $marcas = Marca::all(); 
        $tiendas = Tienda::all(); // Necesario para llenar el select de filtro

        return view('inventario.index', compact('inventarios', 'marcas', 'tiendas', 'startIndex'));
    }

    /**
     * Muestra el formulario para crear un nuevo registro de inventario.
     * Solo cargamos las Tiendas, las Empresas y Marcas se cargan por AJAX.
     */
    public function create()
    {
        $tiendas = Tienda::all();
        
        return view('inventario.create', compact('tiendas'));
    }

    // ------------------------------------------------------------------------------------------------------------------
    // MÉTODOS AJAX PARA LA SELECCIÓN EN CASCADA (TIENDA -> EMPRESA -> MARCA)
    // ------------------------------------------------------------------------------------------------------------------
    
    /**
     * Devuelve las Empresas asociadas a la Tienda seleccionada (Usando EmpresaTienda).
     * Usa 'nombre_negocio' para obtener el nombre de la empresa.
     * @param int $tienda_id
     */
    public function getEmpresasPorTienda($tienda_id)
    {
        // Obtener los IDs de las empresas asociadas a la tienda
        $empresaIds = EmpresaTienda::where('tienda_id', $tienda_id)
                                              ->pluck('empresa_id');

        // Obtener los modelos de Empresa
        $empresas = Empresa::whereIn('id', $empresaIds)
                                      ->get(['id', 'nombre_negocio']); 

        return response()->json($empresas);
    }

    /**
     * Devuelve las Marcas (Productos) de la Empresa seleccionada que NO estén ya inventariadas en la Tienda.
     * @param int $empresa_id
     * @param int $tienda_id
     */
    public function getMarcasPorEmpresa($empresa_id, $tienda_id)
    {
        // 1. Obtener los IDs de las marcas que YA existen en el inventario para la tienda específica
        $marcasYaInventariadas = Inventario::where('tienda_id', $tienda_id)->pluck('marca_id');

        // 2. Obtener las Marcas de la Empresa seleccionada que NO estén en la lista anterior
        $marcasDisponibles = Marca::with('producto')
                                  ->where('empresa_id', $empresa_id)
                                  ->whereNotIn('id', $marcasYaInventariadas) // Filtra las que ya tienen stock inicial
                                  ->get();

        return response()->json($marcasDisponibles);
    }

    // ------------------------------------------------------------------------------------------------------------------
    // CRUD ESTÁNDAR
    // ------------------------------------------------------------------------------------------------------------------

    /**
     * Almacena un nuevo registro de inventario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'marca_id' => 'required|exists:marcas,id',
            'tienda_id' => [
                'required',
                'exists:tiendas,id',
                // Validación para evitar duplicados: una Marca solo puede tener un registro inicial por Tienda.
                Rule::unique('inventarios')->where(function ($query) use ($request) {
                    return $query->where('marca_id', $request->marca_id);
                }),
            ],
            'empresa_id' => 'required|exists:empresas,id', 
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        // Asegúrate de que solo se guardan los campos de la tabla 'inventarios'
        Inventario::create($request->only(['marca_id', 'tienda_id', 'precio', 'stock']));

        return redirect()->route('inventarios.index')
            ->with('success', 'Registro de inventario creado exitosamente.');
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
     */
    public function update(Request $request, Inventario $inventario)
    {
        $request->validate([
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $inventario->update($request->only(['precio', 'stock']));

        return redirect()->route('inventarios.index')
            ->with('success', 'Inventario actualizado exitosamente.');
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
}