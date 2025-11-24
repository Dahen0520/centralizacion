<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Rubro;
use App\Models\TipoOrganizacion;
use App\Models\Pais;
use App\Models\Afiliado;
use App\Models\Tienda;
use App\Models\EmpresaTienda;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $currentStatus = $request->input('estado', 'aprobado');

        $query = Empresa::query();

        if ($currentStatus !== 'todos' && in_array($currentStatus, ['aprobado', 'pendiente', 'rechazado'])) {
            $query->where('estado', $currentStatus);
        }

        if ($request->has('search')) {
            $query->where('nombre_negocio', 'like', '%' . $request->search . '%');
        }
        
        $statusCounts = Empresa::selectRaw('estado, count(*) as count')
                               ->groupBy('estado')
                               ->pluck('count', 'estado')
                               ->toArray();
        
        $statusCounts['todos'] = array_sum($statusCounts);

        $statusCounts['aprobado'] = $statusCounts['aprobado'] ?? 0;
        $statusCounts['pendiente'] = $statusCounts['pendiente'] ?? 0;
        $statusCounts['rechazado'] = $statusCounts['rechazado'] ?? 0;

        $empresas = $query->with(['afiliado', 'rubro', 'tipoOrganizacion'])->paginate(10);
        
        $start_index = ($empresas->currentPage() - 1) * $empresas->perPage() + 1;


        if ($request->ajax()) {
            return response()->json([
     
                'table_rows' => view('empresas.partials.empresas_table_rows', compact('empresas', 'start_index'))->render(),
                'pagination_links' => $empresas->appends(['estado' => $currentStatus, 'search' => $request->search])->links()->toHtml(),
                'empresas_count' => $empresas->total(), 
          
                'status_counts' => $statusCounts, 
            ]);
        }

        return view('empresas.index', compact('empresas', 'currentStatus', 'statusCounts', 'start_index'));
    }

    public function show(Empresa $empresa)
    {
        return view('empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        $rubros = Rubro::all();
        $tiposOrganizacion = TipoOrganizacion::all();
        $paises = Pais::all();
        
        return view('empresas.edit', [
            'empresa'           => $empresa,
            'rubros'            => $rubros,
            'tiposOrganizacion' => $tiposOrganizacion,
            'paises'            => $paises,
        ]);
    }
    
    public function update(Request $request, Empresa $empresa)
    {
        $request->validate([
            'nombre_negocio'       => 'required|string|max:255',
            'direccion'            => 'required|string|max:255',
            'rubro_id'             => 'required|exists:rubros,id',
            'tipo_organizacion_id' => 'required|exists:tipo_organizacions,id',
            'pais_exportacion_id'  => 'nullable|exists:paises,id',
            'facturacion'          => 'nullable|boolean', 
        ]);
        
        $data = $request->all();
        $data['facturacion'] = $request->has('facturacion');

        $empresa->update($data); 

        return redirect()->route('empresas.index')
                         ->with('success', 'Empresa actualizada exitosamente.');
    }

    public function destroy(Request $request, Empresa $empresa)
    {
        try {
            $empresa->delete();
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Empresa eliminada exitosamente.']);
            }
            
            return redirect()->route('empresas.index')->with('success', 'Empresa eliminada exitosamente.');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al eliminar la empresa: ' . $e->getMessage()], 500);
            }

            return redirect()->route('empresas.index')->with('error', 'Error al eliminar la empresa: ' . $e->getMessage());
        }
    }
}
