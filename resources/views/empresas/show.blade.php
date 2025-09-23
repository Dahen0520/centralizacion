<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Detalles de Empresa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8 border-t-8 border-blue-600">
                <div class="text-center mb-8">
                    <p class="text-gray-600 dark:text-gray-400 text-base mb-4">Información detallada de la empresa</p>
                    <a href="{{ route('empresas.index') }}" class="text-base text-blue-600 dark:text-blue-400 hover:underline font-medium block">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Empresas
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700 dark:text-gray-300">
                    <div>
                        <p class="font-bold">Nombre del Negocio:</p>
                        <p class="text-lg text-gray-900 dark:text-gray-100">{{ $empresa->nombre_negocio }}</p>
                    </div>
                    <div>
                        <p class="font-bold">Dirección:</p>
                        <p class="text-lg text-gray-900 dark:text-gray-100">{{ $empresa->direccion }}</p>
                    </div>
                    <div>
                        <p class="font-bold">Afiliado:</p>
                        <p class="text-lg text-gray-900 dark:text-gray-100">{{ $empresa->afiliado->nombre }}</p>
                    </div>
                    <div>
                        <p class="font-bold">Rubro:</p>
                        <p class="text-lg text-gray-900 dark:text-gray-100">{{ $empresa->rubro->nombre }}</p>
                    </div>
                    <div>
                        <p class="font-bold">Tipo de Organización:</p>
                        <p class="text-lg text-gray-900 dark:text-gray-100">{{ $empresa->tipoOrganizacion->nombre }}</p>
                    </div>
                    <div>
                        <p class="font-bold">País de Exportación:</p>
                        <p class="text-lg text-gray-900 dark:text-gray-100">{{ $empresa->paisExportacion ? $empresa->paisExportacion->nombre : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="font-bold">Estado:</p>
                        <p class="text-lg text-gray-900 dark:text-gray-100">{{ $empresa->estado }}</p>
                    </div>
                </div>

                <hr class="my-8 border-gray-200 dark:border-gray-700">
                
                <div class="flex justify-center mt-6">
                    <a href="{{ route('empresas.edit', $empresa->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-edit mr-2"></i> Editar Empresa
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>