<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'Id_Cliente',
        'user_id',
        'asunto',
        'mensaje',
        'estado',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'Id_Cliente', 'Id_Cliente');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
