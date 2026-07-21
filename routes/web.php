<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\DocumentoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ExpedienteCarpetaController;
use Illuminate\Http\Request;

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
    Route::get('/crear-funcionario', function () {
    if (Auth::user()->tipo_usuario !== 'Funcionario') {
        abort(403);
    }
    return view('funcionario.crear_funcionario');
    })->name('crear');
    Route::post('/crear-funcionario', function (Request $request) {
    if (Auth::user()->tipo_usuario !== 'Funcionario') {
        abort(403);
    }
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
    ]);

    \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        'tipo_usuario' => 'Funcionario',
    ]);

    return back()->with('success', '¡Funcionario creado exitosamente!');
    })->name('funcionarios.store');

    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.crear');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes/{cliente}/editar', [ClienteController::class, 'editFuncionario'])->name('clientes.editar');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'updateFuncionario'])->name('clientes.actualizar');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

});


//Rutas de Carpeta dentro de Expediente

Route::get('/expedientes/{expediente}/carpetas/{carpeta?}', [ExpedienteCarpetaController::class, 'index'])->name('expedientes.carpetas.index');
Route::post('/expedientes/{expediente}/carpetas', [ExpedienteCarpetaController::class, 'store'])->name('expedientes.carpetas.store');
Route::put('/expedientes/carpetas/{carpeta}', [ExpedienteCarpetaController::class, 'update'])->name('expedientes.carpetas.update');
Route::delete('/expedientes/carpetas/{carpeta}', [ExpedienteCarpetaController::class, 'destroy'])->name('expedientes.carpetas.destroy');

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

    Route::post('/expedientes/{expediente}/reabrir', [ExpedienteController::class, 'reabrir'])
        ->name('expedientes.reabrir');
    
    // ==========================================
    // MÓDULO DE DOCUMENTOS - Naraly
    // ==========================================
    Route::resource('documentos', DocumentoController::class);
    Route::patch('documentos/{id}/validar', [DocumentoController::class, 'update'])
         ->name('documentos.validar');   
});

// Rutas de Cliente
Route::middleware(['auth', 'verified', 'cliente.completo'])->prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/dashboard', function () {
        if (Auth::user()->tipo_usuario !== 'Cliente') {
            abort(403);
        }
        return view('cliente.dashboard');
    })->name('dashboard');
    // ==========================================
        // DOCUMENTOS - CLIENTE (Naraly)
        // ==========================================
        Route::get('/documentos', [DocumentoController::class, 'misDocumentos'])
            ->name('documentos');
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