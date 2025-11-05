<?php

use Illuminate\Support\Facades\Route;

// Controladores principales
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
use App\Http\Controllers\VentaController; 
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ImpuestoController; 
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\RangoCaiController;

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


// =====================================================================
// RUTA POST ABIERTA: MOVIMIENTOS (MOVER DENTRO DE 'auth' SI ES NECESARIO)
// =====================================================================
Route::post('movimientos', [MovimientoInventarioController::class, 'store'])->name('movimientos.store');


/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // =========================================================
    // MÓDULO DE MOVIMIENTOS
    // =========================================================
    Route::controller(MovimientoInventarioController::class)->prefix('movimientos')->name('movimientos.')->group(function () {
        Route::get('/', 'index')->name('index'); 
        Route::get('/create', 'create')->name('create'); 
    });
    
    // =========================================================
    // MODULO DE VENTAS (POS) - AJUSTADO PARA IMPRESIÓN
    // =========================================================
    
    Route::controller(VentaController::class)->prefix('ventas')->name('ventas.')->group(function () {
        
        // 1. Interfaz del Punto de Venta (Nombre: ventas.pos)
        Route::get('pos', 'create')->name('pos'); 
        
        // 2. Rutas Auxiliares
        Route::get('buscar-clientes', 'buscarClientes')->name('buscar-clientes');
        Route::post('store-cliente', 'storeCliente')->name('store-cliente');
        Route::get('productos-por-tienda/{tienda_id}', 'getProductosParaVenta')->name('productos-por-tienda');
        
        // 3. Rutas de Transacción (POST)
        Route::post('store-ticket', 'storeTicket')->name('store-ticket');
        Route::post('store-quote', 'storeQuote')->name('store-quote');
        Route::post('store-invoice', 'storeInvoice')->name('store-invoice');

        // ⭐ RUTA DE IMPRESIÓN CORREGIDA Y RENOMBRADA
        Route::get('documento/{id}/{type}/imprimir', 'printDocument')->name('print');
        
        // 4. CRUD Manual (Historial de ventas)
        Route::get('/', 'index')->name('index'); // Historial de ventas
        Route::get('{venta}', 'show')->name('show');
        Route::delete('{venta}', 'destroy')->name('destroy');
    });


    // =========================================================
    // INVENTARIO Y EXPLORADOR
    // =========================================================
    Route::get('/api/tiendas/{tienda_id}/empresas', [InventarioController::class, 'getEmpresasPorTienda'])->name('api.tiendas.empresas');
    Route::get('/api/empresas/{empresa_id}/marcas-disponibles/{tienda_id}', [InventarioController::class, 'getMarcasPorEmpresa'])->name('api.empresas.marcas');

    Route::get('inventarios/explorar/tiendas', [InventarioController::class, 'explorarTiendas'])->name('inventarios.explorar.tiendas');
    Route::get('inventarios/explorar/tienda/{tienda}', [InventarioController::class, 'explorarEmpresasPorTienda'])->name('inventarios.explorar.empresas');
    Route::get('inventarios/explorar/empresa/{empresa}/tienda/{tienda}', [InventarioController::class, 'mostrarInventarioPorEmpresa'])->name('inventarios.explorar.inventario');
    Route::resource('inventarios', InventarioController::class);


    // =========================================================
    // SOLICITUDES DE EMPRESAS
    // =========================================================
    Route::prefix('solicitudes')->name('solicitud.')->group(function () {
        Route::get('/', [SolicitudController::class, 'index'])->name('index');
        Route::get('/{empresa}', [SolicitudController::class, 'show'])->name('show');
        Route::post('/{empresa}/aprobar', [SolicitudController::class, 'aprobar'])->name('aprobar');
        Route::post('/{empresa}/rechazar', [SolicitudController::class, 'rechazar'])->name('rechazar');
    });


    // =========================================================
    // PERFIL DE USUARIO
    // =========================================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // =========================================================
    // CATÁLOGOS Y RECURSOS
    // =========================================================
    Route::resource('rubros', RubroController::class);
    Route::resource('tipo-organizacions', TipoOrganizacionController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('subcategorias', SubcategoriaController::class);
    Route::resource('tiendas', TiendaController::class);
    
    Route::resource('impuestos', ImpuestoController::class); 
    
    // GESTIÓN DE RANGOS CAI (ADMINISTRACIÓN FISCAL)
    Route::resource('rangos-cai', RangoCaiController::class); 

    // =========================================================
    // ASOCIACIONES EMPRESA-TIENDA
    // =========================================================
    Route::get('asociaciones', [EmpresaTiendaController::class, 'index'])->name('asociaciones.index');
    Route::get('asociaciones/create', [EmpresaTiendaController::class, 'create'])->name('asociaciones.create');
    Route::post('asociaciones', [EmpresaTiendaController::class, 'store'])->name('asociaciones.store');
    Route::get('asociaciones/{empresa}/{tienda}', [EmpresaTiendaController::class, 'show'])->name('asociaciones.show');
    Route::get('asociaciones/{empresa}/{tienda}/edit', [EmpresaTiendaController::class, 'edit'])->name('asociaciones.edit');
    Route::put('asociaciones/{empresa}/{tienda}', [EmpresaTiendaController::class, 'update'])->name('asociaciones.update');
    Route::delete('asociaciones/{empresa}/{tienda}', [EmpresaTiendaController::class, 'destroy'])->name('asociaciones.destroy');


    // =========================================================
    // AFILIADOS Y PRODUCTOS
    // =========================================================
    Route::get('/afiliados', [AfiliadoController::class, 'list'])->name('afiliados.list');
    Route::resource('afiliados', AfiliadoController::class)->except(['index', 'create', 'store']);
    Route::resource('productos', ProductoController::class);
    Route::resource('marcas', MarcaController::class);

    Route::resource('clientes', ClienteController::class);
    
});

require __DIR__ . '/auth.php';