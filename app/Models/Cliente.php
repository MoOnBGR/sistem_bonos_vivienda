<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';
    protected $primaryKey = 'Id_Cliente';

    protected $fillable = [
        'Id_user', 'identificacion', 'nombre', 'apellidos',
        'telefono', 'direccion', 'correo', 'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'Id_user');
    }

        public function documentosRequeridosAsignados()
    {
        return $this->hasMany(ClienteDocumentoRequerido::class, 'Id_Cliente', 'Id_Cliente');
    }


}