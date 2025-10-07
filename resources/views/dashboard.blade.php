<x-app-layout>
    <x-slot name="header">
        
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- MENSAJE DE BIENVENIDA --}}
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-3xl font-bold mb-2">
                                ¡Bienvenido de nuevo! 
                            </h3>
                            <p class="text-indigo-100 text-lg">
                                Tienes sesión iniciada como <strong>{{ Auth::user()->name }}</strong>
                            </p>
                            <p class="text-indigo-200 text-sm mt-2">
                                Sistema de Gestión de Empresas - Panel de Control
                            </p>
                        </div>
                        <div class="hidden md:block">
                            <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-user-circle text-7xl opacity-80"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TARJETAS DE ESTADÍSTICAS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Total de Empresas --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-building text-white text-2xl"></i>
                            </div>
                            @if($crecimientoEmpresas != 0)
                                <span class="text-sm font-semibold {{ $crecimientoEmpresas > 0 ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30' : 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30' }} px-3 py-1 rounded-full">
                                    <i class="fas fa-arrow-{{ $crecimientoEmpresas > 0 ? 'up' : 'down' }} mr-1"></i>{{ abs($crecimientoEmpresas) }}%
                                </span>
                            @endif
                        </div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Total Empresas</h3>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ $totalEmpresas }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Registradas en el sistema</p>
                    </div>
                </div>

                {{-- Solicitudes Pendientes --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-clock text-white text-2xl"></i>
                            </div>
                            @if($empresasPendientes > 0)
                                <span class="text-sm font-semibold text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/30 px-3 py-1 rounded-full">
                                    <i class="fas fa-exclamation-circle"></i>
                                </span>
                            @endif
                        </div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Pendientes</h3>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ $empresasPendientes }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">En espera de revisión</p>
                    </div>
                </div>

                {{-- Aprobadas --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-check-circle text-white text-2xl"></i>
                            </div>
                            @if($tendenciaAprobadas != 0)
                                <span class="text-sm font-semibold {{ $tendenciaAprobadas > 0 ? 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30' : 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30' }} px-3 py-1 rounded-full">
                                    <i class="fas fa-arrow-{{ $tendenciaAprobadas > 0 ? 'up' : 'down' }} mr-1"></i>{{ abs($tendenciaAprobadas) }}%
                                </span>
                            @endif
                        </div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Aprobadas</h3>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ $empresasAprobadas }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Solicitudes aceptadas</p>
                    </div>
                </div>

                {{-- Rechazadas --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-red-400 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-times-circle text-white text-2xl"></i>
                            </div>
                            @if($tendenciaRechazadas != 0)
                                <span class="text-sm font-semibold {{ $tendenciaRechazadas > 0 ? 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30' : 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30' }} px-3 py-1 rounded-full">
                                    <i class="fas fa-arrow-{{ $tendenciaRechazadas > 0 ? 'up' : 'down' }} mr-1"></i>{{ abs($tendenciaRechazadas) }}%
                                </span>
                            @endif
                        </div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Rechazadas</h3>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ $empresasRechazadas }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">No cumplieron requisitos</p>
                    </div>
                </div>

            </div>

            {{-- GRÁFICO DE PROGRESO --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-chart-bar text-indigo-500"></i>
                            Estado de Solicitudes
                        </h3>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Datos actuales
                        </div>
                    </div>

                    {{-- Barras de progreso --}}
                    <div class="space-y-6">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Aprobadas</span>
                                </div>
                                <span class="text-sm font-bold text-green-600">{{ $empresasAprobadas }} ({{ $porcentajeAprobadas }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 shadow-inner">
                                <div class="bg-gradient-to-r from-green-400 to-green-600 h-4 rounded-full transition-all duration-1000 flex items-center justify-end pr-2" 
                                     style="width: {{ $porcentajeAprobadas }}%">
                                    @if($porcentajeAprobadas > 10)
                                        <span class="text-xs text-white font-semibold">{{ $porcentajeAprobadas }}%</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-yellow-500"></i>
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pendientes</span>
                                </div>
                                <span class="text-sm font-bold text-yellow-600">{{ $empresasPendientes }} ({{ $porcentajePendientes }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 shadow-inner">
                                <div class="bg-gradient-to-r from-yellow-400 to-orange-500 h-4 rounded-full transition-all duration-1000 flex items-center justify-end pr-2" 
                                     style="width: {{ $porcentajePendientes }}%">
                                    @if($porcentajePendientes > 10)
                                        <span class="text-xs text-white font-semibold">{{ $porcentajePendientes }}%</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-times-circle text-red-500"></i>
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Rechazadas</span>
                                </div>
                                <span class="text-sm font-bold text-red-600">{{ $empresasRechazadas }} ({{ $porcentajeRechazadas }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 shadow-inner">
                                <div class="bg-gradient-to-r from-red-400 to-rose-600 h-4 rounded-full transition-all duration-1000 flex items-center justify-end pr-2" 
                                     style="width: {{ $porcentajeRechazadas }}%">
                                    @if($porcentajeRechazadas > 10)
                                        <span class="text-xs text-white font-semibold">{{ $porcentajeRechazadas }}%</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Estadísticas adicionales --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 pt-6 border-t-2 border-gray-200 dark:border-gray-700">
                        <div class="text-center p-4 bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-xl">
                            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mb-1">
                                {{ $tasaAprobacion }}%
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Tasa de Aprobación</p>
                        </div>
                        <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl">
                            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-1">
                                {{ $totalProductos }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Productos Registrados</p>
                        </div>
                        <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl">
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                                {{ $totalAfiliados }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Afiliados Activos</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACCESOS RÁPIDOS --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <i class="fas fa-bolt text-yellow-500"></i>
                        Accesos Rápidos
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        
                        {{-- NUEVO: Ver Tiendas --}}
                        <a href="{{ route('tiendas.index') }}" class="flex flex-col items-center p-6 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-indigo-500 hover:shadow-xl transition-all duration-200 group bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
                            <div class="w-16 h-16 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-lg">
                                <i class="fas fa-store text-white text-3xl"></i>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 text-center">Ver Tiendas</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Administrar</span>
                        </a>

                        <a href="{{ route('solicitud.index') }}" class="flex flex-col items-center p-6 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-xl transition-all duration-200 group bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-lg">
                                <i class="fas fa-list text-white text-3xl"></i>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 text-center">Ver Solicitudes</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gestionar</span>
                        </a>

                        {{-- NUEVO: Ver Vinculaciones --}}
                        <a href="{{ route('asociaciones.index') }}" class="flex flex-col items-center p-6 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-500 hover:shadow-xl transition-all duration-200 group bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-lg">
                                <i class="fas fa-link text-white text-3xl"></i>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 text-center">Ver Vinculaciones</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Empresa-Tienda</span>
                        </a>

                        <a href="{{ route('empresas.index') }}" class="flex flex-col items-center p-6 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-500 hover:shadow-xl transition-all duration-200 group bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-lg">
                                <i class="fas fa-chart-pie text-white text-3xl"></i>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 text-center">Todas las Empresas</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ver listado</span>
                        </a>

                    </div>
                </div>
            </div>

            {{-- MENSAJE INFORMATIVO (solo si hay solicitudes pendientes) --}}
            @if($empresasPendientes > 0)
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-gray-800 dark:to-gray-900 border-l-4 border-yellow-500 shadow-lg sm:rounded-lg p-6">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                            Atención: Solicitudes Pendientes
                        </h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Tienes <strong>{{ $empresasPendientes }}</strong> solicitud{{ $empresasPendientes != 1 ? 'es' : '' }} pendiente{{ $empresasPendientes != 1 ? 's' : '' }} de revisión. 
                            <a href="{{ route('solicitud.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">
                                Ver solicitudes →
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- ANIMACIONES --}}
    <style>
        @keyframes fadeInUp {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        
        .max-w-7xl > div {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .max-w-7xl > div:nth-child(1) { animation-delay: 0.1s; }
        .max-w-7xl > div:nth-child(2) { animation-delay: 0.2s; }
        .max-w-7xl > div:nth-child(3) { animation-delay: 0.3s; }
        .max-w-7xl > div:nth-child(4) { animation-delay: 0.4s; }
        .max-w-7xl > div:nth-child(5) { animation-delay: 0.5s; }
    </style>
</x-app-layout>
