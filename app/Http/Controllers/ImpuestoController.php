<?php

namespace App\Http\Controllers;

use App\Models\Impuesto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ImpuestoController extends Controller
{
    /**
     * Muestra una lista de todos los impuestos con paginación y búsqueda.
     */
    public function index(Request $request)
    {
        $query = Impuesto::query();

        // Lógica de búsqueda por nombre
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where('nombre', 'like', '%' . $searchTerm . '%');
        }

        // Obtener los impuestos paginados (15 por página, como en productos)
        $impuestos = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            // Si es una petición AJAX, solo devuelve las filas y enlaces de paginación
            return response()->json([
                'table_rows' => view('impuestos.partials.table_rows', compact('impuestos'))->render(),
                'pagination_links' => $impuestos->links()->toHtml(),
                'impuestos_count' => $impuestos->total(),
            ]);
        }
        
        return view('impuestos.index', compact('impuestos'));
    }

    /**
     * Muestra el formulario para crear un nuevo impuesto.
     */
    public function create()
    {
        return view('impuestos.create');
    }

    /**
     * Almacena un nuevo impuesto en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:impuestos,nombre',
            // El porcentaje debe ser un número decimal, requerido y positivo
            'porcentaje' => 'required|numeric|min:0|max:100', 
        ]);

        Impuesto::create($request->all());

        return redirect()->route('impuestos.index')
            ->with('success', 'Impuesto creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un impuesto existente.
     */
    public function edit(Impuesto $impuesto)
    {
        return view('impuestos.edit', compact('impuesto'));
    }

    /**
     * Actualiza un impuesto existente en la base de datos.
     */
    public function update(Request $request, Impuesto $impuesto)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                // Validación para asegurar que el nombre sea único, excepto para el impuesto actual
                Rule::unique('impuestos')->ignore($impuesto->id),
            ],
            'porcentaje' => 'required|numeric|min:0|max:100',
        ]);

        $impuesto->update($request->all());

        return redirect()->route('impuestos.index')
            ->with('success', 'Impuesto actualizado exitosamente.');
    }

    /**
     * Elimina un impuesto de la base de datos.
     */
    public function destroy(Impuesto $impuesto)
    {
        try {
            // Intenta eliminar. Si el impuesto está asociado a un producto, fallará por la FK (onDelete('restrict')).
            $impuesto->delete();
            return response()->json(['success' => true, 'message' => 'Impuesto eliminado exitosamente.']);
        } catch (\Exception $e) {
            // Manejar error de clave foránea
            return response()->json(['success' => false, 'message' => 'Error: No se puede eliminar este impuesto porque está asociado a uno o más productos.'], 409);
        }
    }
}
