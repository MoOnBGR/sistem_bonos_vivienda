<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ExpedienteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
});

// Redirige según rol después del login
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->tipo_usuario === 'Funcionario') {
        return redirect()->route('funcionario.dashboard');
    } else {
        return redirect()->route('cliente.dashboard');
    }
})->middleware(['auth', 'verified', 'cliente.completo'])->name('dashboard');

// Rutas de Funcionario
Route::middleware(['auth', 'verified'])->prefix('funcionario')->name('funcionario.')->group(function () {
    Route::get('/dashboard', function () {
        if (Auth::user()->tipo_usuario !== 'Funcionario') {
            abort(403);
        }
        return view('funcionario.dashboard');
    })->name('dashboard');

    // Rutas de Clientes (nombradas en español para que coincidan con las vistas de Monse)
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.crear');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes/{cliente}/editar', [ClienteController::class, 'edit'])->name('clientes.editar');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.actualizar');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
});

// Rutas de Expediente

Route::view('/expedientes/nuevo', 'expediente.buscar')
    ->name('expedientes.crear.buscar');

Route::post('/expedientes/buscar-crear', [ExpedienteController::class, 'buscarParaCrear'])
    ->name('expedientes.buscarCrear');

Route::get('/expedientes', [ExpedienteController::class, 'index'])
    ->name('expedientes.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/expedientes/buscar', [ExpedienteController::class, 'buscarPorCedula'])
        ->name('expedientes.buscar');

    Route::get('/expedientes/crear/{cliente}', [ExpedienteController::class, 'create'])
        ->name('expedientes.crear');

    Route::post('/expedientes', [ExpedienteController::class, 'store'])
        ->name('expedientes.store');

    Route::get('/expedientes/{expediente}/confirmacion', [ExpedienteController::class, 'confirmacion'])
    ->name('expedientes.confirmacion');

    Route::get('/expedientes/cliente/{cliente}', [ExpedienteController::class, 'consultarPorCliente'])
        ->name('expedientes.consultar');

    Route::get('/expedientes/{expediente}/editar', [ExpedienteController::class, 'edit'])
        ->name('expedientes.editar');

    Route::put('/expedientes/{expediente}', [ExpedienteController::class, 'update'])
        ->name('expedientes.update');

    Route::post('/expedientes/{expediente}/cerrar', [ExpedienteController::class, 'cerrar'])
        ->name('expedientes.cerrar');
});

// Rutas de Cliente
Route::middleware(['auth', 'verified', 'cliente.completo'])->prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/dashboard', function () {
        if (Auth::user()->tipo_usuario !== 'Cliente') {
            abort(403);
        }
        return view('cliente.dashboard');
    })->name('dashboard');
});

// Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/datos-adicionales', [ClienteController::class, 'create'])->name('cliente.datos');
    Route::post('/datos-adicionales', [ClienteController::class, 'store'])->name('cliente.store');
});

Route::middleware(['auth', 'cliente.completo'])->group(function () {
    Route::get('/buscar-cliente', [ClienteController::class, 'buscar'])->name('cliente.buscar');
}); 

require __DIR__.'/auth.php';