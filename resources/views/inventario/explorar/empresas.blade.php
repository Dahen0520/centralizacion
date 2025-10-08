<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Inventario en ') }} {{ $tienda->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('inventarios.explorar.tiendas') }}" class="mb-4 inline-block text-emerald-600 dark:text-emerald-400 hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Volver a Tiendas
            </a>

            <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-6">2. Selecciona una Empresa</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($empresas as $empresa)
                    <a href="{{ route('inventarios.explorar.inventario', ['empresa' => $empresa->id, 'tienda' => $tienda->id]) }}"
                       class="block bg-white dark:bg-gray-800 rounded-xl shadow-lg 
                              hover:shadow-xl hover:ring-4 hover:ring-indigo-500/50 
                              transition duration-300 ease-in-out transform hover:-translate-y-1">
                        <div class="p-6">
                            <i class="fas fa-building text-3xl text-indigo-600 dark:text-indigo-400 mb-3"></i>
                            <h4 class="text-xl font-extrabold text-gray-900 dark:text-white mb-1">{{ $empresa->nombre_negocio }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Ver inventario de productos de esta empresa en {{ $tienda->nombre }}.
                            </p>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 col-span-full">No hay empresas asociadas a esta tienda.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>