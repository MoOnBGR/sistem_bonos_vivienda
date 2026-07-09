<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expediente extends Model
{
    use HasFactory;

    protected $table = 'expedientes';
    protected $primaryKey = 'id_expediente';

    protected $fillable = [
        'Id_Cliente',
        'id_funcionario',
        'fecha_creacion',
        'estado',
    ];

    protected $casts = [
        'fecha_creacion' => 'date',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'Id_Cliente', 'Id_Cliente');
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_funcionario', 'id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'id_expediente', 'id_expediente');
    }
}