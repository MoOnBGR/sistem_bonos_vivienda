<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
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
    Route::resource('admin/clientes', ClienteController::class);
});


Route::middleware(['auth'])->group(function () {
    Route::get('/datos-adicionales', [ClienteController::class, 'create'])->name('cliente.datos');
    Route::post('/datos-adicionales', [ClienteController::class, 'store'])->name('cliente.store');
});

Route::middleware(['auth', 'cliente.completo'])->group(function () {
    Route::get('/buscar-cliente', [ClienteController::class, 'buscar'])->name('cliente.buscar');
}); 

require __DIR__.'/auth.php';