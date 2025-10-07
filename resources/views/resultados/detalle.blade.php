<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Detalle de Resultado - {{ $empresa->nombre_negocio }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/welcome.css') }}"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            .status-card {
                animation: fadeInScale 0.5s ease-out;
            }
            @keyframes fadeInScale {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }
            .info-card {
                transition: all 0.3s ease;
            }
            .info-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            }
            .product-item {
                animation: slideIn 0.4s ease-out;
                animation-fill-mode: both;
            }
            .product-item:nth-child(1) { animation-delay: 0.1s; }
            .product-item:nth-child(2) { animation-delay: 0.2s; }
            .product-item:nth-child(3) { animation-delay: 0.3s; }
            .product-item:nth-child(4) { animation-delay: 0.4s; }
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            .print-button {
                transition: all 0.3s ease;
            }
            .print-button:hover {
                transform: scale(1.05);
            }
            @media print {
                .no-print {
                    display: none !important;
                }
                body {
                    background: white !important;
                }
            }
        </style>
    </head>
    <body class="body-animated-gradient dark:dark-body-animated-gradient text-[#1b1b18] dark:text-[#EDEDEC] font-sans min-h-screen">

        @php
            $resultado = $empresa->resultado;
            $estado = $resultado->estado;
            $isAprobado = $estado == 'aprobado';
            $estadoConfig = [
                'aprobado' => [
                    'color' => 'green',
                    'icon' => 'fa-check-circle',
                    'bg' => 'from-green-500 to-emerald-600',
                    'text' => 'Aprobado',
                    'bgLight' => 'bg-green-50 dark:bg-green-900/20',
                    'borderColor' => 'border-green-500',
                    'textColor' => 'text-green-700 dark:text-green-400'
                ],
                'rechazado' => [
                    'color' => 'red',
                    'icon' => 'fa-times-circle',
                    'bg' => 'from-red-500 to-rose-600',
                    'text' => 'Rechazado',
                    'bgLight' => 'bg-red-50 dark:bg-red-900/20',
                    'borderColor' => 'border-red-500',
                    'textColor' => 'text-red-700 dark:text-red-400'
                ]
            ];
            $config = $estadoConfig[$estado] ?? $estadoConfig['rechazado'];
        @endphp

        {{-- HEADER MEJORADO --}}
        <header class="w-full text-base mb-8 absolute top-0 left-0 p-6 lg:p-8 z-10 no-print">
            <nav class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                </div>
                <div class="flex gap-3">
                    <button onclick="window.print()" class="print-button inline-flex items-center gap-2 px-4 py-2 text-purple-300 border border-purple-400 rounded-md text-sm font-medium leading-normal hover:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <i class="fas fa-print"></i>
                        <span class="hidden sm:inline">Imprimir</span>
                    </button>
                    <a
                        href="{{ route('resultados.buscar') }}"
                        class="inline-flex items-center gap-2 px-6 py-2 text-yellow-300 border border-yellow-400 rounded-md text-sm font-medium leading-normal hover:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 btn-hover-scale transition-all duration-200"
                    >
                        <i class="fas fa-arrow-left"></i>
                        <span>Volver a Buscar</span>
                    </a>
                </div>
            </nav>
        </header>
        
        <div class="py-16 pt-32 lg:pt-40 w-full">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-2xl p-6 lg:p-10">

                    {{-- ENCABEZADO CON ESTADO --}}
                    <div class="status-card text-center mb-10">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br {{ $config['bg'] }} mb-4 shadow-xl">
                            <i class="fas {{ $config['icon'] }} text-white text-3xl"></i>
                        </div>
                        <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 dark:text-white mb-3">
                            {{ $empresa->nombre_negocio }}
                        </h2>
                        <div class="flex items-center justify-center gap-3 mb-4">
                            <span class="inline-flex items-center gap-2 px-6 py-2 text-lg font-bold uppercase rounded-full bg-gradient-to-r {{ $config['bg'] }} text-white shadow-lg">
                                <i class="fas {{ $config['icon'] }}"></i>
                                {{ $config['text'] }}
                            </span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            Última actualización: {{ $resultado->updated_at->format('d/M/Y H:i') }}
                        </p>
                    </div>

                    {{-- TARJETAS DE INFORMACIÓN RÁPIDA --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                        <div class="info-card bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 p-5 rounded-xl border-2 border-blue-200 dark:border-blue-700 shadow-md">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center">
                                    <i class="fas fa-id-card text-white"></i>
                                </div>
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-300 uppercase">DNI Afiliado</span>
                            </div>
                            <p class="text-xl font-bold text-gray-900 dark:text-white font-mono">{{ $resultado->afiliado_dni }}</p>
                        </div>

                        <div class="info-card bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/20 p-5 rounded-xl border-2 border-purple-200 dark:border-purple-700 shadow-md">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-lg bg-purple-500 flex items-center justify-center">
                                    <i class="fas fa-box text-white"></i>
                                </div>
                                <span class="text-xs font-semibold text-purple-600 dark:text-purple-300 uppercase">Productos</span>
                            </div>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $empresa->productos->count() }}</p>
                        </div>

                        <div class="info-card bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/30 dark:to-amber-800/20 p-5 rounded-xl border-2 border-amber-200 dark:border-amber-700 shadow-md">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-lg bg-amber-500 flex items-center justify-center">
                                    <i class="fas fa-building text-white"></i>
                                </div>
                                <span class="text-xs font-semibold text-amber-600 dark:text-amber-300 uppercase">Tipo Org.</span>
                            </div>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $empresa->tipoOrganizacion->nombre ?? 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- SECCIÓN DEL DICTAMEN --}}
                    <div class="mb-10 p-6 rounded-xl border-l-4 {{ $config['borderColor'] }} {{ $config['bgLight'] }} shadow-lg">
                        <h3 class="text-2xl font-bold mb-4 {{ $config['textColor'] }} flex items-center">
                            <i class="fas fa-gavel mr-3 text-2xl"></i> 
                            Dictamen de Revisión
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-calendar-check text-gray-400 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Fecha de Emisión</p>
                                    <p class="text-gray-800 dark:text-gray-200 font-semibold">{{ $resultado->updated_at->format('d/M/Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-user-check text-gray-400 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Estado Final</p>
                                    <p class="text-gray-800 dark:text-gray-200 font-semibold">{{ ucfirst($estado) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-5 bg-white dark:bg-gray-800 rounded-lg border-2 border-gray-200 dark:border-gray-600 shadow-inner">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-comment-alt text-indigo-500"></i>
                                <p class="font-bold text-gray-800 dark:text-gray-200">Comentario del Revisor:</p>
                            </div>
                            <div class="pl-7">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $resultado->comentario ?: 'Sin comentarios adicionales.' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- DATOS GENERALES DE LA EMPRESA --}}
                    <div class="mb-10">
                        <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-blue-200 dark:border-blue-700">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-md">
                                <i class="fas fa-building text-white text-lg"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                Información del Negocio
                            </h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas fa-tag text-blue-500"></i>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Rubro</span>
                                </div>
                                <p class="text-gray-900 dark:text-white font-semibold">{{ $empresa->rubro->nombre ?? 'N/A' }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas fa-sitemap text-purple-500"></i>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tipo de Organización</span>
                                </div>
                                <p class="text-gray-900 dark:text-white font-semibold">{{ $empresa->tipoOrganizacion->nombre ?? 'N/A' }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas fa-map-marker-alt text-red-500"></i>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Dirección</span>
                                </div>
                                <p class="text-gray-900 dark:text-white font-semibold">{{ $empresa->direccion }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas fa-globe-americas text-green-500"></i>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">País de Exportación</span>
                                </div>
                                <p class="text-gray-900 dark:text-white font-semibold">{{ $empresa->paisExportacion->nombre ?? 'No aplica' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN PRODUCTOS --}}
                    <div>
                        <div class="flex items-center justify-between mb-6 pb-3 border-b-2 border-purple-200 dark:border-purple-700">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-md">
                                    <i class="fas fa-boxes text-white text-lg"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                    Productos y Marcas
                                </h3>
                            </div>
                            <span class="px-4 py-1.5 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 rounded-full text-sm font-bold">
                                {{ $empresa->productos->count() }} Total
                            </span>
                        </div>

                        @php
                            $productosAprobados = $empresa->productos->where('pivot.estado', 'aprobado')->count();
                            $productosRechazados = $empresa->productos->where('pivot.estado', 'rechazado')->count();
                        @endphp

                        @if($empresa->productos->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border-l-4 border-green-500">
                                    <p class="text-xs text-green-600 dark:text-green-400 font-semibold mb-1 uppercase">Aprobados</p>
                                    <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $productosAprobados }}</p>
                                </div>
                                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border-l-4 border-red-500">
                                    <p class="text-xs text-red-600 dark:text-red-400 font-semibold mb-1 uppercase">Rechazados</p>
                                    <p class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $productosRechazados }}</p>
                                </div>
                                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border-l-4 border-yellow-500">
                                    <p class="text-xs text-yellow-600 dark:text-yellow-400 font-semibold mb-1 uppercase">Pendientes</p>
                                    <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $empresa->productos->count() - $productosAprobados - $productosRechazados }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <div class="space-y-3">
                            @forelse ($empresa->productos as $index => $producto)
                                @php
                                    $productoEstado = $producto->pivot->estado;
                                    $prodConfig = [
                                        'aprobado' => [
                                            'bg' => 'bg-green-50 dark:bg-green-900/20',
                                            'border' => 'border-green-500',
                                            'badge' => 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300',
                                            'icon' => 'fa-check-circle',
                                            'iconColor' => 'text-green-500'
                                        ],
                                        'rechazado' => [
                                            'bg' => 'bg-red-50 dark:bg-red-900/20',
                                            'border' => 'border-red-500',
                                            'badge' => 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300',
                                            'icon' => 'fa-times-circle',
                                            'iconColor' => 'text-red-500'
                                        ],
                                        'pendiente' => [
                                            'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
                                            'border' => 'border-yellow-500',
                                            'badge' => 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300',
                                            'icon' => 'fa-clock',
                                            'iconColor' => 'text-yellow-500'
                                        ]
                                    ];
                                    $prodStyle = $prodConfig[$productoEstado] ?? $prodConfig['pendiente'];
                                @endphp
                                <div class="product-item p-5 border-2 rounded-xl {{ $prodStyle['bg'] }} {{ $prodStyle['border'] }} border-l-4 shadow-md hover:shadow-lg transition-all duration-300">
                                    <div class="flex justify-between items-start gap-4">
                                        <div class="flex-grow">
                                            <div class="flex items-center gap-3 mb-2">
                                                <i class="fas {{ $prodStyle['icon'] }} {{ $prodStyle['iconColor'] }} text-xl"></i>
                                                <h4 class="font-bold text-lg text-gray-900 dark:text-white">{{ $producto->nombre }}</h4>
                                            </div>
                                            <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400 ml-8">
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-layer-group text-xs"></i>
                                                    {{ $producto->subcategoria->categoria->nombre ?? 'N/A' }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-list text-xs"></i>
                                                    {{ $producto->subcategoria->nombre ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="flex-shrink-0 text-xs uppercase font-bold px-4 py-2 rounded-full {{ $prodStyle['badge'] }} shadow-sm">
                                            {{ $productoEstado }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center p-10 bg-gray-50 dark:bg-gray-700/30 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                                    <i class="fas fa-box-open text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                    <p class="text-gray-500 dark:text-gray-400 italic text-lg">No hay productos registrados públicamente.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- PIE DE PÁGINA --}}
                    <div class="mt-10 pt-6 border-t-2 border-gray-200 dark:border-gray-700 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center justify-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            <span>Documento generado el {{ now()->format('d/M/Y H:i') }}</span>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </body>
</html>