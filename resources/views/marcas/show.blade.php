<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalles de la Marca') }}
        </h2>
    </x-slot>

    <div class="w-full max-w-6xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border-t-8 border-blue-600 mx-auto mt-12">
        <div class="text-center mb-8">
            <p class="text-gray-600 dark:text-gray-400 text-base mb-4">Información de la marca</p>
            <a href="{{ route('marcas.index') }}" class="text-base text-blue-600 dark:text-blue-400 hover:underline font-medium block">
                <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Marcas
            </a>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 shadow-sm">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">Código de Marca: {{ $marca->codigo_marca }}</h3>
            <div class="text-gray-600 dark:text-gray-400 space-y-2">
                <p><strong>ID:</strong> {{ $marca->id }}</p>
                <p><strong>Producto:</strong> {{ $marca->producto->nombre }}</p>
                <p><strong>Empresa:</strong> {{ $marca->empresa->nombre }}</p>
                <p><strong>Estado:</strong>
                    @php
                        $colorClass = '';
                        switch ($marca->estado) {
                            case 'pendiente':
                                $colorClass = 'bg-yellow-100 text-yellow-800';
                                break;
                            case 'aprobado':
                                $colorClass = 'bg-green-100 text-green-800';
                                break;
                            case 'rechazado':
                                $colorClass = 'bg-red-100 text-red-800';
                                break;
                        }
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }} dark:bg-gray-700 dark:text-white">
                        {{ ucfirst($marca->estado) }}
                    </span>
                </p>
                <p><strong>Creado en:</strong> {{ $marca->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Actualizado en:</strong> {{ $marca->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</x-app-layout>