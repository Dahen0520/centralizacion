<?php

namespace App\Http\Controllers;

use App\Models\TipoOrganizacion;
use Illuminate\Http\Request;

class TipoOrganizacionController extends Controller
{
    /**
     * Muestra la lista de tipos de organización con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        // Obtiene el término de búsqueda de la solicitud
        $query = $request->input('search');

        // Construye la consulta para filtrar por nombre si hay un término de búsqueda
        $tiposOrganizacion = TipoOrganizacion::when($query, function ($q) use ($query) {
                return $q->where('nombre', 'like', '%' . $query . '%');
            })
            ->latest() // Ordena por los más recientes
            ->paginate(10); // Pagina los resultados, 10 por página

        // Si la solicitud es AJAX, devuelve las vistas parciales como JSON
        if ($request->ajax()) {
            return response()->json([
                'table_rows' => view('tipo-organizacions.partials.tipo_organizacion_table_rows', compact('tiposOrganizacion'))->render(),
                'pagination_links' => $tiposOrganizacion->links()->render(),
            ]);
        }

        // Si es una solicitud normal (primera carga), devuelve la vista completa
        return view('tipo-organizacions.index', compact('tiposOrganizacion'));
    }

    /**
     * Muestra el formulario para crear un nuevo tipo de organización.
     */
    public function create()
    {
        return view('tipo-organizacions.create');
    }

    /**
     * Almacena un nuevo tipo de organización en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        TipoOrganizacion::create($request->all());

        return redirect()->route('tipo-organizacions.index')->with('success', 'Tipo de organización creado exitosamente.');
    }

    /**
     * Muestra el tipo de organización especificado.
     */
    public function show(TipoOrganizacion $tipoOrganizacion)
    {
        return view('tipo-organizacions.show', compact('tipoOrganizacion'));
    }

    /**
     * Muestra el formulario para editar un tipo de organización existente.
     */
    public function edit(TipoOrganizacion $tipoOrganizacion)
    {
        return view('tipo-organizacions.edit', compact('tipoOrganizacion'));
    }

    /**
     * Actualiza el tipo de organización especificado en la base de datos.
     */
    public function update(Request $request, TipoOrganizacion $tipoOrganizacion)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $tipoOrganizacion->update($request->all());

        return redirect()->route('tipo-organizacions.index')->with('success', 'Tipo de organización actualizado exitosamente.');
    }

    /**
     * Elimina el tipo de organización especificado de la base de datos.
     */
    public function destroy(TipoOrganizacion $tipoOrganizacion)
    {
        try {
            $tipoOrganizacion->delete();
            return response()->json(['success' => true, 'message' => 'Tipo de organización eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al eliminar el tipo de organización.'], 500);
        }
    }
}