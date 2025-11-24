<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venta; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::orderBy('nombre');

        if ($search = $request->get('search')) {
            $cleanSearch = str_replace('-', '', $search);

            $query->where(function ($q) use ($search, $cleanSearch) {
                $q->where('nombre', 'like', "%$search%")
                  ->orWhere('id', $search)
                  ->orWhere('identificacion', 'like', "%$cleanSearch%");
            });
        }

        $clientes = $query->paginate(15);

        // Si es una petición AJAX, devuelve el HTML parcial y los enlaces
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'table_rows' => view('clientes.partials.clientes_table_rows', compact('clientes'))->render(),
                'pagination_links' => $clientes->appends($request->except('page'))->links()->toHtml(),
                'clientes_count' => $clientes->total(),
            ]);
        }

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'identificacion' => 'nullable|string|max:50|unique:clientes,identificacion',
            'email' => 'nullable|email|max:255|unique:clientes,email',
            'telefono' => 'nullable|string|max:20',
        ]);

        $validated['identificacion'] = str_replace('-', '', $validated['identificacion']);

        Cliente::create($validated);

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente ' . $validated['nombre'] . ' creado exitosamente.');
    }

    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'identificacion' => 'nullable|string|max:50|unique:clientes,identificacion,' . $cliente->id,
            'email' => 'nullable|email|max:255|unique:clientes,email,' . $cliente->id,
            'telefono' => 'nullable|string|max:20',
        ]);

        $validated['identificacion'] = str_replace('-', '', $validated['identificacion']);

        $cliente->update($validated);

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente ' . $cliente->nombre . ' actualizado exitosamente.');
    }

    public function destroy(Cliente $cliente, Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('clientes.index')->with('error', 'Acceso no permitido.');
        }

        try {
            if (Venta::where('cliente_id', $cliente->id)->exists()) {
                 return response()->json([
                    'success' => false, 
                    'message' => 'No se puede eliminar el cliente porque tiene transacciones registradas.'
                 ], 422);
            }

            $cliente->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cliente ' . $cliente->nombre . ' eliminado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al eliminar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }
}