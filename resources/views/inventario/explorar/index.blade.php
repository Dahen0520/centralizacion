<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Explorador de Inventario por Tienda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-6">1. Selecciona una Tienda</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($tiendas as $tienda)
                    <a href="{{ route('inventarios.explorar.empresas', $tienda) }}"
                       class="block bg-white dark:bg-gray-800 rounded-xl shadow-lg 
                              hover:shadow-xl hover:ring-4 hover:ring-emerald-500/50 
                              transition duration-300 ease-in-out transform hover:-translate-y-1">
                        <div class="p-6">
                            <i class="fas fa-store text-3xl text-emerald-600 dark:text-emerald-400 mb-3"></i>
                            <h4 class="text-xl font-extrabold text-gray-900 dark:text-white mb-1">{{ $tienda->nombre }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Ver empresas asociadas y su inventario en este punto de venta.
                            </p>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 col-span-full">No hay tiendas registradas para explorar.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>