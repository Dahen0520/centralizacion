<?php

namespace App\Http\Controllers;

use App\Models\Rubro;
use Illuminate\Http\Request;

class RubroController extends Controller
{

    public function index(Request $request)
    {
        $query = $request->input('search');
        $rubros = Rubro::when($query, function ($q) use ($query) {
                return $q->where('nombre', 'like', '%' . $query . '%');
            })
            ->latest() 
            ->paginate(10); 

        if ($request->ajax()) {
            return response()->json([
                'table_rows' => view('rubros.partials.rubros_table_rows', compact('rubros'))->render(),
                'pagination_links' => $rubros->links()->render(),
            ]);
        }

        return view('rubros.index', compact('rubros'));
    }

    public function create()
    {
        return view('rubros.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        Rubro::create($request->all());

        return redirect()->route('rubros.index')->with('success', 'Rubro creado exitosamente.');
    }

    public function show(Rubro $rubro)
    {
        return view('rubros.show', compact('rubro'));
    }

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