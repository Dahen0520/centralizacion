<aside class="bg-chorotega-blue text-white w-56 min-h-screen fixed top-0 left-0 flex flex-col z-50 shadow-md">
    <div>
        <div class="flex justify-center mt-0 mb-2">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('assets/imgs/pro_horizontalblanco.png') }}" alt="Logo de Cooperativa Chorotega" class="h-11 mt-4 mb-2">
            </a>
        </div>
        <nav class="flex flex-col px-4 space-y-2">
            {{-- Inicio --}}
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-home mr-3"></i>
                {{ __('Inicio') }}
            </x-nav-link>

            {{-- Rubros --}}
            <x-nav-link :href="route('rubros.index')" :active="request()->routeIs('rubros.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400  transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-tag mr-3"></i>
                {{ __('Rubros') }}
            </x-nav-link>

            {{-- Tipos de Organización --}}
            <x-nav-link :href="route('tipo-organizacions.index')" :active="request()->routeIs('tipo-organizacions.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400  transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-sitemap mr-3"></i>
                {{ __('Tipos de Organización') }}
            </x-nav-link>

            {{-- Empresas --}}
            <x-nav-link :href="route('empresas.index')" :active="request()->routeIs('empresas.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400  transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-building mr-3"></i>
                {{ __('Empresas') }}
            </x-nav-link>

            {{-- Afiliados --}}
            <x-nav-link :href="route('afiliados.list')" :active="request()->routeIs('afiliados.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400  transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-users mr-3"></i>
                {{ __('Afiliados') }}
            </x-nav-link>

            {{-- ========================================================= --}}
            {{-- SOLICITUDES DE EMPRESAS --}}
            {{-- ========================================================= --}}
            <x-nav-link :href="route('solicitud.index')" :active="request()->routeIs('solicitud.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center font-bold bg-chorotega-blue-light/50">
                <i class="fas fa-clipboard-check mr-3"></i>
                {{ __('Solicitudes') }}
            </x-nav-link>

            {{-- Menú de Categorías --}}
            <x-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400  transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-tags mr-3"></i>
                {{ __('Categorías') }}
            </x-nav-link>

            {{-- Menú de Subcategorías (opción añadida) --}}
            <x-nav-link :href="route('subcategorias.index')" :active="request()->routeIs('subcategorias.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-layer-group mr-3"></i>
                {{ __('Subcategorías') }}
            </x-nav-link>

            {{-- Menú de Productos (opción añadida) --}}
            <x-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-boxes mr-3"></i>
                {{ __('Productos') }}
            </x-nav-link>

            {{-- Menú de Marcas (opción añadida) --}}
            <x-nav-link :href="route('marcas.index')" :active="request()->routeIs('marcas.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-barcode mr-3"></i>
                {{ __('Marcas') }}
            </x-nav-link>
            
            {{-- ========================================================= --}}
            {{-- MÓDULO DE INVENTARIO Y VENTAS --}}
            {{-- ========================================================= --}}
            <div class="{{ request()->routeIs('inventarios.*') || request()->routeIs('ventas.*') ? 'bg-chorotega-blue-light rounded' : '' }}">
                
                {{-- NUEVO: PUNTO DE VENTA (POS) --}}
                <x-nav-link :href="route('ventas.pos')" :active="request()->routeIs('ventas.pos')"
                            class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center font-bold">
                    <i class="fas fa-cash-register mr-3"></i>
                    {{ __('Punto de Venta (POS)') }}
                </x-nav-link>

                {{-- Historial de Ventas (opcional, si implementas la vista index) --}}
                @if (Route::has('ventas.index'))
                <x-nav-link :href="route('ventas.index')" :active="request()->routeIs('ventas.index')"
                            class="py-1 pl-8 pr-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center text-sm">
                    <i class="fas fa-history mr-3 opacity-80"></i>
                    {{ __('Historial de Ventas') }}
                </x-nav-link>
                @endif
                
                {{-- Enlace principal (Gestión/CRUD) --}}
                <x-nav-link :href="route('inventarios.index')" :active="request()->routeIs('inventarios.index')"
                            class="py-1 pl-8 pr-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center text-sm">
                    <i class="fas fa-list-alt mr-3 opacity-80"></i>
                    {{ __('Gestión de Inventario') }}
                </x-nav-link>

                {{-- Explorador Jerárquico --}}
                <x-nav-link :href="route('inventarios.explorar.tiendas')" :active="request()->routeIs('inventarios.explorar.*')"
                            class="py-1 pl-8 pr-4 rounded hover:bg-chorotega-blue-light-hover hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center text-sm">
                    <i class="fas fa-search-location mr-3 opacity-80"></i>
                    {{ __('Explorar Inventario') }}
                </x-nav-link>
            </div>

            {{-- Tiendas --}}
            <x-nav-link :href="route('tiendas.index')" :active="request()->routeIs('tiendas.*') && !request()->routeIs('inventarios.explorar.empresas')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-white transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-store mr-3"></i>
                {{ __('Tiendas') }}
            </x-nav-link>

            {{-- **NUEVA RUTA DE ASOCIACIONES** --}}
            <x-nav-link :href="route('asociaciones.index')" :active="request()->routeIs('asociaciones.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-white transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-link mr-3"></i>
                {{ __('Vinculaciones') }}
            </x-nav-link>
        </nav>
    </div>
</aside>