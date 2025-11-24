<?php

namespace App\Http\Controllers;

use App\Models\Subcategoria;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubcategoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Subcategoria::with('categoria');

        if ($request->has('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $subcategorias = $query->paginate(10);

        if ($request->ajax()) {
            $table_rows = view('subcategorias.partials.subcategorias_table_rows', compact('subcategorias'))->render();
            $pagination_links = $subcategorias->links()->toHtml();
            
            return response()->json([
                'table_rows' => $table_rows,
                'pagination_links' => $pagination_links,
            ]);
        }

        return view('subcategorias.index', compact('subcategorias'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        return view('subcategorias.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:255|unique:subcategorias,nombre',
        ]);

        Subcategoria::create($request->all());

        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría creada exitosamente.');
    }

    public function show(Subcategoria $subcategoria)
    {
        return view('subcategorias.show', compact('subcategoria'));
    }

    public function edit(Subcategoria $subcategoria)
    {
        $categorias = Categoria::all();
        return view('subcategorias.edit', compact('subcategoria', 'categorias'));
    }

    public function update(Request $request, Subcategoria $subcategoria)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subcategorias')->ignore($subcategoria->id),
            ],
        ]);

        $subcategoria->update($request->all());

        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría actualizada exitosamente.');
    }

    public function destroy(Subcategoria $subcategoria)
    {
        $subcategoria->delete();
        return response()->json(['success' => true, 'message' => 'Subcategoría eliminada exitosamente.']);
    }
}