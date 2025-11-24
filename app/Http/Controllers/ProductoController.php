<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Subcategoria;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Impuesto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductoController extends Controller
{

    public function index(Request $request)
    {
        $query = Producto::with('subcategoria.categoria', 'impuesto'); 

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where('nombre', 'like', '%' . $searchTerm . '%')
                  ->orWhere('descripcion', 'like', '%' . $searchTerm . '%');
        }

        if ($request->has('categoria') && $request->categoria != '') {
            $query->whereHas('subcategoria.categoria', function ($q) use ($request) {
                $q->where('id', $request->categoria);
            });
        }

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $productos = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table_rows' => view('productos.partials.productos_table_rows', compact('productos'))->render(),
                'pagination_links' => $productos->links()->toHtml(),
            ]);
        }
        
        $categorias = Categoria::all();
        $estados = ['pendiente', 'aprobado', 'rechazado'];

        return view('productos.index', compact('productos', 'categorias', 'estados'));
    }

    public function create()
    {
        $subcategorias = Subcategoria::all();
        $impuestos = Impuesto::all();
        return view('productos.create', compact('subcategorias', 'impuestos'));
    }

    public function createAfiliado(Request $request)
    {
        $subcategorias = Subcategoria::all();

        if (!$request->session()->has('productos_registrados')) {
            $request->session()->put('productos_registrados', 0);
        }

        $productosRegistrados = $request->session()->get('productos_registrados');
        $productosDisponibles = max(0, 5 - $productosRegistrados);

        if ($productosDisponibles <= 0) {
            return redirect('/')->with('success', 'Has alcanzado el límite de 5 productos.');
        }

        return view('productos.create-afiliado', compact('subcategorias', 'productosDisponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'subcategoria_id' => 'required|exists:subcategorias,id',
            'impuesto_id' => 'required|exists:impuestos,id',
            'estado' => 'required|in:pendiente,rechazado,aprobado',
            'afiliado_id' => 'nullable|exists:afiliados,id',
        ]);

        Producto::create($request->all());

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function storeAfiliado(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'subcategoria_id' => 'required|exists:subcategorias,id',
            'action' => 'required|in:agregar-otro,finalizar',
            'empresa_id' => 'required|exists:empresas,id',
        ]);

        $productosRegistrados = $request->session()->get('productos_registrados', 0);
        $productosGuardados = $request->session()->get('productos_guardados', []);
        $empresaId = $request->input('empresa_id');

        if ($productosRegistrados >= 5 && $request->input('action') === 'agregar-otro') {
            return redirect('/')->with('success', 'Has alcanzado el límite de 5 productos.');
        }

        $producto = Producto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'subcategoria_id' => $request->subcategoria_id,
            'estado' => 'pendiente',
        ]);
        
        $productosGuardados[] = $producto->id;
        $request->session()->put('productos_guardados', $productosGuardados);
        $request->session()->increment('productos_registrados');

        if ($request->input('action') === 'agregar-otro') {
            return redirect()->route('afiliados.productos.create', ['empresa_id' => $empresaId])
                            ->with('success', 'Producto guardado. Puedes agregar otro.');
        }
        
        return $this->processAndStoreMarcas($request, $empresaId);
    }

    private function processAndStoreMarcas(Request $request, $empresaId)
    {
        $productosGuardados = $request->session()->get('productos_guardados', []);

        if (empty($productosGuardados) || !$empresaId) {
            return redirect('/')->with('error', 'No se pudieron registrar las marcas. Faltan datos.');
        }

        foreach ($productosGuardados as $productoId) {
            $codigoMarca = Str::random(10);
            while (Marca::where('codigo_marca', $codigoMarca)->exists()) {
                $codigoMarca = Str::random(10);
            }

            Marca::create([
                'producto_id' => $productoId,
                'empresa_id' => $empresaId,
                'codigo_marca' => $codigoMarca,
                'estado' => 'pendiente',
            ]);
        }

        $request->session()->forget(['productos_registrados', 'productos_guardados']);

        return redirect('/')->with('success', '¡Productos y marcas registrados exitosamente!');
    }

    public function show(Producto $producto)
    {
        $producto->load('subcategoria.categoria', 'impuesto');
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $subcategorias = Subcategoria::all();
        $impuestos = Impuesto::all();
        
        $producto->load('impuesto'); 

        return view('productos.edit', compact('producto', 'subcategorias', 'impuestos'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'subcategoria_id' => 'required|exists:subcategorias,id',
            'impuesto_id' => 'required|exists:impuestos,id',
            'estado' => 'required|in:pendiente,rechazado,aprobado',
        ]);

        $producto->update($request->all());

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Producto eliminado exitosamente.']);
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}
