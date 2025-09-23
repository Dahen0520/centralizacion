<header class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
    <div class="text-lg font-semibold text-chorotega-blue">
        {{ config('app.name', 'Reservas Chorotega') }}
    </div>

    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open"
                class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none">
            <img class="h-8 w-8 rounded-full object-cover mr-2"
                 src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2c3991&color=fff"
                 alt="Foto de perfil">
            <span>{{ Auth::user()->name }}</span>
            <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                      d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.08 1.04l-4.25 4.25a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                      clip-rule="evenodd" />
            </svg>
        </button>

        <div x-show="open" @click.away="open = false" x-transition x-cloak
             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
            <a href="{{ route('profile.edit') }}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Perfil</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</header>