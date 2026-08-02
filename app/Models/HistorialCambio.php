<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialCambio extends Model
{
    protected $table = 'historial_cambios';

    protected $fillable = [
        'user_id',
        'accion',
        'modulo',
        'descripcion',
        'registro_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}