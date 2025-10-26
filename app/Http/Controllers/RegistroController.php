<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Afiliado;
use App\Models\Empresa;
use App\Models\Municipio;
use App\Models\Rubro;
use App\Models\TipoOrganizacion;
use App\Models\Pais;
use App\Models\Tienda;
use App\Models\Producto;
use App\Models\Subcategoria;
use App\Models\Marca;
use App\Models\EmpresaTienda;
use App\Models\Impuesto;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RegistroController extends Controller
{
    private $coreUrl;
    private $coreUsername;
    private $corePassword;
    private $coreAppId;

    public function __construct()
    {
        $this->coreUrl      = env('CORE_URL_BASE');
        $this->coreUsername = env('CORE_USERNAME');
        $this->corePassword = env('CORE_PASSWORD');
        $this->coreAppId    = env('CORE_APP_ID');
    }

    public function showForm()
    {
        $rubros = Rubro::all();
        $tiposOrganizacion = TipoOrganizacion::all();
        $paises = Pais::all();
        $tiendas = Tienda::all();
        $subcategorias = Subcategoria::all();
        $impuestos = Impuesto::all();

        return view('registro-completo', compact('rubros', 'tiposOrganizacion', 'paises', 'tiendas', 'subcategorias', 'impuestos'));
    }

    public function queryAfiliado(Request $request)
    {
        try {
            $request->validate(['dni' => 'required|string|min:10|max:15']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $dni = $request->input('dni');

        $afiliado = Afiliado::with('municipio.departamento')->where('dni', $dni)->first();
        
        if ($afiliado) {
            $afiliadoData = [
                'nombre_afiliado'       => $afiliado->nombre,
                'genero'                => $afiliado->genero,
                'fecha_de_nacimiento'   => $afiliado->fecha_nacimiento,
                'correo_electronico'    => $afiliado->email,
                'telefono'              => $afiliado->telefono,
                'nombre_municipio'      => optional($afiliado->municipio)->nombre,
                'nombre_departamento'   => optional($afiliado->municipio->departamento)->nombre,
                'nombre_barrio'         => $afiliado->barrio,
                'rtn'                   => $afiliado->rtn,
                'numero_cuenta'         => $afiliado->numero_cuenta,
            ];
            
            return response()->json([
                'afiliado' => $afiliadoData,
                'source'   => 'database'
            ]);
        }
        
        $afiliadoApi = $this->getAfiliadoFromApi($dni);
        
        if (isset($afiliadoApi['error'])) {
            return response()->json(['error' => $afiliadoApi['error']]);
        }
        
        return response()->json([
            'afiliado' => $afiliadoApi,
            'source'   => 'api'
        ]);
    }

    private function getAfiliadoFromApi(string $dni): array
    {
        try {
            $response = Http::withBasicAuth($this->coreUsername, $this->corePassword)
                            ->withHeaders(['APP_ID' => $this->coreAppId])
                            ->get("{$this->coreUrl}/UDEC_ES/GET_AFILIADO/{$dni}");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['status_code']) && $data['status_code'] == 2) {
                    return ['error' => 'Afiliado no encontrado.'];
                }
                return $data;
            }
            return ['error' => 'Error en la llamada a la API: ' . $response->status()];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return ['error' => 'No se pudo conectar con la API. Verifique la conexión o la URL.'];
        } catch (\Exception $e) {
            return ['error' => 'Ocurrió un error inesperado al procesar la solicitud.'];
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'dni'                  => 'required|string|min:10|max:15',
                'nombre'               => 'required|string|max:255',
                'genero'               => 'nullable|string|in:MASCULINO,FEMENINO',
                'fecha_nacimiento'     => 'nullable|date',
                'email'                => 'nullable|string',
                'telefono'             => 'nullable|string|max:20',
                'departamento_nombre'  => 'nullable|string',
                'municipio_nombre'     => 'nullable|string',
                'barrio'               => 'nullable|string|max:255',
                'rtn'                  => 'nullable|string|max:255',
                'numero_cuenta'        => 'required|string|max:255',
                'nombre_negocio'       => 'required|string|max:255',
                'direccion'            => 'required|string|max:255',
                'rubro_id'             => 'required|exists:rubros,id',
                'tipo_organizacion_id' => 'required|exists:tipo_organizacions,id',
                'pais_exportacion_id'  => 'nullable|exists:paises,id',
                'facturacion'          => 'nullable|boolean', // <-- VALIDACIÓN DEL CAMPO DE EMPRESA
                'tiendas'              => 'required|array',
                'tiendas.*'            => 'exists:tiendas,id',
                'productos_json'       => 'required|json',
            ]);
        } catch (ValidationException $e) {
            // Este es el ajuste clave: permite que Laravel maneje la redirección con los errores.
            return back()->withErrors($e->errors())->withInput();
        }

        $productosData = json_decode($request->input('productos_json'), true);
        if (empty($productosData)) {
            return back()->with('error', 'Debe agregar al menos un producto.')->withInput();
        }
        
        // Validar el contenido del JSON de productos de forma manual
        foreach ($productosData as $index => $productoItem) {
            $validator = validator($productoItem, [
                'nombre'              => 'required|string|max:255',
                'descripcion'         => 'nullable|string',
                'subcategoria_id'     => 'required|exists:subcategorias,id',
                'impuesto_id'         => 'required|exists:impuestos,id',
                // 'permite_facturacion' => 'required|boolean', <-- ESTE CAMPO HA SIDO ELIMINADO
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return back()->with('error', 'Error en el producto #'.($index + 1).': '.implode('; ', $errors))->withInput();
            }
        }


        DB::beginTransaction();

        try {
            $municipioId = null;
            if ($request->filled('municipio_nombre') && $request->filled('departamento_nombre')) {
                $municipio = Municipio::where('nombre', $request->input('municipio_nombre'))
                    ->whereHas('departamento', function ($query) use ($request) {
                        $query->where('nombre', $request->input('departamento_nombre'));
                    })->first();
                if ($municipio) {
                    $municipioId = $municipio->id;
                }
            }

            $afiliado = Afiliado::updateOrCreate(
                ['dni' => $validatedData['dni']],
                [
                    'nombre'           => $validatedData['nombre'],
                    'genero'           => $validatedData['genero'],
                    'fecha_nacimiento' => $validatedData['fecha_nacimiento'] ? Carbon::parse($validatedData['fecha_nacimiento']) : null,
                    'email'            => $validatedData['email'],
                    'telefono'         => $validatedData['telefono'],
                    'municipio_id'     => $municipioId,
                    'barrio'           => $validatedData['barrio'],
                    'rtn'              => $validatedData['rtn'],
                    'numero_cuenta'    => $validatedData['numero_cuenta'],
                    'status'           => 0,
                ]
            );

            $empresa = Empresa::create([
                'afiliado_id'          => $afiliado->id,
                'nombre_negocio'       => $validatedData['nombre_negocio'],
                'direccion'            => $validatedData['direccion'],
                'rubro_id'             => $validatedData['rubro_id'],
                'tipo_organizacion_id' => $validatedData['tipo_organizacion_id'],
                'pais_exportacion_id'  => $validatedData['pais_exportacion_id'],
                'facturacion'          => $request->has('facturacion'), // <-- ALMACENAMIENTO DE FACTURACION DE EMPRESA
            ]);

            foreach ($validatedData['tiendas'] as $tiendaId) {
                do {
                    $codigoAsociacion = Str::random(10);
                } while (EmpresaTienda::where('codigo_asociacion', $codigoAsociacion)->exists());
            
                $empresa->tiendas()->attach($tiendaId, [
                    'estado' => 'pendiente',
                    'codigo_asociacion' => $codigoAsociacion,
                ]);
            }

            foreach ($productosData as $productoItem) {
                
                $producto = Producto::create([
                    'nombre'              => $productoItem['nombre'],
                    'descripcion'         => $productoItem['descripcion'],
                    'subcategoria_id'     => $productoItem['subcategoria_id'],
                    'impuesto_id'         => $productoItem['impuesto_id'],
                    'permite_facturacion' => false, // <-- SE ELIMINA EL CAMPO Y SE FIJA A FALSE POR DEFECTO
                    'estado'              => 'pendiente',
                ]);
                
                $codigoMarca = Str::random(10);
                while (Marca::where('codigo_marca', $codigoMarca)->exists()) {
                    $codigoMarca = Str::random(10);
                }

                Marca::create([
                    'producto_id' => $producto->id,
                    'empresa_id' => $empresa->id,
                    'codigo_marca' => $codigoMarca,
                    'estado' => 'pendiente',
                ]);
            }

            DB::commit();

            return redirect()->route('afiliados.list')
                             ->with('success', 'Afiliado, Empresa y Productos registrados exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al guardar los datos: ' . $e->getMessage())->withInput();
        }
    }
}