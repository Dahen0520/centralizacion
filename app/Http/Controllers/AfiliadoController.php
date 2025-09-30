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

    public function index()
    {
        return view('afiliados.registro-afiliado');
    }

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
    
    public function list(Request $request)
    {
        $query = $request->input('search');

        $afiliados = Afiliado::with('municipio.departamento')
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
        $afiliado->load('municipio.departamento');
        return view('afiliados.show', compact('afiliado'));
    }

    public function edit(Afiliado $afiliado)
    {
        $afiliado->load('municipio.departamento');
        $municipios = Municipio::all();
        return view('afiliados.edit', compact('afiliado', 'municipios'));
    }

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