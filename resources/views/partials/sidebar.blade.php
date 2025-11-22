<aside x-data class="bg-chorotega-blue text-white w-56 min-h-screen fixed top-0 left-0 flex flex-col z-50 shadow-2xl overflow-y-auto">
    
    {{-- LOGO FIJO --}}
    <div class="flex justify-center mt-0 mb-2 sticky top-0 bg-chorotega-blue z-10 pb-2">
        <a href="{{ route('dashboard') }}" class="transition-transform duration-300 hover:scale-105">
            <img src="{{ asset('assets/imgs/pro_horizontalblanco.png') }}" alt="Logo de Cooperativa Chorotega" class="h-11 mt-4 mb-2">
        </a>
    </div>

    {{-- CONTENEDOR DE NAVEGACIÓN --}}
    <nav class="flex flex-col px-4 space-y-2 pb-6">

        {{-- 1. INICIO --}}
        <a href="{{ route('dashboard') }}"
           class="group py-2.5 px-4 rounded-lg transition-all duration-200 ease-in-out flex items-center font-semibold shadow-sm hover:shadow-md hover:translate-x-1 {{ request()->routeIs('dashboard') ? 'text-yellow-400' : 'text-white hover:bg-chorotega-blue-light' }}">
            <i class="fas fa-home mr-3 group-hover:scale-110 transition-transform duration-200"></i>
            {{ __('Inicio') }}
        </a>

        {{-- 2. GESTIÓN DE ENTIDADES --}}
        <div x-data="{ 
            open: {{ request()->routeIs('empresas.*', 'afiliados.*', 'solicitud.*', 'asociaciones.*', 'clientes.*') ? 'true' : 'false' }}
        }" class="space-y-1">
            <button @click="open = !open"
                    class="w-full py-2.5 px-4 text-left rounded-lg transition-all duration-200 ease-in-out flex items-center font-semibold shadow-sm hover:shadow-md {{ request()->routeIs('empresas.*', 'afiliados.*', 'solicitud.*', 'asociaciones.*', 'clientes.*') ? 'text-yellow-400' : 'text-white hover:bg-chorotega-blue-light' }}">
                <i class="fas fa-users-cog mr-3"></i>
                <span class="flex-1">Gestión de Entidades</span>
                <i class="fas transition-transform duration-300" :class="open ? 'fa-chevron-down rotate-0' : 'fa-chevron-right'"></i>
            </button>
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mt-1">
                <div class="flex flex-col space-y-1 pl-4 border-l-2 border-white/20 ml-2">
                    <a href="{{ route('afiliados.list') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('afiliados.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-users mr-3 text-xs"></i>Afiliados
                    </a>
                    <a href="{{ route('empresas.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('empresas.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-building mr-3 text-xs"></i>Empresas
                    </a>
                    <a href="{{ route('solicitud.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('solicitud.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-clipboard-check mr-3 text-xs"></i>Solicitudes
                    </a>
                    <a href="{{ route('clientes.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('clientes.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-address-book mr-3 text-xs"></i>Clientes
                    </a>
                    <a href="{{ route('asociaciones.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('asociaciones.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-link mr-3 text-xs"></i>Vinculaciones
                    </a>
                </div>
            </div>
        </div>

        {{-- 3. MÓDULO POS Y OPERACIONES --}}
        <div x-data="{ 
            open: {{ request()->routeIs('ventas.*', 'inventarios.*', 'movimientos.*', 'reportes.*') ? 'true' : 'false' }}
        }" class="space-y-1">
            <button @click="open = !open"
                    class="w-full py-2.5 px-4 text-left rounded-lg transition-all duration-200 ease-in-out flex items-center font-semibold shadow-sm hover:shadow-md {{ request()->routeIs('ventas.*', 'inventarios.*', 'movimientos.*', 'reportes.*') ? 'text-yellow-400' : 'text-white hover:bg-chorotega-blue-light' }}">
                <i class="fas fa-cubes mr-3"></i>
                <span class="flex-1">Módulo POS / Inventario</span>
                <i class="fas transition-transform duration-300" :class="open ? 'fa-chevron-down rotate-0' : 'fa-chevron-right'"></i>
            </button>
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mt-1">
                <div class="flex flex-col space-y-1 pl-4 border-l-2 border-white/20 ml-2">
                    <a href="{{ route('ventas.pos') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('ventas.pos') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-cash-register mr-3 text-xs"></i>Punto de Venta (POS)
                    </a>
                    
                    {{-- 🔑 NUEVA OPCIÓN: DEVOLUCIONES --}}
                    <a href="{{ route('ventas.devolucion.form') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('ventas.devolucion.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-undo-alt mr-3 text-xs"></i>Devoluciones/Ajustes
                    </a>
                    
                    <a href="{{ route('ventas.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('ventas.index') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-history mr-3 text-xs"></i>Historial de Ventas
                    </a>
                    <a href="{{ route('inventarios.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('inventarios.index') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-list-alt mr-3 text-xs"></i>Gestión de Inventario
                    </a>
                    <a href="{{ route('movimientos.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('movimientos.index') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-clipboard-list mr-3 text-xs"></i>Movimientos/Trazabilidad
                    </a>
                    <a href="{{ route('inventarios.explorar.tiendas') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('inventarios.explorar.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-search-location mr-3 text-xs"></i>Explorar Inventario
                    </a>
                    <a href="{{ route('reportes.resumen.afiliados') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('reportes.resumen.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-chart-bar mr-3 text-xs"></i>Resumen Ingresos Afiliados
                    </a>
                </div>
            </div>
        </div>

        {{-- 4. CONFIGURACIÓN Y CATÁLOGOS --}}
        <div x-data="{ 
            open: {{ request()->routeIs('tiendas.*', 'categorias.*', 'subcategorias.*', 'rubros.*', 'tipo-organizacions.*', 'marcas.*', 'impuestos.*', 'rangos-cai.*', 'productos.*') ? 'true' : 'false' }}
        }" class="space-y-1">
            <button @click="open = !open"
                    class="w-full py-2.5 px-4 text-left rounded-lg transition-all duration-200 ease-in-out flex items-center font-semibold shadow-sm hover:shadow-md {{ request()->routeIs('tiendas.*', 'categorias.*', 'subcategorias.*', 'rubros.*', 'tipo-organizacions.*', 'marcas.*', 'impuestos.*', 'rangos-cai.*', 'productos.*') ? 'text-yellow-400' : 'text-white hover:bg-chorotega-blue-light' }}">
                <i class="fas fa-tools mr-3"></i>
                <span class="flex-1">Catálogos y Config.</span>
                <i class="fas transition-transform duration-300" :class="open ? 'fa-chevron-down rotate-0' : 'fa-chevron-right'"></i>
            </button>
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mt-1">
                <div class="flex flex-col space-y-1 pl-4 border-l-2 border-white/20 ml-2">
                    
                    <a href="{{ route('productos.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('productos.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-boxes mr-3 text-xs"></i>Productos
                    </a>

                    <a href="{{ route('categorias.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('categorias.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-tags mr-3 text-xs"></i>Categorías
                    </a>
                    <a href="{{ route('subcategorias.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('subcategorias.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-layer-group mr-3 text-xs"></i>Subcategorías
                    </a>
                    <a href="{{ route('marcas.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('marcas.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-barcode mr-3 text-xs"></i>Marcas
                    </a>
                    <a href="{{ route('tiendas.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('tiendas.index') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-store mr-3 text-xs"></i>Tiendas
                    </a>
                    <a href="{{ route('rubros.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('rubros.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-tag mr-3 text-xs"></i>Rubros
                    </a>
                    <a href="{{ route('tipo-organizacions.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('tipo-organizacions.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-sitemap mr-3 text-xs"></i>Tipos de Organización
                    </a>
                    <a href="{{ route('impuestos.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('impuestos.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-percent mr-3 text-xs"></i>Impuestos
                    </a>
                    <a href="{{ route('rangos-cai.index') }}"
                       class="py-2 px-4 rounded-lg flex items-center text-sm transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('rangos-cai.*') ? 'text-yellow-400 font-semibold' : 'text-white hover:bg-chorotega-blue-light' }}">
                        <i class="fas fa-file-invoice-dollar mr-3 text-xs"></i>Rangos CAI (Fiscal)
                    </a>
                </div>
            </div>
        </div>
        
    </nav>
</aside>