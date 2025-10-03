<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Models\Producto;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarcaController extends Controller
{
    /**
     * Muestra una lista de todas las marcas con paginación y búsqueda.
     */
    public function index(Request $request)
    {
        // 1. Obtener el estado, por defecto 'aprobado'
        $currentStatus = $request->input('estado', 'aprobado');

        $query = Marca::with('producto.subcategoria.categoria', 'empresa');

        // 2. Aplicar filtro de estado (excepto si es 'todos')
        if ($currentStatus !== 'todos' && in_array($currentStatus, ['aprobado', 'pendiente', 'rechazado'])) {
            $query->where('estado', $currentStatus);
        }

        // Lógica de búsqueda
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->whereHas('producto', function ($q) use ($searchTerm) {
                $q->where('nombre', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // 3. Obtener los conteos de marcas para todos los estados
        $statusCounts = Marca::selectRaw('estado, count(*) as count')
                               ->groupBy('estado')
                               ->pluck('count', 'estado')
                               ->toArray();
        
        // Agregar el conteo total
        $statusCounts['todos'] = array_sum($statusCounts);
        $statusCounts['aprobado'] = $statusCounts['aprobado'] ?? 0;
        $statusCounts['pendiente'] = $statusCounts['pendiente'] ?? 0;
        $statusCounts['rechazado'] = $statusCounts['rechazado'] ?? 0;


        // Paginación
        $marcas = $query->paginate(15);
        
        // 4. Calcular el índice inicial de paginación para la numeración de filas
        $start_index = ($marcas->currentPage() - 1) * $marcas->perPage() + 1;


        // Si la solicitud es AJAX, devuelve una respuesta JSON con el HTML de la tabla y la paginación
        if ($request->ajax()) {
            return response()->json([
                'table_rows' => view('marcas.partials.marcas_table_rows', compact('marcas', 'start_index'))->render(),
                // Pasa el estado actual y búsqueda a los links de paginación
                'pagination_links' => $marcas->appends(['estado' => $currentStatus, 'search' => $request->search])->links()->toHtml(),
                'marcas_count' => $marcas->total(),
                'status_counts' => $statusCounts, 
            ]);
        }

        // Pasa los conteos y el índice a la vista principal
        return view('marcas.index', compact('marcas', 'currentStatus', 'statusCounts', 'start_index'));
    }

    /**
     * Muestra el formulario para crear una nueva marca.
     */
    public function create()
    {
        $productos = Producto::all();
        $empresas = Empresa::all();
        return view('marcas.create', compact('productos', 'empresas'));
    }

    /**
     * Almacena una nueva marca en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'empresa_id' => 'required|exists:empresas,id',
            'estado' => 'required|in:pendiente,aprobado,rechazado',
        ]);
        
        $codigoMarca = Str::random(10);
        while (Marca::where('codigo_marca', $codigoMarca)->exists()) {
            $codigoMarca = Str::random(10);
        }

        Marca::create([
            'producto_id' => $request->producto_id,
            'empresa_id' => $request->empresa_id,
            'codigo_marca' => $codigoMarca,
            'estado' => $request->estado,
        ]);

        return redirect()->route('marcas.index')->with('success', 'Marca creada exitosamente.');
    }

    /**
     * Muestra los detalles de una marca específica.
     */
    public function show(Marca $marca)
    {
        return view('marcas.show', compact('marca'));
    }

    /**
     * Muestra el formulario para editar un municipio existente.
     */
    public function edit(Marca $marca)
    {
        $productos = Producto::all();
        $empresas = Empresa::all();
        return view('marcas.edit', compact('marca', 'productos', 'empresas'));
    }

    /**
     * Actualiza un municipio en la base de datos.
     */
    public function update(Request $request, Marca $marca)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'empresa_id' => 'required|exists:empresas,id',
            'estado' => 'required|in:pendiente,aprobado,rechazado',
        ]);
        
        $marca->update($request->all());

        return redirect()->route('marcas.index')->with('success', 'Marca actualizada exitosamente.');
    }

    /**
     * Elimina un municipio de la base de datos.
     */
    public function destroy(Marca $marca)
    {
        $marca->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Marca eliminada exitosamente.']);
        }

        return redirect()->route('marcas.index')->with('success', 'Marca eliminada exitosamente.');
    }
}