<?php

namespace App\Http\Controllers;

use App\Models\RangoCai;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; 

class RangoCaiController extends Controller
{

    private function parseRangoSar(string $formattedNumber): array
    {
        $length = strlen($formattedNumber);
        $prefijo = substr($formattedNumber, 0, $length - 8); 
        $secuencia = substr($formattedNumber, -8); 
        
        return [
            'prefijo' => $prefijo,
            'secuencia' => (int) ltrim($secuencia, '0'),
        ];
    }
    
    public function index(Request $request)
    {
        $query = RangoCai::with('tienda')->orderBy('fecha_limite_emision', 'desc');
        $filterStatus = $request->input('status');

        $today = Carbon::today();

        if ($filterStatus) {
            
            if ($filterStatus === 'activo') {
                $query->where('esta_activo', true)
                      ->whereDate('fecha_limite_emision', '>=', $today);

            } elseif ($filterStatus === 'expirado') {
                $query->whereDate('fecha_limite_emision', '<', $today);
            }
        }

        $rangos = $query->paginate(10);
        
        if ($request->ajax()) {
            return view('rangos-cai.partials.rangos_table_rows', compact('rangos'))->render();
        }

        return view('rangos-cai.index', compact('rangos'));
    }

    public function create()
    {
        $tiendas = Tienda::all();
        return view('rangos-cai.create', compact('tiendas'));
    }

    public function store(Request $request)
    {
        $rangoRegex = 'regex:/^\d{3}-\d{3}-\d{2}-\d{7,10}$/'; 

        $validated = $request->validate([
            'tienda_id' => 'required|exists:tiendas,id',
            'cai' => 'required|string|max:100', 
            'rango_inicial_full' => ['required', 'string', 'max:50', $rangoRegex],
            'rango_final_full' => ['required', 'string', 'max:50', $rangoRegex],
            'fecha_limite_emision' => 'required|date|after_or_equal:today',
        ]);
        
        $inicialParsed = $this->parseRangoSar($validated['rango_inicial_full']);
        $finalParsed = $this->parseRangoSar($validated['rango_final_full']);

        $inicialNumero = $inicialParsed['secuencia'];
        $finalNumero = $finalParsed['secuencia'];
        $prefijoSar = $inicialParsed['prefijo'];
        
        if ($inicialNumero >= $finalNumero) {
            throw ValidationException::withMessages([
                'rango_final_full' => 'El número final del rango debe ser estrictamente mayor que el número inicial.'
            ]);
        }
        
        if ($inicialParsed['prefijo'] !== $finalParsed['prefijo']) {
             throw ValidationException::withMessages([
                'rango_final_full' => 'El prefijo de la serie (Ej: 000-001-01-) debe coincidir para los rangos inicial y final.'
            ]);
        }
        
        RangoCai::where('tienda_id', $validated['tienda_id'])
                ->where('esta_activo', true)
                ->update(['esta_activo' => false]);


        RangoCai::create([
            'tienda_id' => $validated['tienda_id'],
            'cai' => $validated['cai'],
            'prefijo_sar' => $prefijoSar,
            'rango_inicial' => $inicialNumero, 
            'rango_final' => $finalNumero,
            'numero_actual' => $inicialNumero - 1, 
            'fecha_limite_emision' => $validated['fecha_limite_emision'],
            'esta_activo' => true,
        ]);

        return redirect()->route('rangos-cai.index')
                         ->with('success', 'Rango CAI registrado y activado correctamente.');
    }

    public function show(RangoCai $rangoCai)
    {
        $rangoCai->load('tienda');
        return view('rangos-cai.show', compact('rangoCai'));
    }

    public function edit(RangoCai $rangoCai)
    {
        $tiendas = Tienda::all();
        return view('rangos-cai.edit', compact('rangoCai', 'tiendas'));
    }

    public function update(Request $request, RangoCai $rangoCai)
    {
        $validated = $request->validate([
            'esta_activo' => 'required|boolean',
            'fecha_limite_emision' => 'required|date|after_or_equal:today',
        ]);

        if ($validated['esta_activo']) {
            RangoCai::where('tienda_id', $rangoCai->tienda_id)
                    ->where('id', '!=', $rangoCai->id)
                    ->update(['esta_activo' => false]);
        }
        
        $rangoCai->update($validated);

        return redirect()->route('rangos-cai.index')
                         ->with('success', 'Rango CAI actualizado correctamente.');
    }

    public function destroy(RangoCai $rangoCai)
    {
        $rangoId = $rangoCai->id; 

        try {

            DB::beginTransaction();
            $rangoCai->delete();
            DB::commit(); 

            return response()->json(['success' => true, 'message' => 'Rango CAI eliminado exitosamente.'], 200); 
        
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                 Log::warning("Intento fallido de eliminar rango CAI #{$rangoId}: Clave foránea. Mensaje: " . $e->getMessage());
                 
                 return response()->json([
                    'success' => false, 
                    'message' => 'El rango no se puede eliminar porque ya tiene documentos fiscales asociados.'
                ], 409); 
            }
            
            Log::error("Error Query al eliminar rango CAI #{$rangoId}: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Ocurrió un error en la base de datos.'
            ], 500); 
            
        } catch (\Exception $e) {
            DB::rollBack(); 
            Log::error("Error general al eliminar rango CAI #{$rangoId}: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Ocurrió un error inesperado al intentar eliminar el rango.'
            ], 500); 
        }
    }
}