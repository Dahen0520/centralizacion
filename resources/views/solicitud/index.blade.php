<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Solicitudes de Empresas') }}
        </h2>
    </x-slot>

    {{-- INCLUSIÓN DE SWEETALERT2 VIA CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- EL BLOQUE DE NOTIFICACIONES HTML NATIVAS HA SIDO ELIMINADO Y REEMPLAZADO POR EL SCRIPT DE SWEETALERT AL FINAL DE LA VISTA --}}

            {{-- LÓGICA DEL FILTRO DE ESTADO --}}
            @php
                $currentStatus = request('estado', 'pendiente');
                
                $statuses = [
                    'pendiente' => [
                        'label' => 'Pendientes', 
                        'icon' => 'fas fa-clock', 
                        'color' => 'yellow',
                        'bgColor' => 'bg-yellow-100',
                        'textColor' => 'text-yellow-700',
                        'borderColor' => 'border-yellow-500'
                    ],
                    'aprobado'  => [
                        'label' => 'Aprobadas', 
                        'icon' => 'fas fa-check-circle', 
                        'color' => 'green',
                        'bgColor' => 'bg-green-100',
                        'textColor' => 'text-green-700',
                        'borderColor' => 'border-green-500'
                    ],
                    'rechazado' => [
                        'label' => 'Rechazadas', 
                        'icon' => 'fas fa-times-circle', 
                        'color' => 'red',
                        'bgColor' => 'bg-red-100',
                        'textColor' => 'text-red-700',
                        'borderColor' => 'border-red-500'
                    ],
                ];

                $currentStatusData = $statuses[$currentStatus] ?? $statuses['pendiente'];
            @endphp

            {{-- BARRA DE PESTAÑAS MEJORADA --}}
            <div class="mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-2">
                    <nav class="flex flex-wrap gap-2" aria-label="Tabs">
                        @foreach ($statuses as $statusKey => $statusData)
                            @php
                                $isActive = $currentStatus === $statusKey;
                            @endphp
                            <a href="{{ route('solicitud.index', ['estado' => $statusKey]) }}" 
                                class="flex-1 min-w-fit inline-flex items-center justify-center px-6 py-3 rounded-lg font-semibold text-sm transition-all duration-200
                                        @if ($isActive)
                                            {{ $statusData['bgColor'] }} {{ $statusData['textColor'] }} shadow-md transform scale-105
                                        @else
                                            text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white
                                        @endif">
                                <i class="{{ $statusData['icon'] }} mr-2"></i>
                                <span>{{ $statusData['label'] }}</span>
                                @if ($isActive)
                                    <span class="ml-2 px-2 py-0.5 text-xs font-bold rounded-full bg-white/50">
                                        {{ $empresas->total() }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>
            
            {{-- HEADER CON ESTADÍSTICAS --}}
            <div class="mb-8">
                <div class="bg-gradient-to-r from-{{ $currentStatusData['color'] }}-50 to-{{ $currentStatusData['color'] }}-100 dark:from-gray-800 dark:to-gray-700 rounded-xl shadow-lg p-6 border-l-4 {{ $currentStatusData['borderColor'] }}">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center">
                            <div class="bg-white dark:bg-gray-600 rounded-full p-4 shadow-md mr-4">
                                <i class="{{ $currentStatusData['icon'] }} text-3xl {{ $currentStatusData['textColor'] }}"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                                    Solicitudes {{ $currentStatusData['label'] }}
                                </h3>
                                @if ($empresas->total() > 0)
                                    <p class="text-gray-600 dark:text-gray-300 mt-1">
                                        <span class="font-bold text-xl {{ $currentStatusData['textColor'] }}">{{ $empresas->total() }}</span> 
                                        {{ Str::plural('solicitud', $empresas->total()) }} encontrada{{ $empresas->total() !== 1 ? 's' : '' }}
                                    </p>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400 mt-1">
                                        No hay solicitudes en este estado
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Botón de Acciones Rápidas (opcional) --}}
                        @if ($currentStatus === 'pendiente' && $empresas->total() > 0)
                            <div>
                                <span class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-150">
                                    <i class="fas fa-tasks mr-2"></i>
                                    {{ $empresas->total() }} por revisar
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- LAYOUT DE TARJETAS MEJORADO --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                
                @forelse ($empresas as $empresa)
                    @php
                        $cardStatusData = $statuses[$empresa->estado] ?? $statuses['pendiente'];
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border-t-4 {{ $cardStatusData['borderColor'] }} hover:shadow-2xl hover:scale-105 transition-all duration-300">
                        
                        {{-- Header de la Tarjeta --}}
                        <div class="bg-gradient-to-r from-gray-50 to-white dark:from-gray-700 dark:to-gray-800 p-5 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate mb-1">
                                        {{ $empresa->nombre_negocio }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        {{ $empresa->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <span class="ml-3 px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $cardStatusData['bgColor'] }} {{ $cardStatusData['textColor'] }} shadow-sm whitespace-nowrap">
                                    <i class="{{ $cardStatusData['icon'] }} mr-1"></i>
                                    {{ ucfirst($empresa->estado) }}
                                </span>
                            </div>
                        </div>

                        {{-- Contenido de la Tarjeta --}}
                        <div class="p-5">
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-user-tie text-blue-600 dark:text-blue-400 text-sm"></i>
                                    </div>
                                    <div class="ml-3 flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Afiliado</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $empresa->afiliado->nombre ?? 'No especificado' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-industry text-purple-600 dark:text-purple-400 text-sm"></i>
                                    </div>
                                    <div class="ml-3 flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Rubro</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $empresa->rubro->nombre ?? 'No especificado' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-clock text-green-600 dark:text-green-400 text-sm"></i>
                                    </div>
                                    <div class="ml-3 flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tiempo transcurrido</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $empresa->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer de la Tarjeta --}}
                        <div class="px-5 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('solicitud.show', $empresa) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 transform hover:scale-105">
                                <i class="fas fa-search mr-2"></i>
                                Revisar Solicitud
                                <i class="fas fa-arrow-right ml-2 text-xs"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    {{-- Estado Vacío Mejorado --}}
                    <div class="col-span-full">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-12 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full {{ $currentStatusData['bgColor'] }} mb-6">
                                <i class="{{ $currentStatusData['icon'] }} text-4xl {{ $currentStatusData['textColor'] }}"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                                No hay solicitudes {{ strtolower($currentStatusData['label']) }}
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                                @if ($currentStatus === 'pendiente')
                                    ¡Excelente! No tienes solicitudes pendientes por revisar en este momento.
                                @elseif ($currentStatus === 'aprobado')
                                    Aún no hay solicitudes aprobadas. Revisa las solicitudes pendientes para aprobar.
                                @else
                                    No se han rechazado solicitudes. Todas las solicitudes procesadas han sido aprobadas.
                                @endif
                            </p>
                            @if ($currentStatus !== 'pendiente')
                                <a href="{{ route('solicitud.index', ['estado' => 'pendiente']) }}" 
                                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-150">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Ver Solicitudes Pendientes
                                </a>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>
            
            {{-- Paginación Mejorada --}}
            @if ($empresas->hasPages())
                <div class="mt-8">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
                        {{ $empresas->appends(['estado' => $currentStatus])->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- SCRIPT DE SWEETALERT2 PARA MOSTRAR NOTIFICACIONES DE SESIÓN --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top-end'
                });
            @endif

            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: '{{ session('warning') }}',
                    showConfirmButton: true,
                    confirmButtonText: 'Entendido'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    showConfirmButton: true,
                    confirmButtonText: 'Cerrar'
                });
            @endif
        });
    </script>
</x-app-layout>