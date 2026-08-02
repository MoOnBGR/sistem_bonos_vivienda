<?php

namespace App\Http\Controllers;

use App\Models\HistorialCambio;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    public function index(Request $request)
    {
        $query = HistorialCambio::with('user')->orderBy('created_at', 'desc');

        // Filtro por módulo
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        // Filtro por funcionario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $historial = $query->paginate(20);

        $modulos = HistorialCambio::distinct()->pluck('modulo');
        $funcionarios = \App\Models\User::where('tipo_usuario', 'Funcionario')->get();

        return view('funcionario.historial', compact('historial', 'modulos', 'funcionarios'));
    }
}