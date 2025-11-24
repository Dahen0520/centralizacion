<?php

namespace App\Http\Controllers;

use App\Models\TipoOrganizacion;
use Illuminate\Http\Request;

class TipoOrganizacionController extends Controller
{

    public function index(Request $request)
    {
        $query = $request->input('search');

          $tiposOrganizacion = TipoOrganizacion::when($query, function ($q) use ($query) {
                return $q->where('nombre', 'like', '%' . $query . '%');
            })
            ->latest() 
            ->paginate(10); 

        if ($request->ajax()) {
            return response()->json([
                'table_rows' => view('tipo-organizacions.partials.tipo_organizacion_table_rows', compact('tiposOrganizacion'))->render(),
                'pagination_links' => $tiposOrganizacion->links()->render(),
            ]);
        }

        return view('tipo-organizacions.index', compact('tiposOrganizacion'));
    }

    public function create()
    {
        return view('tipo-organizacions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        TipoOrganizacion::create($request->all());

        return redirect()->route('tipo-organizacions.index')->with('success', 'Tipo de organización creado exitosamente.');
    }

    public function show(TipoOrganizacion $tipoOrganizacion)
    {
        return view('tipo-organizacions.show', compact('tipoOrganizacion'));
    }

    public function edit(TipoOrganizacion $tipoOrganizacion)
    {
        return view('tipo-organizacions.edit', compact('tipoOrganizacion'));
    }

    public function update(Request $request, TipoOrganizacion $tipoOrganizacion)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $tipoOrganizacion->update($request->all());

        return redirect()->route('tipo-organizacions.index')->with('success', 'Tipo de organización actualizado exitosamente.');
    }

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