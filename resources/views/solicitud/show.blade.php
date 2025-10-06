<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Revisión de Solicitud:') }} <span class="font-extrabold text-blue-600 dark:text-blue-400">{{ $empresa->nombre_negocio }}</span>
        </h2>
    </x-slot>

    {{-- 1. INCLUSIÓN DE SWEETALERT2 VIA CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

    @php
        $estado = $empresa->estado;
        $color = $estado == 'aprobado' ? 'green' : ($estado == 'rechazado' ? 'red' : 'yellow');
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <a href="{{ route('solicitud.index', ['estado' => $estado]) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 mb-4 inline-flex items-center text-sm font-semibold transition duration-150">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver a la cola de {{ ucfirst($estado) }}
            </a>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mt-4">
                
                {{-- PANEL DE ACCIONES Y ESTADO --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0 p-4 rounded-lg border-l-8 border-{{ $color }}-600 bg-{{ $color }}-50 dark:bg-gray-700/50 mb-8 shadow-md">
                    
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Estado Actual de la Solicitud</p>
                        <h4 class="text-2xl font-extrabold text-{{ $color }}-700 dark:text-{{ $color }}-400 uppercase">
                            {{ $estado }}
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Solicitado el {{ $empresa->created_at->format('d/M/Y') }}</p>
                    </div>
                    
                    <div class="flex space-x-3">
                        @if ($estado != 'aprobado')
                            {{-- Modificado: Llamado a la función JS que abre el modal de comentario --}}
                            <form id="approve-form" method="POST" action="{{ route('solicitud.aprobar', $empresa) }}">
                                @csrf
                                <button type="button" onclick="confirmAction('approve')" 
                                        class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 shadow-md disabled:opacity-50"
                                        @if ($estado == 'aprobado') disabled @endif>
                                    <i class="fas fa-check-circle mr-2"></i> Aprobar y Comentar
                                </button>
                            </form>
                        @endif

                        @if ($estado != 'rechazado')
                            {{-- Modificado: Llamado a la función JS que abre el modal de comentario --}}
                            <form id="reject-form" method="POST" action="{{ route('solicitud.rechazar', $empresa) }}">
                                @csrf
                                <button type="button" onclick="confirmAction('reject')" 
                                        class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 shadow-md disabled:opacity-50"
                                        @if ($estado == 'rechazado') disabled @endif>
                                    <i class="fas fa-times-circle mr-2"></i> Rechazar y Comentar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- TABS/PESTAÑAS DE CONTENIDO --}}
                <div x-data="{ activeTab: 'general' }">
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button @click="activeTab = 'general'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'general', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'general' }"
                                    class="py-3 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out">
                                <i class="fas fa-info-circle mr-2"></i> Datos Generales
                            </button>
                            <button @click="activeTab = 'productos'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'productos', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'productos' }"
                                    class="py-3 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out">
                                <i class="fas fa-boxes mr-2"></i> Productos ({{ $empresa->productos->count() }})
                            </button>
                            <button @click="activeTab = 'tiendas'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'tiendas', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'tiendas' }"
                                    class="py-3 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out">
                                <i class="fas fa-store mr-2"></i> Tiendas ({{ $empresa->tiendas->count() }})
                            </button>
                        </nav>
                    </div>

                    {{-- CONTENIDO DE LA PESTAÑA: DATOS GENERALES --}}
                    <div x-show="activeTab === 'general'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- BLOQUE 1: DATOS DE LA EMPRESA --}}
                        <div class="p-5 border border-blue-100 dark:border-gray-700 rounded-lg bg-blue-50/50 dark:bg-gray-800/50 shadow-inner">
                            <h3 class="text-xl font-bold mb-4 text-blue-600 dark:text-blue-400 flex items-center">
                                <i class="fas fa-building mr-2"></i> Detalles del Negocio
                            </h3>
                            <p class="mb-2"><strong>Rubro:</strong> {{ $empresa->rubro->nombre ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>Tipo Org.:</strong> {{ $empresa->tipoOrganizacion->nombre ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>Dirección:</strong> {{ $empresa->direccion }}</p>
                            <p class="mb-2"><strong>País Exporta:</strong> {{ $empresa->paisExportacion->nombre ?? 'No aplica' }}</p>
                        </div>

                        {{-- BLOQUE 2: DATOS DEL AFILIADO --}}
                        <div class="p-5 border border-yellow-100 dark:border-gray-700 rounded-lg bg-yellow-50/50 dark:bg-gray-800/50 shadow-inner">
                            <h3 class="text-xl font-bold mb-4 text-yellow-600 dark:text-yellow-400 flex items-center">
                                <i class="fas fa-user-tag mr-2"></i> Afiliado Registrante
                            </h3>
                            <p class="mb-2"><strong>Nombre:</strong> {{ $empresa->afiliado->nombre ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>DNI:</strong> {{ $empresa->afiliado->dni ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>Teléfono:</strong> {{ $empresa->afiliado->telefono ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>Email:</strong> {{ $empresa->afiliado->email ?? 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- CONTENIDO DE LA PESTAÑA: PRODUCTOS/MARCAS --}}
                    <div x-show="activeTab === 'productos'">
                        <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Listado de Productos/Marcas</h3>
                        @forelse ($empresa->productos as $producto)
                            @php
                                $prodColor = $producto->pivot->estado == 'aprobado' ? 'green' : ($producto->pivot->estado == 'rechazado' ? 'red' : 'yellow');
                                // Aseguramos el acceso a la Categoría
                                $categoriaNombre = $producto->subcategoria->categoria->nombre ?? 'N/A';
                            @endphp
                            <div class="p-4 border rounded-lg mb-3 bg-gray-50 dark:bg-gray-700 flex justify-between items-start shadow-sm border-l-4 border-{{ $prodColor }}-500">
                                <div class="w-full">
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="font-bold text-lg text-gray-900 dark:text-white">{{ $producto->nombre }}</p>
                                        <span class="text-sm uppercase font-semibold px-3 py-1 rounded-full bg-{{ $prodColor }}-100 text-{{ $prodColor }}-800 dark:bg-{{ $prodColor }}-900/50 dark:text-{{ $prodColor }}-300">
                                            ESTADO MARCA: {{ $producto->pivot->estado }}
                                        </span>
                                    </div>
                                    
                                    {{-- NUEVOS DETALLES DEL PRODUCTO --}}
                                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1 mt-2 p-3 bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-600">
                                        <p class="font-semibold">Descripción:</p>
                                        <p class="text-xs italic">{{ $producto->descripcion ?? 'No se proporcionó descripción.' }}</p>
                                        
                                        <div class="grid grid-cols-2 gap-4 mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <p>
                                                <strong class="text-gray-700 dark:text-gray-300">Categoría:</strong> 
                                                <span class="font-medium text-purple-600 dark:text-purple-400">{{ $categoriaNombre }}</span>
                                            </p>
                                            <p>
                                                <strong class="text-gray-700 dark:text-gray-300">Subcategoría:</strong> 
                                                <span class="font-medium text-blue-600 dark:text-blue-400">{{ $producto->subcategoria->nombre ?? 'N/A' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 p-4 border rounded-lg italic">Esta empresa no ha registrado productos.</p>
                        @endforelse
                    </div>

                    {{-- CONTENIDO DE LA PESTAÑA: TIENDAS --}}
                    <div x-show="activeTab === 'tiendas'">
                         <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Puntos de Venta Solicitados</h3>
                        @forelse ($empresa->tiendas as $tienda)
                            @php
                                $tiendaColor = $tienda->pivot->estado == 'aprobado' ? 'green' : ($tienda->pivot->estado == 'rechazado' ? 'red' : 'yellow');
                            @endphp
                            <div class="p-4 border rounded-lg mb-3 bg-gray-50 dark:bg-gray-700 flex justify-between items-center shadow-sm border-l-4 border-{{ $tiendaColor }}-500">
                                <div>
                                    <p class="font-bold text-lg text-gray-900 dark:text-white">{{ $tienda->nombre }}</p>
                                </div>
                                <span class="text-sm uppercase font-semibold px-3 py-1 rounded-full bg-{{ $tiendaColor }}-100 text-{{ $tiendaColor }}-800 dark:bg-{{ $tiendaColor }}-900/50 dark:text-{{ $tiendaColor }}-300">
                                    ESTADO TIENDA: {{ $tienda->pivot->estado }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 p-4 border rounded-lg italic">Esta empresa no ha solicitado participación en tiendas.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. SCRIPT DE SWEETALERT2 PARA CONFIRMACIONES Y NOTIFICACIONES --}}
    <script>
        // Función para manejar la confirmación de Aprobar/Rechazar con formulario de Comentario
        function confirmAction(type) {
            const isApprove = type === 'approve';
            const title = isApprove ? 'Registrar Aprobación' : 'Registrar Rechazo';
            const htmlText = `
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    Ingrese el comentario del Resultado (obligatorio) para auditar esta decisión.
                </p>
                <textarea id="swal-input-comentario" class="swal2-input !h-24" placeholder="Escriba aquí el comentario del resultado..."></textarea>
            `;
            const icon = isApprove ? 'question' : 'warning';
            const confirmButtonColor = isApprove ? '#10B981' : '#EF4444'; // green-600 o red-600
            const confirmButtonText = isApprove ? 'Sí, ¡Aprobar y Registrar!' : 'Sí, ¡Rechazar y Registrar!';
            const formId = isApprove ? 'approve-form' : 'reject-form';

            Swal.fire({
                title: title,
                html: htmlText,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                cancelButtonColor: '#6B7280', // gray-500
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Cancelar',
                focusConfirm: false,
                preConfirm: () => {
                    const comentario = document.getElementById('swal-input-comentario').value;
                    if (!comentario || comentario.trim() === '') {
                        Swal.showValidationMessage('El campo de comentario es obligatorio.');
                        return false;
                    }
                    return comentario;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const comentario = result.value;
                    const form = document.getElementById(formId);
                    
                    // 1. Crear campo de comentario oculto
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'comentario';
                    input.value = comentario;
                    
                    // 2. Añadir al formulario y enviarlo
                    form.appendChild(input);
                    form.submit();
                }
            });
        }

        // Script para mostrar notificaciones de sesión (success, warning, error)
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