<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venta; // Necesario para la restricción
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClienteController extends Controller
{
    /**
     * Muestra una lista de todos los clientes (con paginación y búsqueda AJAX).
     */
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
            
            // Pasamos el objeto paginador completo para que la vista parcial use firstItem()
            return response()->json([
                'table_rows' => view('clientes.partials.clientes_table_rows', compact('clientes'))->render(),
                'pagination_links' => $clientes->appends($request->except('page'))->links()->toHtml(),
                'clientes_count' => $clientes->total(),
            ]);
        }

        // Si es una petición normal, devolvemos la vista completa
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Muestra el formulario para crear un nuevo cliente. (CREATE: Form)
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Almacena un nuevo cliente en la base de datos. (CREATE: Store)
     */
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

    /**
     * Muestra los detalles de un cliente específico. (READ: Show)
     */
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Muestra el formulario para editar un cliente. (UPDATE: Form)
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Actualiza un cliente existente en la base de datos. (UPDATE: Update)
     */
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

    /**
     * Elimina un cliente de la base de datos. (DELETE: Destroy)
     * Asegura respuesta JSON para manejo AJAX.
     */
    public function destroy(Cliente $cliente, Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('clientes.index')->with('error', 'Acceso no permitido.');
        }

        try {
            // 1. Verificar si tiene transacciones (Restricción)
            if (Venta::where('cliente_id', $cliente->id)->exists()) {
                 return response()->json([
                    'success' => false, 
                    'message' => 'No se puede eliminar el cliente porque tiene transacciones registradas.'
                 ], 422);
            }

            // 2. Eliminación exitosa
            $cliente->delete();

            // DEVOLUCIÓN JSON DE ÉXITO 
            return response()->json([
                'success' => true,
                'message' => 'Cliente ' . $cliente->nombre . ' eliminado exitosamente.'
            ]);

        } catch (\Exception $e) {
            // 3. Error inesperado
            return response()->json([
                'success' => false, 
                'message' => 'Error al eliminar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }
}