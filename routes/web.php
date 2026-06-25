<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Redirige según rol después del login
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->tipo_usuario === 'Funcionario') {
        return redirect()->route('funcionario.dashboard');
    } else {
        return redirect()->route('cliente.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

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
Route::middleware(['auth', 'verified'])->prefix('cliente')->name('cliente.')->group(function () {
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

require __DIR__.'/auth.php';