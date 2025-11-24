<?php

namespace App\Http\Controllers;

use App\Models\Impuesto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ImpuestoController extends Controller
{

    public function index(Request $request)
    {
        $query = Impuesto::query();
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where('nombre', 'like', '%' . $searchTerm . '%');
        }

        $impuestos = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'table_rows' => view('impuestos.partials.table_rows', compact('impuestos'))->render(),
                'pagination_links' => $impuestos->links()->toHtml(),
                'impuestos_count' => $impuestos->total(),
            ]);
        }
        
        return view('impuestos.index', compact('impuestos'));
    }

    public function create()
    {
        return view('impuestos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:impuestos,nombre',
            'porcentaje' => 'required|numeric|min:0|max:100', 
        ]);

        Impuesto::create($request->all());

        return redirect()->route('impuestos.index')
            ->with('success', 'Impuesto creado exitosamente.');
    }

    public function edit(Impuesto $impuesto)
    {
        return view('impuestos.edit', compact('impuesto'));
    }

    public function update(Request $request, Impuesto $impuesto)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('impuestos')->ignore($impuesto->id),
            ],
            'porcentaje' => 'required|numeric|min:0|max:100',
        ]);

        $impuesto->update($request->all());

        return redirect()->route('impuestos.index')
            ->with('success', 'Impuesto actualizado exitosamente.');
    }

    public function destroy(Impuesto $impuesto)
    {
        try {
            $impuesto->delete();
            return response()->json(['success' => true, 'message' => 'Impuesto eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: No se puede eliminar este impuesto porque está asociado a uno o más productos.'], 409);
        }
    }
}
