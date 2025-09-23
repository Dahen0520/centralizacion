<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Categoria::query();

        // Lógica de búsqueda
        if ($request->has('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $categorias = $query->paginate(10);

        // Si la solicitud es AJAX, devuelve solo las filas de la tabla y los enlaces de paginación
        if ($request->ajax()) {
            $table_rows = view('categorias.partials.categorias_table_rows', compact('categorias'))->render();
            $pagination_links = $categorias->links()->toHtml();
            
            return response()->json([
                'table_rows' => $table_rows,
                'pagination_links' => $pagination_links,
            ]);
        }

        // Para una solicitud HTTP normal, devuelve la vista completa
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
        ]);

        Categoria::create($request->all());

        return redirect()->route('categorias.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function show(Categoria $categoria)
    {
        return view('categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
        ]);

        $categoria->update($request->all());

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return response()->json(['success' => true, 'message' => 'Categoría eliminada exitosamente.']);
    }
}