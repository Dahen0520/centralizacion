<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Detalles del Tipo de Organización') }}
        </h2>
    </x-slot>

    <div class="w-full max-w-6xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border-t-8 border-blue-600 mx-auto mt-12">
        <div class="text-center mb-8">
            <p class="text-gray-600 dark:text-gray-400 text-base mb-4">Información del tipo de organización</p>
            <a href="{{ route('tipo-organizacions.index') }}" class="text-base text-blue-600 dark:text-blue-400 hover:underline font-medium block">
                <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista
            </a>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 shadow-sm">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $tipoOrganizacion->nombre }}</h3>
            <div class="text-gray-600 dark:text-gray-400 space-y-2">
                <p><strong>ID:</strong> {{ $tipoOrganizacion->id }}</p>
                <p><strong>Nombre:</strong> {{ $tipoOrganizacion->nombre }}</p>
                <p><strong>Creado en:</strong> {{ $tipoOrganizacion->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Actualizado en:</strong> {{ $tipoOrganizacion->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</x-app-layout>