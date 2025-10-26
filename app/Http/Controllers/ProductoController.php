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
    /**
     * Muestra una lista de todos los productos con paginación y búsqueda.
     */
    public function index(Request $request)
    {
        // Se carga la relación 'impuesto' para mostrar la información en la tabla si es necesario
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

    /**
     * Muestra el formulario para crear un nuevo producto (para el Administrador).
     */
    public function create()
    {
        $subcategorias = Subcategoria::all();
        $impuestos = Impuesto::all();
        return view('productos.create', compact('subcategorias', 'impuestos'));
    }

    /**
     * Muestra el formulario para que un afiliado sugiera un nuevo producto.
     */
    public function createAfiliado(Request $request)
    {
        $subcategorias = Subcategoria::all();

        // Inicializa el contador de productos en la sesión si no existe
        if (!$request->session()->has('productos_registrados')) {
            $request->session()->put('productos_registrados', 0);
        }

        $productosRegistrados = $request->session()->get('productos_registrados');
        $productosDisponibles = max(0, 5 - $productosRegistrados);

        // Si ya registró el máximo, redirige al inicio
        if ($productosDisponibles <= 0) {
            return redirect('/')->with('success', 'Has alcanzado el límite de 5 productos.');
        }

        return view('productos.create-afiliado', compact('subcategorias', 'productosDisponibles'));
    }

    /**
     * Almacena un nuevo producto en la base de datos (para el Administrador).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'subcategoria_id' => 'required|exists:subcategorias,id',
            'impuesto_id' => 'required|exists:impuestos,id',
            // Se eliminó la validación 'permite_facturacion'
            'estado' => 'required|in:pendiente,rechazado,aprobado',
            'afiliado_id' => 'nullable|exists:afiliados,id',
        ]);

        // Ya que 'permite_facturacion' fue eliminado del modelo y la validación,
        // $request->all() ya no contendrá este campo si venía del formulario,
        // o si venía, será ignorado por el modelo ya que no está en $fillable.
        Producto::create($request->all());

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Almacena un producto sugerido por un afiliado en la base de datos.
     */
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
        
        // Agrega el ID del producto guardado a la sesión
        $productosGuardados[] = $producto->id;
        $request->session()->put('productos_guardados', $productosGuardados);
        
        // Incrementa el contador de productos registrados
        $request->session()->increment('productos_registrados');

        if ($request->input('action') === 'agregar-otro') {
            return redirect()->route('afiliados.productos.create', ['empresa_id' => $empresaId])
                            ->with('success', 'Producto guardado. Puedes agregar otro.');
        }
        
        // Llama a la función para procesar las marcas y luego limpia la sesión
        return $this->processAndStoreMarcas($request, $empresaId);
    }

    /**
     * Procesa y almacena las marcas una vez finalizado el registro de productos.
     */
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

        // Limpia las variables de la sesión una vez completado el proceso
        $request->session()->forget(['productos_registrados', 'productos_guardados']);

        return redirect('/')->with('success', '¡Productos y marcas registrados exitosamente!');
    }

    /**
     * Muestra los detalles de un producto específico.
     */
    public function show(Producto $producto)
    {
        // Asegúrate de cargar las relaciones
        $producto->load('subcategoria.categoria', 'impuesto');
        return view('productos.show', compact('producto'));
    }

    /**
     * Muestra el formulario para editar un producto.
     */
    public function edit(Producto $producto)
    {
        $subcategorias = Subcategoria::all();
        $impuestos = Impuesto::all();
        
        // Cargar la relación del impuesto para asegurar que esté disponible en la vista
        $producto->load('impuesto'); 

        // Pasar las variables a la vista
        return view('productos.edit', compact('producto', 'subcategorias', 'impuestos'));
    }

    /**
     * Actualiza un producto existente en la base de datos.
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'subcategoria_id' => 'required|exists:subcategorias,id',
            'impuesto_id' => 'required|exists:impuestos,id',
            // Se eliminó la validación 'permite_facturacion'
            'estado' => 'required|in:pendiente,rechazado,aprobado',
        ]);

        // Se eliminó la lógica que manejaba el campo 'permite_facturacion'.
        // Ahora solo se actualiza con los campos restantes que vienen en el request.
        $producto->update($request->all());

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Elimina un producto de la base de datos.
     */
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
