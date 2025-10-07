<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Búsqueda de Resultados</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/welcome.css') }}"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            .search-container {
                transition: all 0.3s ease;
            }
            .search-container:focus-within {
                transform: translateY(-2px);
            }
            .table-row {
                transition: all 0.2s ease;
            }
            .table-row:hover {
                transform: translateX(4px);
                background-color: rgba(59, 130, 246, 0.05);
            }
            .status-badge {
                animation: fadeIn 0.5s ease-in;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: scale(0.9); }
                to { opacity: 1; transform: scale(1); }
            }
            .empty-state {
                animation: slideUp 0.4s ease-out;
            }
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>
    <body class="body-animated-gradient dark:dark-body-animated-gradient text-[#1b1b18] dark:text-[#EDEDEC] font-sans min-h-screen">

        {{-- HEADER MEJORADO --}}
        <header class="w-full text-base mb-8 absolute top-0 left-0 p-6 lg:p-8 z-10">
            <nav class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">         
                </div>
                <a
                    href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 px-6 py-2 text-blue-300 border border-blue-400 rounded-md text-sm font-medium leading-normal hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 btn-hover-scale transition-all duration-200"
                >
                    <i class="fas fa-home"></i>
                    <span>Volver al Inicio</span>
                </a>
            </nav>
        </header>
        
        <div class="py-16 pt-32 lg:pt-40 w-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-2xl p-6 lg:p-10 mx-auto max-w-4xl">
                    
                    {{-- ENCABEZADO CON ICONO --}}
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 mb-4 shadow-lg">
                            <i class="fas fa-clipboard-check text-white text-2xl"></i>
                        </div>
                        <h2 class="text-3xl lg:text-4xl font-extrabold mb-2 text-gray-900 dark:text-white">
                            Consulta de Resultados
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                            Ingrese el número de identidad del afiliado para consultar el estado de sus solicitudes de empresa.
                        </p>
                    </div>

                    {{-- FORMULARIO DE BÚSQUEDA MEJORADO --}}
                    <form action="{{ route('resultados.buscar') }}" method="GET" class="mb-10">
                        <div class="search-container bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 p-6 rounded-xl shadow-lg border-2 border-gray-200 dark:border-gray-700">
                            <div class="mb-4">
                                <label for="dni" class="block font-semibold text-sm text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                    <i class="fas fa-id-card text-indigo-500"></i>
                                    Número de Identidad (DNI)
                                </label>
                                <div class="flex gap-3">
                                    <input 
                                        type="text" 
                                        name="dni" 
                                        id="dni" 
                                        value="{{ $dniBuscado ?? '' }}"
                                        placeholder="Ej: 0615-2003-00144"
                                        required
                                        maxlength="17"
                                        class="flex-grow border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-lg shadow-sm px-4 py-3 text-lg font-mono transition-all duration-200">
                                    <button 
                                        type="submit" 
                                        class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-3 px-8 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center gap-2 whitespace-nowrap">
                                        <i class="fas fa-search"></i>
                                        <span>Buscar</span>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 ml-1">
                                    <i class="fas fa-info-circle"></i> Formato: 0000-0000-00000 (13 dígitos)
                                </p>
                            </div>
                        </div>
                    </form>

                    {{-- INFORMACIÓN DE BÚSQUEDA --}}
                    @if ($dniBuscado)
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-lg">
                            <p class="text-sm text-blue-800 dark:text-blue-300 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i>
                                <span>Mostrando resultados para DNI: <strong class="font-mono">{{ $dniBuscado }}</strong></span>
                            </p>
                        </div>
                    @endif

                    {{-- ENCABEZADO DE RESULTADOS --}}
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-list-ul text-indigo-500"></i>
                            Historial de Resultados
                        </h4>
                        @if (!$resultados->isEmpty())
                            <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-full text-sm font-semibold">
                                {{ $resultados->count() }} {{ $resultados->count() === 1 ? 'resultado' : 'resultados' }}
                            </span>
                        @endif
                    </div>

                    {{-- ESTADOS VACÍOS Y DE ERROR --}}
                    @if ($resultados->isEmpty() && $dniBuscado)
                        <div class="empty-state p-8 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                No se encontraron resultados
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                No hay solicitudes asociadas al DNI <strong class="font-mono">{{ $dniBuscado }}</strong>
                            </p>
                            <a href="{{ route('resultados.buscar') }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-semibold">
                                <i class="fas fa-redo"></i>
                                Realizar nueva búsqueda
                            </a>
                        </div>
                    @elseif ($resultados->isEmpty() && !$dniBuscado)
                        <div class="empty-state p-8 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                                <i class="fas fa-search text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                Inicie una búsqueda
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Ingrese un DNI válido en el campo de búsqueda para consultar los resultados.
                            </p>
                        </div>
                    @else
                        {{-- TABLA DE RESULTADOS MEJORADA --}}
                        <div class="overflow-hidden shadow-xl rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                <i class="fas fa-building mr-2"></i>Empresa
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                <i class="fas fa-check-circle mr-2"></i>Estado
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                <i class="fas fa-calendar mr-2"></i>Fecha
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                <i class="fas fa-cog mr-2"></i>Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($resultados as $resultado)
                                            @php
                                                $isAprobado = $resultado->estado == 'aprobado';
                                                $badgeColor = $isAprobado 
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' 
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
                                                $iconClass = $isAprobado ? 'fa-check-circle' : 'fa-times-circle';
                                            @endphp
                                            <tr class="table-row">
                                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                                    <a href="{{ route('resultados.detalle', $resultado->empresa_id) }}" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150 group">
                                                        <i class="fas fa-building text-gray-400 group-hover:text-blue-500"></i>
                                                        <span>{{ $resultado->empresa->nombre_negocio ?? 'Empresa Desconocida' }}</span>
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="status-badge px-3 py-1 inline-flex items-center gap-1.5 text-xs leading-5 font-bold rounded-full {{ $badgeColor }} shadow-sm">
                                                        <i class="fas {{ $iconClass }}"></i>
                                                        {{ ucfirst($resultado->estado) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                    <div class="flex flex-col">
                                                        <span class="font-semibold">{{ $resultado->updated_at->format('d/M/Y') }}</span>
                                                        <span class="text-xs text-gray-500">{{ $resultado->updated_at->format('H:i') }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <a href="{{ route('resultados.detalle', $resultado->empresa_id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-all duration-200 shadow hover:shadow-lg transform hover:scale-105">
                                                        <i class="fas fa-eye"></i>
                                                        Ver Detalle
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- FOOTER DE LA TABLA --}}
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-600 dark:text-gray-400 text-center flex items-center justify-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i>
                                <span>Haga clic en "Ver Detalle" para obtener información completa de cada solicitud</span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- SCRIPT PARA FORMATEO AUTOMÁTICO DE DNI --}}
        <script>
            const dniInput = document.getElementById('dni');
            
            // Formatear automáticamente mientras el usuario escribe
            dniInput.addEventListener('input', function(e) {
                let value = e.target.value;
                
                // Remover todo excepto números
                value = value.replace(/[^0-9]/g, '');
                
                // Limitar a 13 dígitos
                value = value.substring(0, 13);
                
                // Aplicar formato: 0000-0000-00000
                let formatted = '';
                if (value.length > 0) {
                    formatted = value.substring(0, 4);
                    if (value.length >= 5) {
                        formatted += '-' + value.substring(4, 8);
                    }
                    if (value.length >= 9) {
                        formatted += '-' + value.substring(8, 13);
                    }
                }
                
                e.target.value = formatted;
            });
            
            // Al pegar, también formatear
            dniInput.addEventListener('paste', function(e) {
                setTimeout(() => {
                    let value = e.target.value.replace(/[^0-9]/g, '');
                    value = value.substring(0, 13);
                    
                    let formatted = '';
                    if (value.length > 0) {
                        formatted = value.substring(0, 4);
                        if (value.length >= 5) {
                            formatted += '-' + value.substring(4, 8);
                        }
                        if (value.length >= 9) {
                            formatted += '-' + value.substring(8, 13);
                        }
                    }
                    
                    e.target.value = formatted;
                }, 10);
            });
            
            // Validación antes de enviar el formulario
            dniInput.closest('form').addEventListener('submit', function(e) {
                const dniValue = dniInput.value.replace(/[^0-9]/g, '');
                if (dniValue.length !== 13) {
                    e.preventDefault();
                    alert('⚠️ El DNI debe contener exactamente 13 dígitos.');
                    dniInput.focus();
                }
            });
        </script>
    </body>
</html>