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
    /**
     * Muestra la vista con el formulario para crear una nueva empresa.
     * Recibe el ID del afiliado de la URL para asociar la empresa.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $afiliado = Afiliado::findOrFail($request->query('afiliado_id'));
        
        $rubros = Rubro::all();
        $tiposOrganizacion = TipoOrganizacion::all();
        $paises = Pais::all();
        $tiendas = Tienda::all();
        
        return view('empresas.create', [
            'afiliado'          => $afiliado,
            'rubros'            => $rubros,
            'tiposOrganizacion' => $tiposOrganizacion,
            'paises'            => $paises,
            'tiendas'           => $tiendas,
        ]);
    }
    
    /**
     * Almacena una nueva empresa en la base de datos.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validación de los campos del formulario
        $request->validate([
            'afiliado_id'          => 'required|exists:afiliados,id',
            'nombre_negocio'       => 'required|string|max:255',
            'direccion'            => 'required|string|max:255',
            'rubro_id'             => 'required|exists:rubros,id',
            'tipo_organizacion_id' => 'required|exists:tipo_organizacions,id',
            'pais_exportacion_id'  => 'nullable|exists:paises,id',
            'tiendas'              => 'required|array',
            'tiendas.*'            => 'exists:tiendas,id',
        ]);
    
        // Crear la empresa
        $empresa = Empresa::create($request->all());

        // Recorre cada tienda seleccionada para crear la asociación
        foreach ($request->input('tiendas') as $tiendaId) {
            // Generar un código de asociación único
            do {
                $codigoAsociacion = Str::random(10);
            } while (EmpresaTienda::where('codigo_asociacion', $codigoAsociacion)->exists());
        
            // Usar 'attach' para guardar la asociación con los datos adicionales
            $empresa->tiendas()->attach($tiendaId, [
                'estado' => 'pendiente',
                'codigo_asociacion' => $codigoAsociacion,
            ]);
        }
    
        // **CORRECCIÓN:** La redirección ahora usa el nombre de ruta correcto.
        return redirect()->route('afiliados.productos.create', ['empresa_id' => $empresa->id])
                         ->with('success', 'Empresa registrada exitosamente. Ahora, describe tus productos.');
    }

    /**
     * Muestra la lista de todas las empresas.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Empresa::query();

        if ($request->has('search')) {
            $query->where('nombre_negocio', 'like', '%' . $request->search . '%');
        }

        $empresas = $query->with(['afiliado', 'rubro', 'tipoOrganizacion'])->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'table_rows' => view('empresas.partials.empresas_table_rows', compact('empresas'))->render(),
                'pagination_links' => $empresas->links()->toHtml(),
            ]);
        }

        return view('empresas.index', compact('empresas'));
    }

    /**
     * Muestra los detalles de una empresa específica.
     * @param  \App\Models\Empresa  $empresa
     * @return \Illuminate\View\View
     */
    public function show(Empresa $empresa)
    {
        return view('empresas.show', compact('empresa'));
    }

    /**
     * Muestra el formulario para editar una empresa existente.
     * @param  \App\Models\Empresa  $empresa
     * @return \Illuminate\View\View
     */
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
    
    /**
     * Actualiza una empresa existente en la base de datos.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Empresa  $empresa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Empresa $empresa)
    {
        $request->validate([
            'nombre_negocio'       => 'required|string|max:255',
            'direccion'            => 'required|string|max:255',
            'rubro_id'             => 'required|exists:rubros,id',
            'tipo_organizacion_id' => 'required|exists:tipo_organizacions,id',
            'pais_exportacion_id'  => 'nullable|exists:paises,id',
        ]);
        
        $empresa->update($request->all());

        return redirect()->route('empresas.index')
                         ->with('success', 'Empresa actualizada exitosamente.');
    }

    /**
     * Elimina una empresa de la base de datos.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Empresa  $empresa
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
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