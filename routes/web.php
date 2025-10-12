<?php

use Illuminate\Support\Facades\Route;

// ✅ Controladores principales
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AfiliadoController;
use App\Http\Controllers\RubroController;
use App\Http\Controllers\TipoOrganizacionController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SubcategoriaController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\EmpresaTiendaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\VentaController; // 🧾 Controlador de Ventas

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Resultados de búsqueda y detalle de empresas
Route::get('/resultados/buscar', [SolicitudController::class, 'buscar'])->name('resultados.buscar');
Route::get('/resultados/{empresa}/detalle', [SolicitudController::class, 'verResultadoDetalle'])->name('resultados.detalle');

// Dashboard principal (basado en solicitudes)
Route::get('/dashboard', [SolicitudController::class, 'dashboard'])->name('dashboard');

// Registro de Afiliados y Empresas (sin autenticación)
Route::get('/afiliados/registro', [AfiliadoController::class, 'index'])->name('afiliados.registro');
Route::post('/afiliados/query', [AfiliadoController::class, 'query'])->name('afiliados.query');
Route::post('/afiliados/register', [AfiliadoController::class, 'registerFromQuery'])->name('afiliados.register');

Route::get('/empresas/create', [EmpresaController::class, 'create'])->name('empresas.create');
Route::resource('empresas', EmpresaController::class)->except('create');

// Registro de productos por Afiliados
Route::get('/afiliados/productos/sugerir', [ProductoController::class, 'createAfiliado'])->name('afiliados.productos.create');
Route::post('/afiliados/productos', [ProductoController::class, 'storeAfiliado'])->name('afiliados.productos.store');
Route::get('/marcas/store-from-form', [MarcaController::class, 'storeFromForm'])->name('marcas.storeFromForm');
Route::post('/productos/reiniciar-proceso', [ProductoController::class, 'reiniciarProceso'])->name('productos.reiniciar');
Route::get('/registro-completo', [RegistroController::class, 'showForm'])->name('registro.completo');
Route::post('/registro-completo', [RegistroController::class, 'store'])->name('registro.store');


/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // =========================================================
    // 🧾 MÓDULO DE VENTAS (POS)
    // =========================================================

    // Interfaz principal del Punto de Venta
    Route::get('ventas/pos', [VentaController::class, 'create'])->name('ventas.pos');

    // Buscar clientes (usado por el buscador del POS)
    Route::get('ventas/buscar-clientes', [VentaController::class, 'buscarClientes'])->name('ventas.buscar-clientes');

    // Registrar nuevo cliente desde el modal del POS
    Route::post('ventas/store-cliente', [VentaController::class, 'storeCliente'])->name('ventas.store-cliente');

    // Obtener productos disponibles por tienda (solo con stock > 0)
    Route::get('ventas/productos-por-tienda/{tienda_id}', [VentaController::class, 'getProductosParaVenta'])->name('ventas.productos-por-tienda');

    // CRUD general de ventas (index, show, destroy para historial)
    Route::resource('ventas', VentaController::class)->except(['create', 'edit', 'update']);


    // =========================================================
    // 📦 INVENTARIO Y EXPLORADOR
    // =========================================================
    Route::get('/api/tiendas/{tienda_id}/empresas', [InventarioController::class, 'getEmpresasPorTienda'])->name('api.tiendas.empresas');
    Route::get('/api/empresas/{empresa_id}/marcas-disponibles/{tienda_id}', [InventarioController::class, 'getMarcasPorEmpresa'])->name('api.empresas.marcas');

    Route::get('inventarios/explorar/tiendas', [InventarioController::class, 'explorarTiendas'])->name('inventarios.explorar.tiendas');
    Route::get('inventarios/explorar/tienda/{tienda}', [InventarioController::class, 'explorarEmpresasPorTienda'])->name('inventarios.explorar.empresas');
    Route::get('inventarios/explorar/empresa/{empresa}/tienda/{tienda}', [InventarioController::class, 'mostrarInventarioPorEmpresa'])->name('inventarios.explorar.inventario');
    Route::resource('inventarios', InventarioController::class);


    // =========================================================
    // 🏢 SOLICITUDES DE EMPRESAS
    // =========================================================
    Route::prefix('solicitudes')->name('solicitud.')->group(function () {
        Route::get('/', [SolicitudController::class, 'index'])->name('index');
        Route::get('/{empresa}', [SolicitudController::class, 'show'])->name('show');
        Route::post('/{empresa}/aprobar', [SolicitudController::class, 'aprobar'])->name('aprobar');
        Route::post('/{empresa}/rechazar', [SolicitudController::class, 'rechazar'])->name('rechazar');
    });


    // =========================================================
    // 👤 PERFIL DE USUARIO
    // =========================================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // =========================================================
    // 🗂️ CATÁLOGOS Y RECURSOS
    // =========================================================
    Route::resource('rubros', RubroController::class);
    Route::resource('tipo-organizacions', TipoOrganizacionController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('subcategorias', SubcategoriaController::class);
    Route::resource('tiendas', TiendaController::class);

    // =========================================================
    // 🔗 ASOCIACIONES EMPRESA-TIENDA
    // =========================================================
    Route::get('asociaciones', [EmpresaTiendaController::class, 'index'])->name('asociaciones.index');
    Route::get('asociaciones/create', [EmpresaTiendaController::class, 'create'])->name('asociaciones.create');
    Route::post('asociaciones', [EmpresaTiendaController::class, 'store'])->name('asociaciones.store');
    Route::get('asociaciones/{empresa}/{tienda}', [EmpresaTiendaController::class, 'show'])->name('asociaciones.show');
    Route::get('asociaciones/{empresa}/{tienda}/edit', [EmpresaTiendaController::class, 'edit'])->name('asociaciones.edit');
    Route::put('asociaciones/{empresa}/{tienda}', [EmpresaTiendaController::class, 'update'])->name('asociaciones.update');
    Route::delete('asociaciones/{empresa}/{tienda}', [EmpresaTiendaController::class, 'destroy'])->name('asociaciones.destroy');


    // =========================================================
    // 🧍‍♂️ AFILIADOS Y PRODUCTOS
    // =========================================================
    Route::get('/afiliados', [AfiliadoController::class, 'list'])->name('afiliados.list');
    Route::resource('afiliados', AfiliadoController::class)->except(['index', 'create', 'store']);
    Route::resource('productos', ProductoController::class);
    Route::resource('marcas', MarcaController::class);
});

require __DIR__ . '/auth.php';
