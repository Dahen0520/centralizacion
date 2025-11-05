<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Administración de Rangos CAI (SAR)') }}
        </h2>
    </x-slot>
    
    {{-- INCLUSIÓN DE SWEETALERT2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                {{-- Botón de Creación --}}
                <div class="flex justify-end mb-6">
                    <a href="{{ route('rangos-cai.create') }}" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition duration-150 shadow-md flex items-center">
                        <i class="fas fa-plus-circle mr-2"></i> Nuevo Rango CAI
                    </a>
                </div>

                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                         class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900 dark:border-green-600 dark:text-green-300 shadow-lg">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                         class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg dark:bg-red-900 dark:border-red-600 dark:text-red-300 shadow-lg">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- TABLA DE RANGOS CAI --}}
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Tienda
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    CAI / Rango
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Núm. Actual
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Fecha Límite
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($rangos as $rango)
                                @php
                                    // 🌟 CRÍTICO: Reconstruir la cadena completa para la visualización
                                    $ceroPad = 8;
                                    $rangoInicialFull = $rango->prefijo_sar . str_pad($rango->rango_inicial, $ceroPad, '0', STR_PAD_LEFT);
                                    $rangoFinalFull = $rango->prefijo_sar . str_pad($rango->rango_final, $ceroPad, '0', STR_PAD_LEFT);
                                    $numeroActualFull = $rango->prefijo_sar . str_pad($rango->numero_actual, $ceroPad, '0', STR_PAD_LEFT);

                                    $fechaLimite = \Carbon\Carbon::parse($rango->fecha_limite_emision);
                                    $isExpired = $fechaLimite->isPast();
                                    $isNear = $fechaLimite->diffInDays(now()) < 30 && !$isExpired;
                                    $dateClass = $isExpired ? 'text-red-600 font-bold' : ($isNear ? 'text-yellow-600 font-semibold' : 'text-gray-700 dark:text-gray-300');
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $rango->tienda->nombre ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700 dark:text-gray-300">
                                        <p class="font-semibold text-blue-600 dark:text-blue-400 mb-1">CAI: {{ $rango->cai }}</p>
                                        {{-- Mostrar el rango completo reconstruido --}}
                                        <p class="text-xs text-gray-500 dark:text-gray-400" title="Prefijo: {{ $rango->prefijo_sar }}">
                                            Rango: **{{ $rangoInicialFull }}** a **{{ $rangoFinalFull }}**
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-teal-600 dark:text-teal-400">
                                        {{-- Mostrar el número actual completo reconstruido --}}
                                        <span title="Secuencia: {{ $rango->numero_actual }}">
                                            {{ $numeroActualFull }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <span class="{{ $dateClass }}">
                                            {{ $fechaLimite->format('d/M/Y') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($isExpired)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-300">
                                                EXPIRADO
                                            </span>
                                        @elseif ($rango->esta_activo)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-300">
                                                ACTIVO
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                INACTIVO
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="{{ route('rangos-cai.edit', $rango) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 transition-colors mr-3" title="Editar Estado/Fecha">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('rangos-cai.destroy', $rango) }}" method="POST" class="inline delete-form" onsubmit="return false;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" data-name="{{ $rango->cai }}" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 italic">
                                        No hay rangos CAI registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="mt-4">
                    {{ $rangos->links() }}
                </div>
            </div>
        </div>
    </div>
    
    {{-- LÓGICA DE ELIMINACIÓN CON SWEETALERT2 --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    const cai = this.getAttribute('data-name');

                    Swal.fire({
                        title: '¿Eliminar Rango CAI?',
                        text: `Está a punto de eliminar el rango con CAI: ${cai}. ¡Esto es irreversible!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#EF4444', 
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Sí, ¡Eliminar!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>