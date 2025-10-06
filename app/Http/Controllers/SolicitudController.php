<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Marca;
use App\Models\Resultado; // Importamos el modelo Resultado
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    /**
     * Muestra la lista de todas las empresas en estado 'pendiente' (la cola de solicitudes).
     */
    public function index(Request $request)
    {
        // 1. Obtener el estado del filtro de la URL, por defecto 'pendiente'
        $statusFilter = $request->get('estado', 'pendiente');

        // 2. Cargar y filtrar las empresas, precargando el afiliado para mostrar detalles
        $empresas = Empresa::with(['afiliado', 'rubro'])
                            ->where('estado', $statusFilter)
                            ->orderBy('created_at', 'desc')
                            ->paginate(15); 

        return view('solicitud.index', compact('empresas'));
    }

    /**
     * Muestra la vista de detalle para una empresa específica, expandiendo sus relaciones.
     */
    public function show(Empresa $empresa)
    {
        // Precargamos TODAS las relaciones necesarias, incluyendo 'afiliado' para acceder a su DNI.
        $empresa->load([
            'afiliado',
            'rubro',
            'tipoOrganizacion',
            'paisExportacion',
            'productos', 
            'tiendas' 
        ]);

        return view('solicitud.show', compact('empresa'));
    }

    /**
     * Aprueba la empresa, registros relacionados y registra/actualiza el Resultado.
     */
    public function aprobar(Request $request, Empresa $empresa)
    {
        $request->validate([
            'comentario' => 'required|string|max:1000',
        ]);
        
        // Obtenemos los datos del afiliado dueño de la empresa para el registro del resultado.
        $afiliadoId = $empresa->afiliado_id;
        $afiliadoDni = $empresa->afiliado->dni ?? 'DNI_No_Encontrado'; 

        DB::transaction(function () use ($empresa, $request, $afiliadoId, $afiliadoDni) {
            
            // 1. Aprobar registros relacionados
            $empresa->update(['estado' => 'aprobado']);
            Marca::where('empresa_id', $empresa->id)->update(['estado' => 'aprobado']);
            DB::table('empresa_tienda')->where('empresa_id', $empresa->id)->update(['estado' => 'aprobado']);

            // 2. REGISTRAR O ACTUALIZAR EL RESULTADO: Si ya existe un registro para esta empresa, lo actualiza.
            Resultado::updateOrCreate(
                ['empresa_id' => $empresa->id], // Criterio de búsqueda (Empresa ID)
                [
                    'afiliado_id' => $afiliadoId,
                    'afiliado_dni' => $afiliadoDni, 
                    'estado' => 'aprobado',
                    'comentario' => $request->comentario,
                ]
            );
        });

        return redirect()->route('solicitud.index')->with('success', 'La solicitud de ' . $empresa->nombre_negocio . ' ha sido APROBADA y su resultado actualizado.');
    }

    /**
     * Rechaza la solicitud de la empresa, registros relacionados y registra/actualiza el Resultado.
     */
    public function rechazar(Request $request, Empresa $empresa)
    {
        $request->validate([
            'comentario' => 'required|string|max:1000',
        ]);
        
        // Obtenemos los datos del afiliado dueño de la empresa para el registro del resultado.
        $afiliadoId = $empresa->afiliado_id;
        $afiliadoDni = $empresa->afiliado->dni ?? 'DNI_No_Encontrado';

        DB::transaction(function () use ($empresa, $request, $afiliadoId, $afiliadoDni) {
            
            // 1. Rechazar registros relacionados
            $empresa->update(['estado' => 'rechazado']);
            Marca::where('empresa_id', $empresa->id)->update(['estado' => 'rechazado']);
            DB::table('empresa_tienda')->where('empresa_id', $empresa->id)->update(['estado' => 'rechazado']);

            // 2. REGISTRAR O ACTUALIZAR EL RESULTADO: Si ya existe un registro para esta empresa, lo actualiza.
            Resultado::updateOrCreate(
                ['empresa_id' => $empresa->id], // Criterio de búsqueda (Empresa ID)
                [
                    'afiliado_id' => $afiliadoId,
                    'afiliado_dni' => $afiliadoDni, 
                    'estado' => 'rechazado',
                    'comentario' => $request->comentario,
                ]
            );
        });

        return redirect()->route('solicitud.index')->with('warning', 'La solicitud de ' . $empresa->nombre_negocio . ' ha sido RECHAZADA y su resultado actualizado.');
    }
}
