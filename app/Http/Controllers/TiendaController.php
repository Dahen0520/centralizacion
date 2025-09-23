<?php
namespace App\Http\Controllers;

use App\Models\Tienda;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TiendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Tienda::query();

        // Search logic
        if ($request->has('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $tiendas = $query->with('municipio')->paginate(10);

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'table_rows' => view('tiendas.partials.tiendas_table_rows', compact('tiendas'))->render(),
                'pagination_links' => $tiendas->links()->toHtml(),
            ]);
        }

        // Return full view for regular requests
        return view('tiendas.index', compact('tiendas'));
    }

    public function create()
    {
        $municipios = Municipio::all();
        return view('tiendas.create', compact('municipios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:tiendas,nombre',
            'municipio_id' => 'required|exists:municipios,id',
        ]);

        Tienda::create($request->all());
        return redirect()->route('tiendas.index')->with('success', 'Tienda creada exitosamente.');
    }

    public function show(Tienda $tienda)
    {
        return view('tiendas.show', compact('tienda'));
    }

    public function edit(Tienda $tienda)
    {
        $municipios = Municipio::all();
        return view('tiendas.edit', compact('tienda', 'municipios'));
    }

    public function update(Request $request, Tienda $tienda)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tiendas')->ignore($tienda->id),
            ],
            'municipio_id' => 'required|exists:municipios,id',
        ]);

        $tienda->update($request->all());
        return redirect()->route('tiendas.index')->with('success', 'Tienda actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tienda  $tienda
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Tienda $tienda)
    {
        try {
            $tienda->delete();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Tienda eliminada exitosamente.']);
            }

            return redirect()->route('tiendas.index')->with('success', 'Tienda eliminada exitosamente.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al eliminar la tienda: ' . $e->getMessage()], 500);
            }

            return redirect()->route('tiendas.index')->with('error', 'Error al eliminar la tienda: ' . $e->getMessage());
        }
    }
}