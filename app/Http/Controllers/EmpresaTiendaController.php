<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Tienda;
use App\Models\EmpresaTienda;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; 

class EmpresaTiendaController extends Controller
{

    public function index(Request $request)
    {
        $query = $request->input('search');
        $filtroTienda = $request->input('tienda_id');
        $filtroEstado = $request->input('estado');

        $asociaciones = EmpresaTienda::when($query, function ($q) use ($query) {
                $q->whereHas('empresa', function ($empresaQuery) use ($query) {
                    $empresaQuery->where('nombre_negocio', 'like', '%' . $query . '%');
                })
                ->orWhereHas('tienda', function ($tiendaQuery) use ($query) {
                    $tiendaQuery->where('nombre', 'like', '%' . $query . '%');
                })
                ->orWhere('codigo_asociacion', 'like', '%' . $query . '%'); 
            })
            ->when($filtroTienda, function ($q) use ($filtroTienda) {
                $q->where('tienda_id', $filtroTienda);
            })
            ->when($filtroEstado, function ($q) use ($filtroEstado) {
                $q->where('estado', $filtroEstado);
            })
            ->with(['empresa', 'tienda'])
            ->paginate(10)
            ->onEachSide(1)
            ->appends(request()->query());

        $tiendas = Tienda::all();
        
        if ($request->ajax()) {
            return response()->json([
                'table_rows' => view('empresa-tienda.partials.association_table_rows', compact('asociaciones'))->render(),
                'pagination_links' => $asociaciones->links()->render(),
            ]);
        }
        
        return view('empresa-tienda.index', compact('asociaciones', 'tiendas'));
    }

    public function create()
    {
        $empresas = Empresa::select('id', 'nombre_negocio')->get();
        $tiendas = Tienda::select('id', 'nombre')->get();

        return view('empresa-tienda.create', compact('empresas', 'tiendas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'tienda_id' => 'required|exists:tiendas,id',
        ]);
    
        $exists = EmpresaTienda::where('empresa_id', $request->empresa_id)
            ->where('tienda_id', $request->tienda_id)
            ->exists();
    
        if ($exists) {
            return back()->withInput()->withErrors([
                'asociacion_existente' => 'La asociación entre esta empresa y esta tienda ya existe.'
            ]);
        }
    
        $empresa = Empresa::find($request->empresa_id);

        do {
            $codigoAsociacion = Str::random(10);
        } while (EmpresaTienda::where('codigo_asociacion', $codigoAsociacion)->exists());
    
        $empresa->tiendas()->attach($request->tienda_id, [
            'estado' => $request->input('estado', 'pendiente'),
            'codigo_asociacion' => $codigoAsociacion, 
        ]);
    
        return redirect()->route('asociaciones.index')->with('success', 'Asociación creada exitosamente.');
    }

    public function show(Empresa $empresa, Tienda $tienda)
    {
        $asociacion = EmpresaTienda::where('empresa_id', $empresa->id)
                                   ->where('tienda_id', $tienda->id)
                                   ->firstOrFail();

        return view('empresa-tienda.show', compact('empresa', 'tienda', 'asociacion'));
    }

    public function edit(Empresa $empresa, Tienda $tienda)
    {
        $asociacion = EmpresaTienda::where('empresa_id', $empresa->id)
                                   ->where('tienda_id', $tienda->id)
                                   ->firstOrFail();
    
        $empresas = Empresa::select('id', 'nombre_negocio')->get();
        $tiendas = Tienda::select('id', 'nombre')->get();
        
        return view('empresa-tienda.edit', compact('empresa', 'tienda', 'asociacion', 'empresas', 'tiendas'));
    }

    public function update(Request $request, Empresa $empresa, Tienda $tienda)
    {

        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'tienda_id' => 'required|exists:tiendas,id',
            'estado' => 'required|in:pendiente,aprobado,rechazado',
        ]);
        
        $asociacionAntigua = EmpresaTienda::where('empresa_id', $empresa->id)
                                ->where('tienda_id', $tienda->id)
                                ->firstOrFail();

        $nuevaAsociacionExistente = EmpresaTienda::where('empresa_id', $request->empresa_id)
                                                 ->where('tienda_id', $request->tienda_id)
                                                 ->first();

        if ($nuevaAsociacionExistente && ($nuevaAsociacionExistente->empresa_id != $empresa->id || $nuevaAsociacionExistente->tienda_id != $tienda->id)) {
            throw ValidationException::withMessages([
                'asociacion_existente' => 'La asociación que intentas crear ya existe.'
            ]);
        }

        if ($asociacionAntigua->empresa_id != $request->empresa_id || $asociacionAntigua->tienda_id != $request->tienda_id) {
    
            $empresa->tiendas()->detach($tienda->id);
            $nuevaEmpresa = Empresa::findOrFail($request->empresa_id);
            $nuevaEmpresa->tiendas()->attach($request->tienda_id, [
                'estado' => $request->estado,
                'codigo_asociacion' => $asociacionAntigua->codigo_asociacion, 
            ]);
        } else {
             $empresa->tiendas()->updateExistingPivot($tienda->id, [
                 'estado' => $request->estado,
             ]);
        }
        
        return redirect()->route('asociaciones.index')->with('success', 'Asociación actualizada exitosamente.');
    }

    public function destroy(Empresa $empresa, Tienda $tienda)
    {
        try {
            $empresa->tiendas()->detach($tienda->id);
            return response()->json(['success' => true, 'message' => 'La vinculación ha sido eliminada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al eliminar la vinculación.'], 500);
        }
    }
}
