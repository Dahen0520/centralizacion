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
            <x-nav-link :href="route('empresas.index')" :active="request()->routeIs('empresas.*') && !request()->routeIs('afiliados.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400  transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-building mr-3"></i>
                {{ __('Empresas') }}
            </x-nav-link>

            {{-- Afiliados --}}
            <x-nav-link :href="route('afiliados.list')" :active="request()->routeIs('afiliados.*') && !request()->routeIs('afiliados.registro')"
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

            {{-- Menú de Subcategorías --}}
            <x-nav-link :href="route('subcategorias.index')" :active="request()->routeIs('subcategorias.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-layer-group mr-3"></i>
                {{ __('Subcategorías') }}
            </x-nav-link>
            
            {{-- NUEVA RUTA: Clientes --}}
            <x-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center font-bold">
                <i class="fas fa-address-book mr-3"></i>
                {{ __('Clientes') }}
            </x-nav-link>

            {{-- Menú de Productos --}}
            <x-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-boxes mr-3"></i>
                {{ __('Productos') }}
            </x-nav-link>

            {{-- Menú de Marcas --}}
            <x-nav-link :href="route('marcas.index')" :active="request()->routeIs('marcas.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-barcode mr-3"></i>
                {{ __('Marcas') }}
            </x-nav-link>

            {{-- NUEVO: Menú de Impuestos --}}
            <x-nav-link :href="route('impuestos.index')" :active="request()->routeIs('impuestos.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center">
                <i class="fas fa-percent mr-3"></i>
                {{ __('Impuestos') }}
            </x-nav-link>
            
            {{-- GESTIÓN DE RANGOS CAI --}}
            <x-nav-link :href="route('rangos-cai.index')" :active="request()->routeIs('rangos-cai.*')"
                        class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center font-bold">
                <i class="fas fa-file-invoice-dollar mr-3"></i>
                {{ __('Rangos CAI (Fiscal)') }}
            </x-nav-link>
            
            {{-- ========================================================= --}}
            {{-- MÓDULO DE INVENTARIO Y VENTAS --}}
            {{-- ========================================================= --}}
            <div class="{{ request()->routeIs('inventarios.*') || request()->routeIs('ventas.*') || request()->routeIs('movimientos.*') || request()->routeIs('reportes.cierre_caja.*') || request()->routeIs('reportes.resumen.*') ? 'bg-chorotega-blue-light rounded' : '' }} p-1 space-y-1">
                
                {{-- ⭐ CORRECCIÓN: Apunta a ventas.pos --}}
                <x-nav-link :href="route('ventas.pos')" :active="request()->routeIs('ventas.pos')" 
                            class="py-2 px-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center font-bold">
                    <i class="fas fa-cash-register mr-3"></i>
                    {{ __('Punto de Venta (POS)') }}
                </x-nav-link>

                {{-- Historial de Ventas --}}
                @if (Route::has('ventas.index'))
                <x-nav-link :href="route('ventas.index')" :active="request()->routeIs('ventas.index')"
                            class="py-1 pl-8 pr-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center text-sm">
                    <i class="fas fa-history mr-3 opacity-80"></i>
                    {{ __('Historial de Ventas') }}
                </x-nav-link>
                @endif

                {{-- 🆕 REPORTE DE CIERRE DE CAJA --}}
                <x-nav-link :href="route('reportes.cierre_caja.form')" :active="request()->routeIs('reportes.cierre_caja.*')"
                            class="py-1 pl-8 pr-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center text-sm font-bold">
                    <i class="fas fa-file-alt mr-3 opacity-80"></i>
                    {{ __('Reporte de Cierre de Caja') }}
                </x-nav-link>
                
                {{-- 🆕 RESUMEN DE INGRESOS POR AFILIADOS --}}
                <x-nav-link :href="route('reportes.resumen.afiliados')" :active="request()->routeIs('reportes.resumen.afiliados')"
                            class="py-1 pl-8 pr-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center text-sm">
                    <i class="fas fa-chart-bar mr-3 opacity-80"></i>
                    {{ __('Resumen Ingresos Afiliados') }}
                </x-nav-link>
                
                {{-- Enlace principal (Gestión/CRUD de Inventario) --}}
                <x-nav-link :href="route('inventarios.index')" :active="request()->routeIs('inventarios.index')"
                            class="py-1 pl-8 pr-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center text-sm">
                    <i class="fas fa-list-alt mr-3 opacity-80"></i>
                    {{ __('Gestión de Inventario') }}
                </x-nav-link>
                
                {{-- Historial de Movimientos (Ajustes, Entradas, Descarte) --}}
                <x-nav-link :href="route('movimientos.index')" :active="request()->routeIs('movimientos.index')"
                            class="py-1 pl-8 pr-4 rounded hover:bg-chorotega-blue-light hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center text-sm">
                    <i class="fas fa-clipboard-list mr-3 opacity-80"></i>
                    {{ __('Movimientos/Trazabilidad') }}
                </x-nav-link>
                
                {{-- Explorador Jerárquico --}}
                <x-nav-link :href="route('inventarios.explorar.tiendas')" :active="request()->routeIs('inventarios.explorar.*')"
                            class="py-1 pl-8 pr-4 rounded hover:bg-chorotega-blue-light-hover hover:text-yellow-400 transition duration-200 ease-in-out text-white flex items-center text-sm">
                    <i class="fas fa-search-location mr-3 opacity-80"></i>
                    {{ __('Explorar Inventario') }}
                </x-nav-link>
            </div>

            {{-- Tiendas --}}
            <x-nav-link :href="route('tiendas.index')" :active="request()->routeIs('tiendas.index')"
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