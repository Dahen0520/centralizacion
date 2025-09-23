<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Detalles de la Categoría') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8 border-t-8 border-blue-600">
                <div class="flex justify-between items-center mb-6">
                    <a href="{{ route('categorias.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista
                    </a>
                </div>

                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                    <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-gray-100">Información de la Categoría</h3>
                </div>

                <div class="mt-5 border-t border-gray-200 dark:border-gray-700">
                    <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:col-span-2 sm:mt-0">{{ $categoria->id }}</dd>
                        </div>
                        <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:col-span-2 sm:mt-0">{{ $categoria->nombre }}</dd>
                        </div>
                        <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Creado en</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:col-span-2 sm:mt-0">{{ $categoria->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Actualizado en</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:col-span-2 sm:mt-0">{{ $categoria->updated_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>