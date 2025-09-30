<?php

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
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// **RUTAS PÚBLICAS (ACCESIBLES SIN AUTENTICACIÓN)**
// Rutas del proceso de registro de Afiliados.
Route::get('/afiliados/registro', [AfiliadoController::class, 'index'])->name('afiliados.registro');
Route::post('/afiliados/query', [AfiliadoController::class, 'query'])->name('afiliados.query');
Route::post('/afiliados/register', [AfiliadoController::class, 'registerFromQuery'])->name('afiliados.register');

// Rutas de Empresa (son públicas para el proceso de registro del afiliado)
Route::get('/empresas/create', [EmpresaController::class, 'create'])->name('empresas.create');
Route::resource('empresas', EmpresaController::class)->except('create');

// Rutas para el registro de productos del afiliado
Route::get('/afiliados/productos/sugerir', [ProductoController::class, 'createAfiliado'])->name('afiliados.productos.create');
Route::post('/afiliados/productos', [ProductoController::class, 'storeAfiliado'])->name('afiliados.productos.store');

// Nueva ruta para la creación de marcas desde el formulario (GET)
Route::get('/marcas/store-from-form', [MarcaController::class, 'storeFromForm'])
     ->name('marcas.storeFromForm');

Route::post('/productos/reiniciar-proceso', [ProductoController::class, 'reiniciarProceso'])
    ->name('productos.reiniciar');

Route::get('/registro-completo', [RegistroController::class, 'showForm'])->name('registro.completo');

// Nueva ruta para el almacenamiento de todos los datos en un solo envío
Route::post('/registro-completo', [RegistroController::class, 'store'])->name('registro.store');

// Mantén la ruta de consulta del DNI, ya que la vista la necesita para el primer paso.
Route::post('/afiliados/query', [AfiliadoController::class, 'query'])->name('afiliados.query');

     
Route::middleware('auth')->group(function () {
    // **RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN)**
    
    // Rutas del perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Recursos (protegidas por autenticación)
    Route::resource('rubros', RubroController::class);
    Route::resource('tipo-organizacions', TipoOrganizacionController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('subcategorias', SubcategoriaController::class);
    Route::resource('tiendas', TiendaController::class);
    
    // Rutas para el CRUD de Asociaciones (Empresa-Tienda)
    Route::get('asociaciones', [EmpresaTiendaController::class, 'index'])->name('asociaciones.index');
    Route::get('asociaciones/create', [EmpresaTiendaController::class, 'create'])->name('asociaciones.create');
    Route::post('asociaciones', [EmpresaTiendaController::class, 'store'])->name('asociaciones.store');
    
    // Rutas corregidas
    Route::get('asociaciones/{empresa}/{tienda}', [EmpresaTiendaController::class, 'show'])->name('asociaciones.show');
    Route::get('asociaciones/{empresa}/{tienda}/edit', [EmpresaTiendaController::class, 'edit'])->name('asociaciones.edit');
    Route::put('asociaciones/{empresa}/{tienda}', [EmpresaTiendaController::class, 'update'])->name('asociaciones.update');
    Route::delete('asociaciones/{empresa}/{tienda}', [EmpresaTiendaController::class, 'destroy'])->name('asociaciones.destroy');

    // Rutas de Afiliados (listado, edición y eliminación)
    Route::get('/afiliados', [AfiliadoController::class, 'list'])->name('afiliados.list');
    Route::resource('afiliados', AfiliadoController::class)->except([
        'index', 'create', 'store'
    ]);

    Route::resource('productos', ProductoController::class);
    Route::resource('marcas', MarcaController::class);
});

require __DIR__.'/auth.php';