<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\NotificacionController;
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

    // Gestión de Funcionarios
    Route::get('/funcionarios', function () {
        if (Auth::user()->tipo_usuario !== 'Funcionario') {
            abort(403);
        }
        $funcionarios = \App\Models\User::where('tipo_usuario', 'Funcionario')->get();
        return view('funcionario.funcionarios.index', compact('funcionarios'));
    })->name('funcionarios.index');

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

        \App\Helpers\Historial::registrar('Usuarios', 'Crear', 'Se creó el funcionario: ' . $request->name);

        return redirect()->route('funcionario.funcionarios.index')->with('success', '¡Funcionario creado exitosamente!');
    })->name('funcionarios.store');

    Route::get('/funcionarios/{id}/editar', function ($id) {
        if (Auth::user()->tipo_usuario !== 'Funcionario') {
            abort(403);
        }
        $funcionario = \App\Models\User::findOrFail($id);
        return view('funcionario.funcionarios.editar', compact('funcionario'));
    })->name('funcionarios.editar');

    Route::put('/funcionarios/{id}', function (Request $request, $id) {
        if (Auth::user()->tipo_usuario !== 'Funcionario') {
            abort(403);
        }
        $funcionario = \App\Models\User::findOrFail($id);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', \Illuminate\Validation\Rules\Password::defaults()];
        }

        $request->validate($rules);

        $funcionario->name = $request->name;
        $funcionario->email = $request->email;
        if ($request->filled('password')) {
            $funcionario->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $funcionario->save();

        \App\Helpers\Historial::registrar('Usuarios', 'Actualizar', 'Se actualizó el funcionario: ' . $funcionario->name);

        return redirect()->route('funcionario.funcionarios.index')->with('success', 'Funcionario actualizado correctamente.');
    })->name('funcionarios.update');

    Route::delete('/funcionarios/{id}', function ($id) {
        if (Auth::user()->tipo_usuario !== 'Funcionario') {
            abort(403);
        }
        if (Auth::id() == $id) {
            return redirect()->route('funcionario.funcionarios.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        $funcionario = \App\Models\User::findOrFail($id);
        $nombre = $funcionario->name;
        $funcionario->delete();

        \App\Helpers\Historial::registrar('Usuarios', 'Eliminar', 'Se eliminó el funcionario: ' . $nombre);

        return redirect()->route('funcionario.funcionarios.index')->with('success', 'Funcionario eliminado correctamente.');
    })->name('funcionarios.destroy');

    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.crear');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes/{cliente}/editar', [ClienteController::class, 'editFuncionario'])->name('clientes.editar');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'updateFuncionario'])->name('clientes.actualizar');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

    // Documentos - Funcionario
    Route::get('/documentos/cliente', [DocumentoController::class, 'buscarCliente'])->name('documentos.buscar');
    Route::get('/documentos/subir/{id_expediente}/{carpeta?}', [DocumentoController::class, 'subirDocumentoEmpresa'])->name('documentos.subir');
    Route::post('/documentos/subir-empresa', [DocumentoController::class, 'storeDocumentoEmpresa'])->name('documentos.subir-empresa');

    Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');

    // Notificaciones
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::get('/notificaciones/crear', [NotificacionController::class, 'create'])->name('notificaciones.crear');
    Route::post('/notificaciones', [NotificacionController::class, 'store'])->name('notificaciones.store');
});

// Rutas de Carpeta dentro de Expediente
Route::get('/expedientes/{expediente}/carpetas/{carpeta?}', [ExpedienteCarpetaController::class, 'index'])->name('expedientes.carpetas.index');
Route::post('/expedientes/{expediente}/carpetas', [ExpedienteCarpetaController::class, 'store'])->name('expedientes.carpetas.store');
Route::get('/expedientes/carpetas/{carpeta}/editar', [ExpedienteCarpetaController::class, 'edit'])->name('expedientes.carpetas.editar');
Route::put('/expedientes/carpetas/{carpeta}', [ExpedienteCarpetaController::class, 'update'])->name('expedientes.carpetas.update');
Route::delete('/expedientes/carpetas/{carpeta}', [ExpedienteCarpetaController::class, 'destroy'])->name('expedientes.carpetas.destroy');

// Subir documento a carpeta específica
Route::post('/expedientes/{expediente}/carpetas/{carpeta}/documentos', [DocumentoController::class, 'subirDocumentoCarpeta'])
    ->name('expedientes.carpetas.documentos.subir');

// Copiar/Pegar/Cancelar documentos
Route::post('/documentos/cancelar-movimiento', [DocumentoController::class, 'cancelarMovimiento'])
    ->name('documentos.cancelar-movimiento');
Route::post('/documentos/{id}/copiar', [DocumentoController::class, 'copiarDocumento'])
    ->name('documentos.copiar');
Route::post('/expedientes/{expediente}/pegar', [DocumentoController::class, 'pegarDocumento'])
    ->name('documentos.pegar');

// Mover documento
Route::patch('/documentos/{documento}/mover', [DocumentoController::class, 'moverDocumento'])
    ->name('documentos.mover');

// Rutas de Expediente
Route::view('/expedientes/nuevo', 'expediente.buscar')->name('expedientes.crear.buscar');
Route::post('/expedientes/buscar-crear', [ExpedienteController::class, 'buscarParaCrear'])->name('expedientes.buscarCrear');
Route::get('/expedientes', [ExpedienteController::class, 'index'])->name('expedientes.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/expedientes/buscar', [ExpedienteController::class, 'buscarPorCedula'])->name('expedientes.buscar');
    Route::get('/expedientes/crear/{cliente}', [ExpedienteController::class, 'create'])->name('expedientes.crear');
    Route::post('/expedientes', [ExpedienteController::class, 'store'])->name('expedientes.store');
    Route::get('/expedientes/{expediente}/confirmacion', [ExpedienteController::class, 'confirmacion'])->name('expedientes.confirmacion');
    Route::get('/expedientes/cliente/{cliente}', [ExpedienteController::class, 'consultarPorCliente'])->name('expedientes.consultar');
    Route::get('/expedientes/{expediente}/editar', [ExpedienteController::class, 'edit'])->name('expedientes.editar');
    Route::put('/expedientes/{expediente}', [ExpedienteController::class, 'update'])->name('expedientes.update');
    Route::post('/expedientes/{expediente}/cerrar', [ExpedienteController::class, 'cerrar'])->name('expedientes.cerrar');
    Route::post('/expedientes/{expediente}/reabrir', [ExpedienteController::class, 'reabrir'])->name('expedientes.reabrir');

    // MÓDULO DE DOCUMENTOS - Naraly
    Route::get('/documentos/requerir/{id_expediente}', [DocumentoController::class, 'mostrarRequerir'])->name('documentos.requerir');
    Route::post('/documentos/requerir', [DocumentoController::class, 'requerirDocumento'])->name('documentos.requerir.post');
    Route::patch('/documentos/{id}/validar', [DocumentoController::class, 'update'])->name('documentos.validar');
    Route::delete('/documentos/solicitud/{id}', [DocumentoController::class, 'eliminarSolicitud'])->name('documentos.solicitud.destroy');

    // Resource DESPUÉS
    Route::resource('documentos', DocumentoController::class);
});

// Rutas de Cliente
Route::middleware(['auth', 'verified', 'cliente.completo'])->prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/dashboard', function () {
        if (Auth::user()->tipo_usuario !== 'Cliente') {
            abort(403);
        }
        return view('cliente.dashboard');
    })->name('dashboard');

    Route::get('/documentos', [DocumentoController::class, 'misDocumentos'])->name('documentos');
    Route::post('/documentos/subir', [DocumentoController::class, 'subirDocumentoCliente'])->name('documentos.subir');
    Route::delete('/documentos/eliminar/{id}', [DocumentoController::class, 'eliminarDocumentoCliente'])->name('documentos.eliminar');

    Route::get('/tramite', function () {
        $cliente = Auth::user()->cliente;
        if (!$cliente) {
            return redirect()->route('cliente.datos');
        }
        $expediente = \App\Models\Expediente::where('Id_Cliente', $cliente->Id_Cliente)
            ->with(['funcionario'])
            ->latest('fecha_creacion')
            ->first();

        $documentos = $expediente
            ? \App\Models\Documento::where('id_expediente', $expediente->id_expediente)
                ->where('Id_Cliente', $cliente->Id_Cliente)
                ->get()
            : collect();

        return view('cliente.tramite', compact('cliente', 'expediente', 'documentos'));
    })->name('tramite');
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