<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Tienda;
use App\Models\EmpresaTienda;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; // Importar el helper Str

class EmpresaTiendaController extends Controller
{
    /**
     * Muestra la lista de asociaciones con búsqueda y paginación.
     */
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
                ->orWhere('codigo_asociacion', 'like', '%' . $query . '%'); // Agrega la búsqueda por el código de asociación
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
    /**
     * Muestra el formulario para crear una nueva asociación.
     */
    public function create()
    {
        // Se cargan solo el ID y el nombre de las empresas y tiendas para el select.
        $empresas = Empresa::select('id', 'nombre_negocio')->get();
        $tiendas = Tienda::select('id', 'nombre')->get();

        return view('empresa-tienda.create', compact('empresas', 'tiendas'));
    }

    /**
     * Almacena una nueva asociación entre una empresa y una tienda.
     */
    public function store(Request $request)
    {
        // 1. Validar los datos para la nueva asociación, requiriendo el ID de la empresa
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'tienda_id' => 'required|exists:tiendas,id',
        ]);
    
        // 2. Verificar si la asociación ya existe para evitar duplicados.
        $exists = EmpresaTienda::where('empresa_id', $request->empresa_id)
            ->where('tienda_id', $request->tienda_id)
            ->exists();
    
        if ($exists) {
            return back()->withInput()->withErrors([
                'asociacion_existente' => 'La asociación entre esta empresa y esta tienda ya existe.'
            ]);
        }
    
        // 3. Obtener la empresa del ID proporcionado
        $empresa = Empresa::find($request->empresa_id);

        // 4. Nueva lógica para generar un código de asociación único.
        do {
            $codigoAsociacion = Str::random(10);
        } while (EmpresaTienda::where('codigo_asociacion', $codigoAsociacion)->exists());
    
        // 5. Usar 'attach' para guardar la asociación con 'estado' y 'codigo_asociacion'.
        $empresa->tiendas()->attach($request->tienda_id, [
            'estado' => $request->input('estado', 'pendiente'),
            'codigo_asociacion' => $codigoAsociacion, // Pasa el código generado aquí.
        ]);
    
        return redirect()->route('asociaciones.index')->with('success', 'Asociación creada exitosamente.');
    }


    /**
     * Muestra la información detallada de una asociación específica.
     */
    public function show(Empresa $empresa, Tienda $tienda)
    {
        $asociacion = EmpresaTienda::where('empresa_id', $empresa->id)
                                   ->where('tienda_id', $tienda->id)
                                   ->firstOrFail();

        return view('empresa-tienda.show', compact('empresa', 'tienda', 'asociacion'));
    }

    /**
     * Muestra el formulario para editar una asociación existente.
     */
    public function edit(Empresa $empresa, Tienda $tienda)
    {
        $asociacion = EmpresaTienda::where('empresa_id', $empresa->id)
                                   ->where('tienda_id', $tienda->id)
                                   ->firstOrFail();
    
        // **NUEVO**: Cargar todas las empresas y tiendas para los menús desplegables
        $empresas = Empresa::select('id', 'nombre_negocio')->get();
        $tiendas = Tienda::select('id', 'nombre')->get();
        
        return view('empresa-tienda.edit', compact('empresa', 'tienda', 'asociacion', 'empresas', 'tiendas'));
    }
    /**
     * Actualiza el estado de una asociación existente.
     */
    public function update(Request $request, Empresa $empresa, Tienda $tienda)
    {
        // 1. Validar los datos del formulario
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'tienda_id' => 'required|exists:tiendas,id',
            'estado' => 'required|in:pendiente,aprobado,rechazado',
        ]);
        
        // 2. Verificar si la nueva asociación ya existe para evitar duplicados
        $asociacionAntigua = EmpresaTienda::where('empresa_id', $empresa->id)
                                ->where('tienda_id', $tienda->id)
                                ->firstOrFail();

        $nuevaAsociacionExistente = EmpresaTienda::where('empresa_id', $request->empresa_id)
                                                 ->where('tienda_id', $request->tienda_id)
                                                 ->first();

        // Evita crear una asociación duplicada, a menos que sea la misma que se está editando
        if ($nuevaAsociacionExistente && ($nuevaAsociacionExistente->empresa_id != $empresa->id || $nuevaAsociacionExistente->tienda_id != $tienda->id)) {
            throw ValidationException::withMessages([
                'asociacion_existente' => 'La asociación que intentas crear ya existe.'
            ]);
        }

        // Si la empresa o la tienda cambian, se elimina y crea una nueva asociación.
        // Si solo cambia el estado, se actualiza el registro existente.
        if ($asociacionAntigua->empresa_id != $request->empresa_id || $asociacionAntigua->tienda_id != $request->tienda_id) {
             // Eliminar la asociación antigua
            $empresa->tiendas()->detach($tienda->id);
            // Crear la nueva asociación con los datos actualizados y el mismo código
            $nuevaEmpresa = Empresa::findOrFail($request->empresa_id);
            $nuevaEmpresa->tiendas()->attach($request->tienda_id, [
                'estado' => $request->estado,
                'codigo_asociacion' => $asociacionAntigua->codigo_asociacion, // Mantiene el código original
            ]);
        } else {
             // Solo actualiza el estado si la empresa y la tienda no han cambiado
             $empresa->tiendas()->updateExistingPivot($tienda->id, [
                 'estado' => $request->estado,
             ]);
        }
        
        return redirect()->route('asociaciones.index')->with('success', 'Asociación actualizada exitosamente.');
    }


    /**
     * Elimina una asociación entre empresa y tienda.
     */
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
