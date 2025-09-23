<?php

namespace App\Http\Controllers;

use App\Models\Rubro;
use Illuminate\Http\Request;

class RubroController extends Controller
{
    /**
     * Muestra la lista de rubros con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        // Obtiene el término de búsqueda de la solicitud
        $query = $request->input('search');

        // Construye la consulta para filtrar por nombre si hay un término de búsqueda
        $rubros = Rubro::when($query, function ($q) use ($query) {
                return $q->where('nombre', 'like', '%' . $query . '%');
            })
            ->latest() // Ordena por los más recientes
            ->paginate(10); // Pagina los resultados, 10 por página

        // Si la solicitud es AJAX, devuelve las vistas parciales como JSON
        if ($request->ajax()) {
            return response()->json([
                'table_rows' => view('rubros.partials.rubros_table_rows', compact('rubros'))->render(),
                'pagination_links' => $rubros->links()->render(),
            ]);
        }

        // Si es una solicitud normal (primera carga), devuelve la vista completa
        return view('rubros.index', compact('rubros'));
    }

    /**
     * Muestra el formulario para crear un nuevo rubro.
     */
    public function create()
    {
        return view('rubros.create');
    }

    /**
     * Almacena un nuevo rubro en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        Rubro::create($request->all());

        return redirect()->route('rubros.index')->with('success', 'Rubro creado exitosamente.');
    }

    /**
     * Muestra el rubro especificado.
     */
    public function show(Rubro $rubro)
    {
        return view('rubros.show', compact('rubro'));
    }

    /**
     * Muestra el formulario para editar un rubro existente.
     */
    public function edit(Rubro $rubro)
    {
        return view('rubros.edit', compact('rubro'));
    }

    public function update(Request $request, Rubro $rubro)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $rubro->update($request->all());

        return redirect()->route('rubros.index')->with('success', 'Rubro actualizado exitosamente.');
    }

    public function destroy(Rubro $rubro)
    {
        try {
            $rubro->delete();
            return response()->json(['success' => true, 'message' => 'Rubro eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al eliminar el rubro.'], 500);
        }
    }
}