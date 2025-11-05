<?php

namespace App\Http\Controllers;

use App\Models\RangoCai;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Agregado para registro de errores

class RangoCaiController extends Controller
{
    /**
     * Función auxiliar para extraer el Prefijo (Ej: '000-001-01-') y la Secuencia (Ej: 1) 
     * de una cadena de formato SAR completa (Ej: '000-001-01-00000001').
     */
    private function parseRangoSar(string $formattedNumber): array
    {
        // Se asume el formato 'XXX-XXX-XX-XXXXXXXX'
        $length = strlen($formattedNumber);
        
        // El prefijo es todo hasta los últimos 8 dígitos (la serie secuencial)
        $prefijo = substr($formattedNumber, 0, $length - 8); 
        
        // La secuencia son los últimos 8 dígitos
        $secuencia = substr($formattedNumber, -8); 
        
        // Devolvemos el prefijo de texto y la secuencia como un número entero limpio
        return [
            'prefijo' => $prefijo,
            'secuencia' => (int) ltrim($secuencia, '0'), // Quitar ceros a la izquierda
        ];
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rangos = RangoCai::with('tienda')->orderBy('tienda_id')->paginate(10);
        return view('rangos-cai.index', compact('rangos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiendas = Tienda::all();
        return view('rangos-cai.create', compact('tiendas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Regla de validación para el formato SAR (Ej: 000-001-01-00000010)
        // Se permite cualquier número de dígitos para la secuencia, de 7 a 10 para mayor flexibilidad.
        $rangoRegex = 'regex:/^\d{3}-\d{3}-\d{2}-\d{7,10}$/'; 

        $validated = $request->validate([
            'tienda_id' => 'required|exists:tiendas,id',
            'cai' => 'required|string|max:100|unique:rango_cais,cai',
            'rango_inicial_full' => ['required', 'string', 'max:50', $rangoRegex], // Campo del form
            'rango_final_full' => ['required', 'string', 'max:50', $rangoRegex],   // Campo del form
            'fecha_limite_emision' => 'required|date|after_or_equal:today',
        ]);
        
        // 1. Parsear los rangos completos
        $inicialParsed = $this->parseRangoSar($validated['rango_inicial_full']);
        $finalParsed = $this->parseRangoSar($validated['rango_final_full']);

        $inicialNumero = $inicialParsed['secuencia'];
        $finalNumero = $finalParsed['secuencia'];
        $prefijoSar = $inicialParsed['prefijo'];
        
        // Validación Lógica: Rango Inicial no debe ser mayor al Rango Final
        if ($inicialNumero >= $finalNumero) {
            throw ValidationException::withMessages([
                'rango_final_full' => 'El número final del rango debe ser estrictamente mayor que el número inicial.'
            ]);
        }
        
        // Validación Lógica: Los prefijos deben ser idénticos
        if ($inicialParsed['prefijo'] !== $finalParsed['prefijo']) {
             throw ValidationException::withMessages([
                'rango_final_full' => 'El prefijo de la serie (Ej: 000-001-01-) debe coincidir para los rangos inicial y final.'
            ]);
        }
        
        // Desactivar cualquier rango ANTERIOR que esté activo para la misma tienda
        RangoCai::where('tienda_id', $validated['tienda_id'])
                ->where('esta_activo', true)
                ->update(['esta_activo' => false]);


        RangoCai::create([
            'tienda_id' => $validated['tienda_id'],
            'cai' => $validated['cai'],
            'prefijo_sar' => $prefijoSar, // 🆕 Guardamos el prefijo de la serie
            
            // 🌟 Guardamos solo la secuencia numérica limpia
            'rango_inicial' => $inicialNumero, 
            'rango_final' => $finalNumero,
            
            // CRÍTICO: El numero_actual se establece al número anterior al inicial. 
            // Si el rango empieza en 1, numero_actual = 0. Si empieza en 100, numero_actual = 99.
            'numero_actual' => $inicialNumero - 1, 
            
            'fecha_limite_emision' => $validated['fecha_limite_emision'],
            'esta_activo' => true,
        ]);

        return redirect()->route('rangos-cai.index')
                         ->with('success', 'Rango CAI registrado y activado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RangoCai $rangoCai)
    {
        $rangoCai->load('tienda');
        return view('rangos-cai.show', compact('rangoCai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RangoCai $rangoCai)
    {
        $tiendas = Tienda::all();
        // Solo se permite editar el estado de activo/inactivo o la fecha
        return view('rangos-cai.edit', compact('rangoCai', 'tiendas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RangoCai $rangoCai)
    {
        $validated = $request->validate([
            'esta_activo' => 'required|boolean',
            'fecha_limite_emision' => 'required|date|after_or_equal:today',
        ]);

        // Si se está activando este rango, desactivar todos los demás de la misma tienda
        if ($validated['esta_activo']) {
            RangoCai::where('tienda_id', $rangoCai->tienda_id)
                    ->where('id', '!=', $rangoCai->id)
                    ->update(['esta_activo' => false]);
        }
        
        $rangoCai->update($validated);

        return redirect()->route('rangos-cai.index')
                         ->with('success', 'Rango CAI actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RangoCai $rangoCai)
    {
        try {
            $rangoCai->delete();
            return redirect()->route('rangos-cai.index')
                             ->with('success', 'Rango CAI eliminado.');
        } catch (\Exception $e) {
            // Se registra el error para el desarrollador
            Log::error("Error al eliminar rango CAI #{$rangoCai->id}: " . $e->getMessage());
            
            return redirect()->route('rangos-cai.index')
                             ->with('error', 'No se puede eliminar el rango: probablemente ya hay facturas asociadas.');
        }
    }
}