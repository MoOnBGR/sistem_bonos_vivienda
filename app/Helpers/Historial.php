<?php

namespace App\Helpers;

use App\Models\HistorialCambio;
use Illuminate\Support\Facades\Auth;

class Historial
{
    public static function registrar(string $modulo, string $accion, string $descripcion = '', string $registro_id = null): void
    {
        if (!Auth::check()) return;

        HistorialCambio::create([
            'user_id' => Auth::id(),
            'modulo' => $modulo,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'registro_id' => $registro_id,
        ]);
    }
}