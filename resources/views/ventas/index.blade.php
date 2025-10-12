<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Historial de Ventas Registradas') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-8 lg:p-10 
                        border-t-4 border-emerald-600 dark:border-emerald-500">
                
                {{-- HEADER Y BOTONES --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    
                    {{-- Botón POS --}}
                    <a href="{{ route('ventas.pos') }}" 
                       class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3 
                              bg-gradient-to-r from-blue-600 to-indigo-700 text-white 
                              font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg 
                              hover:shadow-xl hover:from-blue-700 hover:to-indigo-800 
                              focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-cash-register mr-2 text-lg"></i> Ir a Punto de Venta
                    </a>

                    {{-- Barra de búsqueda (Opcional, se puede implementar con AJAX) --}}
                    {{-- <div class="relative flex-1 min-w-[300px]">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
                        <input type="text" id="search-input" placeholder="Buscar por ID, Tienda o Total..."
                               class="pl-12 py-3 border border-gray-300 dark:border-gray-600 rounded-xl w-full text-gray-900 dark:text-gray-100 dark:bg-gray-700 placeholder-gray-500 focus:ring-emerald-500 focus:border-emerald-500 transition duration-150 shadow-md">
                    </div> --}}
                </div>

                {{-- CONTENEDOR DE LA TABLA --}}
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-16">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-calendar-alt mr-1"></i> Fecha/Hora
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-user mr-1"></i> Cliente </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-store mr-1"></i> Tienda
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-user-tag mr-1"></i> Registrado Por
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-money-bill-wave mr-1"></i> Total (L)
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-32">
                                    <i class="fas fa-cogs mr-1"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($ventas as $venta)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900 dark:text-white">
                                    #{{ $venta->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    {{ $venta->fecha_venta->format('d/M/Y H:i A') }}
                                </td>
                                {{-- DATOS DEL CLIENTE (NUEVA CELDA) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-100">
                                    @if ($venta->cliente)
                                        {{ $venta->cliente->nombre }}
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ $venta->cliente->identificacion ?? 'N/A' }}</span>
                                    @else
                                        <span class="text-xs text-gray-500 dark:text-gray-400 italic">Cliente Genérico</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                    {{ $venta->tienda->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $venta->usuario->name ?? 'Sistema' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-lg font-extrabold text-indigo-600 dark:text-indigo-400">
                                    L {{ number_format($venta->total_venta, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                    {{-- Botón para ver detalles de la venta --}}
                                    <a href="{{ route('ventas.show', $venta) }}" 
                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 transition-colors" 
                                       title="Ver Detalles">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    
                                    {{-- Botón para anular la venta (Debe usar lógica DELETE/AJAX con SweetAlert) --}}
                                    <form action="{{ route('ventas.destroy', $venta) }}" method="POST" class="inline delete-form" onsubmit="return false;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" 
                                                data-name="Venta #{{ $venta->id }}">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                {{-- Colspan ajustado a 7 --}}
                                <td colspan="7" class="p-10 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-clipboard-list text-5xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                    <p class="font-extrabold text-xl">No se encontraron registros de ventas.</p>
                                    <p>Comience a usar el Punto de Venta para ver el historial.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="mt-8">
                    {{ $ventas->links() }}
                </div>
            </div>
        </div>
    </div>
    
    {{-- Scripts de SweetAlert2 y AJAX para Eliminar (Anular) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Lógica de SweetAlert para mensajes de sesión
            const successMessage = '{{ session('success') }}';
            if (successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación Exitosa!',
                    text: successMessage,
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top-end'
                });
            }

            // Lógica para Anular (Eliminar) Venta
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const form = this.closest('form');
                    const itemName = this.getAttribute('data-name') || 'esta venta';

                    Swal.fire({
                        title: '¿Anular ' + itemName + '?',
                        text: '¡Esta acción es irreversible y requiere ajuste de inventario manual!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#EF4444', 
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Sí, ¡Anular Venta!',
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