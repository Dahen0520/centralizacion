<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Detalles del Afiliado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8 border-t-8 border-blue-600">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ $afiliado->nombre }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">DNI: {{ $afiliado->dni }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700 dark:text-gray-200">
                    <div>
                        <p class="font-bold">Género:</p>
                        <p>{{ $afiliado->genero ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="font-bold">Fecha de Nacimiento:</p>
                        <p>{{ $afiliado->fecha_nacimiento ? \Carbon\Carbon::parse($afiliado->fecha_nacimiento)->format('d/m/Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="font-bold">Email:</p>
                        <p>{{ $afiliado->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="font-bold">Teléfono:</p>
                        <p>{{ $afiliado->telefono ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="font-bold">Municipio:</p>
                        <p>{{ $afiliado->municipio->nombre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="font-bold">Barrio:</p>
                        <p>{{ $afiliado->barrio ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="font-bold">RTN:</p>
                        <p>{{ $afiliado->rtn ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="font-bold">Número de Cuenta:</p>
                        <p>{{ $afiliado->numero_cuenta ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="mt-8">
                    <a href="{{ route('afiliados.list') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-md font-semibold shadow-md hover:bg-blue-700 transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
