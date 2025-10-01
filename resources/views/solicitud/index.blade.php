<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Solicitudes de Empresas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes de Notificación --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4 shadow-md" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg relative mb-4 shadow-md" role="alert">
                    {{ session('warning') }}
                </div>
            @endif

            {{-- LÓGICA DEL FILTRO DE ESTADO --}}
            @php
                // Determina el estado actual del filtro (default: 'pendiente')
                $currentStatus = request('estado', 'pendiente');
                
                // Define los estados y sus etiquetas
                $statuses = [
                    'pendiente' => ['label' => 'Pendientes', 'icon' => 'fas fa-clock', 'color' => 'yellow'],
                    'aprobado'  => ['label' => 'Aprobadas', 'icon' => 'fas fa-check-circle', 'color' => 'green'],
                    'rechazado' => ['label' => 'Rechazadas', 'icon' => 'fas fa-times-circle', 'color' => 'red'],
                ];
            @endphp

            {{-- BARRA DE PESTAÑAS (TABS) --}}
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                    @foreach ($statuses as $statusKey => $statusData)
                        @php
                            $isActive = $currentStatus === $statusKey;
                        @endphp
                        <li class="mr-2">
                            <a href="{{ route('solicitud.index', ['estado' => $statusKey]) }}" 
                                class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg transition duration-200 ease-in-out
                                        @if ($isActive)
                                            text-{{ $statusData['color'] }}-600 border-{{ $statusData['color'] }}-600 dark:text-{{ $statusData['color'] }}-500 dark:border-{{ $statusData['color'] }}-500 font-bold
                                        @else
                                            border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300
                                        @endif">
                                <i class="{{ $statusData['icon'] }} w-4 h-4 mr-2"></i>
                                {{ $statusData['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            {{-- CONTEO DE SOLICITUDES PENDIENTES (Actualizado) --}}
            <div class="mb-6">
                @if ($empresas->total() > 0)
                    <p class="text-gray-600 dark:text-gray-300 text-lg font-medium">
                        Mostrando <span class="font-extrabold">{{ $empresas->total() }}</span> solicitudes en estado <span class="text-{{ $statuses[$currentStatus]['color'] }}-500 font-extrabold">{{ ucfirst($currentStatus) }}</span>.
                    </p>
                @else
                    <p class="text-gray-600 dark:text-gray-300 text-lg font-medium">
                        No hay solicitudes en estado <span class="text-{{ $statuses[$currentStatus]['color'] }}-500 font-extrabold">{{ ucfirst($currentStatus) }}</span>.
                    </p>
                @endif
            </div>

            {{-- LAYOUT DE TARJETAS RESPONSIVE --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                
                @forelse ($empresas as $empresa)
                    @php
                        $cardColor = $statuses[$empresa->estado]['color'] ?? 'gray';
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden border-t-8 border-{{ $cardColor }}-500 dark:border-{{ $cardColor }}-400 hover:shadow-2xl transition duration-300">
                        <div class="p-6">
                            
                            {{-- Título y Etiqueta de Estado --}}
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-xl font-extrabold text-gray-900 dark:text-white leading-tight">
                                    {{ $empresa->nombre_negocio }}
                                </h3>
                                {{-- Etiqueta de Estado Dinámico --}}
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-{{ $cardColor }}-100 text-{{ $cardColor }}-700 dark:bg-{{ $cardColor }}-900/50 dark:text-{{ $cardColor }}-300 shadow-sm">
                                    {{ ucfirst($empresa->estado) }}
                                </span>
                            </div>

                            {{-- Información Clave --}}
                            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                <p>
                                    <i class="fas fa-user-tag w-4 mr-2 text-blue-500"></i>
                                    Afiliado: <span class="font-semibold">{{ $empresa->afiliado->nombre ?? 'N/A' }}</span>
                                </p>
                                <p>
                                    <i class="fas fa-industry w-4 mr-2 text-blue-500"></i>
                                    Rubro: {{ $empresa->rubro->nombre ?? 'N/A' }}
                                </p>
                                <p>
                                    <i class="fas fa-calendar-alt w-4 mr-2 text-blue-500"></i>
                                    Solicitado: {{ $empresa->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        {{-- Botón de Acción (Footer de la Tarjeta) --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 border-t flex justify-end">
                            <a href="{{ route('solicitud.show', $empresa) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-bold rounded-lg shadow-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                                Revisar Solicitud
                                <i class="fas fa-arrow-right w-3 h-3 ml-2"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    {{-- Mensaje si no hay solicitudes --}}
                    <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg shadow-xl p-10 text-center">
                        <i class="{{ $statuses[$currentStatus]['icon'] }} text-{{ $statuses[$currentStatus]['color'] }}-500 text-3xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                            No hay solicitudes en este estado.
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">
                            Revisa el estado "Pendientes" para gestionar las solicitudes nuevas.
                        </p>
                    </div>
                @endforelse
            </div>
            
            {{-- Enlaces de paginación --}}
            @if ($empresas->total() > 0)
                <div class="mt-8">
                    {{ $empresas->appends(['estado' => $currentStatus])->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
