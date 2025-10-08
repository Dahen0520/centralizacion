<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Marca;
use App\Models\Resultado; 
use App\Models\Inventario; // <-- ¡NUEVA IMPORTACIÓN NECESARIA!
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
        // Precargamos TODAS las relaciones necesarias.
        $empresa->load([
            'afiliado',
            'rubro',
            'tipoOrganizacion',
            'paisExportacion',
            'productos', // Carga productos asociados (que son Marcas)
            'tiendas'    // Carga tiendas asociadas
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
        
        // Carga las marcas (One-to-Many) y las tiendas (Many-to-Many)
        $empresa->load(['afiliado', 'marcas', 'tiendas']); // <-- ¡Aquí se usa la nueva relación 'marcas'!

        $afiliadoId = $empresa->afiliado_id;
        $afiliadoDni = $empresa->afiliado->dni ?? 'DNI_No_Encontrado'; 

        DB::transaction(function () use ($empresa, $request, $afiliadoId, $afiliadoDni) {
            
            // 1. Aprobar registros relacionados
            $empresa->update(['estado' => 'aprobado']);
            Marca::where('empresa_id', $empresa->id)->update(['estado' => 'aprobado']);
            DB::table('empresa_tienda')->where('empresa_id', $empresa->id)->update(['estado' => 'aprobado']);

            // 2. CREACIÓN DE REGISTROS DE INVENTARIO
            $marcas = $empresa->marcas; // Usa la nueva relación directa HasMany
            $tiendas = $empresa->tiendas; 
            
            foreach ($marcas as $marca) {
                foreach ($tiendas as $tienda) {
                    
                    // Crea o encuentra el registro de inventario con precio y stock en 0
                    \App\Models\Inventario::firstOrCreate( // Uso el FQN por seguridad
                        [
                            'marca_id' => $marca->id,
                            'tienda_id' => $tienda->id,
                        ],
                        [
                            'precio' => 0.00,
                            'stock' => 0,
                        ]
                    );
                }
            }

            // 3. REGISTRAR O ACTUALIZAR EL RESULTADO
            \App\Models\Resultado::updateOrCreate(
                ['empresa_id' => $empresa->id], 
                [
                    'afiliado_id' => $afiliadoId,
                    'afiliado_dni' => $afiliadoDni, 
                    'estado' => 'aprobado',
                    'comentario' => $request->comentario,
                ]
            );
        });

        return redirect()->route('solicitud.index')->with('success', 'La solicitud de ' . $empresa->nombre_negocio . ' ha sido APROBADA, su inventario inicial generado, y su resultado actualizado. 🎉');
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

            // 2. REGISTRAR O ACTUALIZAR EL RESULTADO
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

        return redirect()->route('solicitud.index')->with('warning', 'La solicitud de ' . $empresa->nombre_negocio . ' ha sido RECHAZADA y su resultado actualizado. ❌');
    }

    // =================================================================================
    // MÉTODOS DE BÚSQUEDA DE RESULTADOS POR DNI Y DASHBOARD
    // =================================================================================

    public function buscar(Request $request)
    {
        $resultados = collect();
        $dniBuscado = $request->input('dni');

        if ($dniBuscado) {
            $resultados = Resultado::with('empresa')
                                    ->where('afiliado_dni', $dniBuscado)
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        }

        return view('resultados.busqueda', [
            'resultados' => $resultados,
            'dniBuscado' => $dniBuscado
        ]);
    }
    
    public function verResultadoDetalle(Empresa $empresa)
    {
        $empresa->load([
            'resultado',
            'rubro',
            'tipoOrganizacion',
            'paisExportacion',
            'productos.subcategoria.categoria',
        ]);

        if (!$empresa->resultado) {
            return redirect()->route('resultados.buscar')->withErrors(['No se encontró un resultado asociado a esta empresa.']);
        }

        return view('resultados.detalle', compact('empresa'));
    }

    public function dashboard()
    {
        // ... (Tu lógica del dashboard se mantiene igual) ...

        $totalEmpresas = Empresa::count();
        $empresasPendientes = Empresa::where('estado', 'pendiente')->count();
        $empresasAprobadas = Empresa::where('estado', 'aprobado')->count();
        $empresasRechazadas = Empresa::where('estado', 'rechazado')->count();

        $totalSolicitudes = $empresasPendientes + $empresasAprobadas + $empresasRechazadas;
        
        if ($totalSolicitudes > 0) {
            $porcentajeAprobadas = round(($empresasAprobadas / $totalSolicitudes) * 100);
            $porcentajePendientes = round(($empresasPendientes / $totalSolicitudes) * 100);
            $porcentajeRechazadas = round(($empresasRechazadas / $totalSolicitudes) * 100);
            $tasaAprobacion = round(($empresasAprobadas / $totalSolicitudes) * 100);
        } else {
            $porcentajeAprobadas = 0;
            $porcentajePendientes = 0;
            $porcentajeRechazadas = 0;
            $tasaAprobacion = 0;
        }

        $totalProductos = Marca::count();
        $totalAfiliados = DB::table('afiliados')->count();

        $empresasMesActual = Empresa::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count();
        
        $empresasMesAnterior = Empresa::whereMonth('created_at', now()->subMonth()->month)
                                    ->whereYear('created_at', now()->subMonth()->year)
                                    ->count();
        
        $crecimientoEmpresas = $empresasMesAnterior > 0 
            ? round((($empresasMesActual - $empresasMesAnterior) / $empresasMesAnterior) * 100) 
            : 0;

        $aprobadasMesActual = Empresa::where('estado', 'aprobado')
                                    ->whereMonth('updated_at', now()->month)
                                    ->whereYear('updated_at', now()->year)
                                    ->count();
        
        $aprobadasMesAnterior = Empresa::where('estado', 'aprobado')
                                    ->whereMonth('updated_at', now()->subMonth()->month)
                                    ->whereYear('updated_at', now()->subMonth()->year)
                                    ->count();
        
        $tendenciaAprobadas = $aprobadasMesAnterior > 0 
            ? round((($aprobadasMesActual - $aprobadasMesAnterior) / $aprobadasMesAnterior) * 100) 
            : 0;

        $rechazadasMesActual = Empresa::where('estado', 'rechazado')
                                    ->whereMonth('updated_at', now()->month)
                                    ->whereYear('updated_at', now()->year)
                                    ->count();
        
        $rechazadasMesAnterior = Empresa::where('estado', 'rechazado')
                                        ->whereMonth('updated_at', now()->subMonth()->month)
                                        ->whereYear('updated_at', now()->subMonth()->year)
                                        ->count();
        
        $tendenciaRechazadas = $rechazadasMesAnterior > 0 
            ? round((($rechazadasMesActual - $rechazadasMesAnterior) / $rechazadasMesAnterior) * 100) 
            : 0;

        return view('dashboard', compact(
            'totalEmpresas',
            'empresasPendientes',
            'empresasAprobadas',
            'empresasRechazadas',
            'porcentajeAprobadas',
            'porcentajePendientes',
            'porcentajeRechazadas',
            'tasaAprobacion',
            'totalProductos',
            'totalAfiliados',
            'crecimientoEmpresas',
            'tendenciaAprobadas',
            'tendenciaRechazadas'
        ));
    }
}