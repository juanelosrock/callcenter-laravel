<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Autenticadas
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Pedido — selección ciudad/dirección y menú
    Route::get('/pedido', [HomeController::class, 'index'])->name('pedido.nuevo');
    Route::get('/pedido/menu', [MenuController::class, 'index'])->name('pedido.menu');

    // API — Ciudad y dirección
    Route::post('/api/ciudades',          [HomeController::class, 'ciudades'])->name('api.ciudades');
    Route::post('/api/validar-direccion', [HomeController::class, 'validarDireccion'])->name('api.validar-direccion');

    // API — Menú y productos
    Route::post('/api/menu',      [MenuController::class, 'menu'])->name('api.menu');
    Route::post('/api/producto',  [MenuController::class, 'producto'])->name('api.producto');
    Route::post('/api/combos',    [MenuController::class, 'combos'])->name('api.combos');
    Route::post('/api/adiciones', [MenuController::class, 'adiciones'])->name('api.adiciones');

    // API — Pedidos y cupones
    Route::post('/api/pedido',        [OrderController::class, 'enviar'])->name('api.pedido');
    Route::post('/api/estado-pedido', [OrderController::class, 'estado'])->name('api.estado-pedido');
    Route::post('/api/cupon',         [OrderController::class, 'validarCupon'])->name('api.cupon');

    // Admin — solo rol admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
    });
});
