<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Marca; // Para aprobar/rechazar productos
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

        // 2. Cargar y filtrar las empresas
        $empresas = Empresa::with(['afiliado', 'rubro'])
                            ->where('estado', $statusFilter)
                            ->orderBy('created_at', 'desc') // Ordenar por las más recientes primero
                            ->paginate(15); 

        return view('solicitud.index', compact('empresas'));
    }

    /**
     * Muestra la vista de detalle para una empresa específica, expandiendo sus relaciones.
     * La Empresa ($empresa) es cargada automáticamente por su ID (Route Model Binding).
     */
    public function show(Empresa $empresa)
    {
        // Precargamos TODAS las relaciones necesarias para la vista de detalle
        $empresa->load([
            'afiliado',
            'rubro',
            'tipoOrganizacion',
            'paisExportacion',
            // Relación con Productos a través de la tabla pivot 'marcas'
            'productos', 
            // Relación con Tiendas a través de la tabla pivot 'empresa_tienda'
            'tiendas' 
        ]);

        return view('solicitud.show', compact('empresa'));
    }

    /**
     * Aprueba la empresa y todos los registros relacionados en las tablas pivot.
     */
    public function aprobar(Empresa $empresa)
    {
        DB::transaction(function () use ($empresa) {
            // 1. Aprobar el registro de la Empresa
            $empresa->update(['estado' => 'aprobado']);

            // 2. Aprobar todos los Productos asociados (actualiza el estado en la tabla 'marcas')
            Marca::where('empresa_id', $empresa->id)
                ->update(['estado' => 'aprobado']);

            // 3. Aprobar todas las Tiendas asociadas (actualiza el estado en la tabla 'empresa_tienda')
            DB::table('empresa_tienda')
                ->where('empresa_id', $empresa->id)
                ->update(['estado' => 'aprobado']);
        });

        return redirect()->route('solicitud.index')->with('success', 'La solicitud de ' . $empresa->nombre_negocio . ' y sus registros han sido APROBADOS.');
    }

    /**
     * Rechaza la solicitud de la empresa.
     */
    public function rechazar(Empresa $empresa)
    {
        $empresa->update(['estado' => 'rechazado']);

        // Opcional: También rechazamos las marcas y tiendas (si quieres ser estricto)
        Marca::where('empresa_id', $empresa->id)->update(['estado' => 'rechazado']);
        DB::table('empresa_tienda')->where('empresa_id', $empresa->id)->update(['estado' => 'rechazado']);

        return redirect()->route('solicitud.index')->with('warning', 'La solicitud de ' . $empresa->nombre_negocio . ' ha sido RECHAZADA.');
    }
}