<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\Afiliado;
use App\Models\Municipio;
use Illuminate\Http\JsonResponse;

class AfiliadoController extends Controller
{
    /**
     * URL base de la API y credenciales.
     */
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

    /**
     * Muestra el formulario de consulta.
     */
    public function index()
    {
        return view('afiliados.registro-afiliado');
    }

    /**
     * Procesa la consulta, busca en la base de datos y/o llama a la API y muestra los datos del afiliado.
     */
    public function query(Request $request)
    {
        try {
            $request->validate(['dni' => 'required|string|min:10|max:15']);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            return redirect()->route('afiliados.index')->withErrors($e->errors());
        }

        $dni = $request->input('dni');

        // 1. Buscar en la base de datos local incluyendo la relación.
        $afiliado = Afiliado::with('municipio.departamento')->where('dni', $dni)->first();
        
        if ($afiliado) {
            // El afiliado existe en la base de datos.
            $afiliadoData = [
                'nombre_afiliado'       => $afiliado->nombre,
                'genero'                => $afiliado->genero,
                'fecha_de_nacimiento'   => $afiliado->fecha_nacimiento,
                'correo_electronico'    => $afiliado->email,
                'telefono'              => $afiliado->telefono,
                'nombre_municipio'      => optional($afiliado->municipio)->nombre,
                'nombre_departamento'   => optional($afiliado->municipio->departamento)->nombre, // Agregado
                'nombre_barrio'         => $afiliado->barrio,
                'rtn'                   => $afiliado->rtn,
                'numero_cuenta'         => $afiliado->numero_cuenta,
            ];
            
            return response()->json([
                'afiliado' => $afiliadoData,
                'source'   => 'database' // Bandera para saber el origen
            ]);
        }
        
        // 2. Si no se encontró en la base de datos, buscar en la API.
        $afiliadoApi = $this->getAfiliadoFromApi($dni);
        
        if (isset($afiliadoApi['error'])) {
            // La API retornó un error o no encontró el afiliado.
            return response()->json(['error' => $afiliadoApi['error']]);
        }
        
        // La API encontró al afiliado, retornar sus datos.
        return response()->json([
            'afiliado' => $afiliadoApi,
            'source'   => 'api' // Bandera para saber el origen
        ]);
    }

    /**
     * Llama a la API de CORE para obtener los datos de un afiliado.
     */
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
    
    /**
     * Almacena o edita un nuevo afiliado en la base de datos.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerFromQuery(Request $request)
    {
        try {
            $request->validate([
                'dni'                 => 'required|string',
                'nombre'              => 'required|string|max:255',
                'genero'              => 'nullable|string|in:MASCULINO,FEMENINO',
                'fecha_nacimiento'    => 'nullable|date',
                'email'               => 'nullable|string',
                'telefono'            => 'nullable|string|max:20',
                'departamento_nombre' => 'nullable|string', // Se valida solo el nombre
                'municipio_nombre'    => 'nullable|string', // Se valida solo el nombre
                'barrio'              => 'nullable|string|max:255',
                'rtn'                 => 'nullable|string|max:255',
                'numero_cuenta'       => 'required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

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
        
        $data = [
            'dni'              => $request->input('dni'),
            'nombre'           => $request->input('nombre'),
            'genero'           => $request->input('genero'),
            'fecha_nacimiento' => $request->input('fecha_nacimiento') ? Carbon::parse($request->input('fecha_nacimiento')) : null,
            'email'            => $request->input('email'),
            'telefono'         => $request->input('telefono'),
            'municipio_id'     => $municipioId,
            'barrio'           => $request->input('barrio'),
            'rtn'              => $request->input('rtn'),
            'numero_cuenta'    => $request->input('numero_cuenta'),
            'status'           => 0,
        ];

        try {
            $afiliado = Afiliado::updateOrCreate(['dni' => $request->input('dni')], $data);
            
            $message = $afiliado->wasRecentlyCreated ? 'Afiliado registrado exitosamente.' : 'Afiliado actualizado exitosamente.';

            return response()->json([
                'message'     => $message,
                'afiliado'    => $afiliado,
                'afiliado_id' => $afiliado->id
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al guardar el afiliado.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Muestra la vista de la lista de afiliados.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $query = $request->input('search');

        $afiliados = Afiliado::with('municipio.departamento') // Se agregó la carga de la relación.
            ->when($query, function ($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                  ->orWhere('dni', 'like', "%{$query}%");
            })
            ->latest()
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'table_rows' => view('afiliados.partials.table_rows', compact('afiliados'))->render(),
                'pagination_links' => $afiliados->links()->toHtml(),
            ]);
        }
        
        return view('afiliados.listado-afiliados', compact('afiliados'));
    }

    public function show(Afiliado $afiliado)
    {
        // Cargar la relación para mostrar el nombre del departamento
        $afiliado->load('municipio.departamento');
        return view('afiliados.show', compact('afiliado'));
    }

    public function edit(Afiliado $afiliado)
    {
        // Cargar la relación para mostrar el nombre del departamento
        $afiliado->load('municipio.departamento');
        $municipios = Municipio::all();
        return view('afiliados.edit', compact('afiliado', 'municipios'));
    }

    /**
     * Actualiza un afiliado existente en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Afiliado  $afiliado
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Afiliado $afiliado)
    {
        $request->validate([
            'nombre'           => 'required|string|max:255',
            'email'            => 'nullable|string|email|max:255',
            'telefono'         => 'nullable|string|max:20',
            'municipio_id'     => 'nullable|exists:municipios,id',
            'barrio'           => 'nullable|string|max:255',
            'rtn'              => 'nullable|string|max:255',
            'numero_cuenta'    => 'required|string|max:255',
            'status'           => 'required|in:0,1,2',
        ]);

        $afiliado->update($request->all());

        return redirect()->route('afiliados.list')
                         ->with('success', 'Afiliado actualizado exitosamente.');
    }

    /**
     * Elimina un afiliado de la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Afiliado  $afiliado
     * @return \Illuminate\Http\RedirectResponse|JsonResponse
     */
    public function destroy(Request $request, Afiliado $afiliado)
    {
        try {
            $afiliado->delete();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Afiliado eliminado exitosamente.']);
            }

            return redirect()->route('afiliados.list')->with('success', 'Afiliado eliminado exitosamente.');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al eliminar el afiliado: ' . $e->getMessage()], 500);
            }

            return redirect()->route('afiliados.list')->with('error', 'Error al eliminar el afiliado: ' . $e->getMessage());
        }
    }
}