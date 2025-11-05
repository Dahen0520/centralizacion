<?php

namespace App\Http\Controllers;

use App\Models\RangoCai;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class RangoCaiController extends Controller
{
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
        // Regla de validación para el formato SAR (ej: 000-001-01-0000001)
        $rangoRegex = 'regex:/^\d{3}-\d{3}-\d{2}-\d{7,10}$/'; 

        $validated = $request->validate([
            'tienda_id' => 'required|exists:tiendas,id',
            'cai' => 'required|string|max:100|unique:rango_cais,cai',
            'rango_inicial' => ['required', 'string', 'max:50', $rangoRegex],
            'rango_final' => ['required', 'string', 'max:50', $rangoRegex],
            'fecha_limite_emision' => 'required|date|after_or_equal:today',
        ]);
        
        // Validación Lógica: Rango Inicial no debe ser mayor al Rango Final
        $inicialNumero = (int) str_replace('-', '', substr($validated['rango_inicial'], -10));
        $finalNumero = (int) str_replace('-', '', substr($validated['rango_final'], -10));
        
        if ($inicialNumero >= $finalNumero) {
            throw ValidationException::withMessages([
                'rango_final' => 'El número final del rango debe ser estrictamente mayor que el número inicial.'
            ]);
        }
        
        // Desactivar cualquier rango ANTERIOR que esté activo para la misma tienda
        RangoCai::where('tienda_id', $validated['tienda_id'])
                ->where('esta_activo', true)
                ->update(['esta_activo' => false]);


        RangoCai::create([
            'tienda_id' => $validated['tienda_id'],
            'cai' => $validated['cai'],
            'rango_inicial' => $validated['rango_inicial'],
            'rango_final' => $validated['rango_final'],
            // El numero_actual se establece al rango inicial, listo para ser usado +1
            'numero_actual' => $validated['rango_inicial'], 
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
            return redirect()->route('rangos-cai.index')
                             ->with('error', 'No se puede eliminar el rango: probablemente ya hay facturas asociadas.');
        }
    }
}