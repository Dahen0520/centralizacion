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

    // =================================================================================
    // MÉTODOS DE BÚSQUEDA DE RESULTADOS POR DNI
    // =================================================================================

    /**
     * Muestra el formulario de búsqueda por DNI (Identidad).
     * También muestra los resultados si se envía un DNI.
     */
        public function buscar(Request $request)
        {
            $resultados = collect(); // Colección vacía por defecto
            $dniBuscado = $request->input('dni'); // Captura el DNI del formulario

            if ($dniBuscado) {
                // Buscamos resultados por el campo afiliado_dni
                // Precargamos la relación con la empresa para mostrar el nombre.
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

        /**
         * Método auxiliar (alias) para la ruta que muestra el formulario de búsqueda.
         */
        public function mostrarResultados(Request $request)
        {
            return $this->buscar($request);
        }
        
        /**
         * Muestra la vista pública de detalle del resultado de una empresa específica.
         * La empresa ($empresa) es cargada automáticamente por su ID.
         */
        public function verResultadoDetalle(Empresa $empresa)
        {
            // Precargamos solo las relaciones necesarias para la vista pública:
            // 1. El resultado (dictamen) actual de la empresa.
            // 2. Las relaciones de la empresa para mostrar detalles generales (rubro, productos).
            $empresa->load([
                'resultado', // Carga el dictamen actual (usado en la vista)
                'rubro',
                'tipoOrganizacion',
                'paisExportacion',
                'productos.subcategoria.categoria', // Productos y su jerarquía
            ]);

            // Verificamos que el resultado exista antes de mostrar el detalle
            if (!$empresa->resultado) {
                // Manejar caso donde no hay un resultado registrado (por seguridad o error)
                return redirect()->route('resultados.buscar')->withErrors(['No se encontró un resultado asociado a esta empresa.']);
            }

            // Muestra la vista de detalle público
            return view('resultados.detalle', compact('empresa'));
        }

        public function dashboard()
    {
        // Estadísticas de empresas
        $totalEmpresas = Empresa::count();
        $empresasPendientes = Empresa::where('estado', 'pendiente')->count();
        $empresasAprobadas = Empresa::where('estado', 'aprobado')->count();
        $empresasRechazadas = Empresa::where('estado', 'rechazado')->count();

        // Calcular porcentajes para las barras de progreso
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

        // Estadísticas adicionales
        $totalProductos = Marca::count(); // Total de productos/marcas registradas
        $totalAfiliados = DB::table('afiliados')->count(); // Total de afiliados en el sistema

        // Calcular crecimiento del mes actual vs mes anterior
        $empresasMesActual = Empresa::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count();
        
        $empresasMesAnterior = Empresa::whereMonth('created_at', now()->subMonth()->month)
                                    ->whereYear('created_at', now()->subMonth()->year)
                                    ->count();
        
        $crecimientoEmpresas = $empresasMesAnterior > 0 
            ? round((($empresasMesActual - $empresasMesAnterior) / $empresasMesAnterior) * 100) 
            : 0;

        // Calcular tendencias para los badges
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
