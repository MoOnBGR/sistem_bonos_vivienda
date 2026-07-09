<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documentos';
    protected $primaryKey = 'id_documento';

    protected $fillable = [
        'id_expediente',
        'id_funcionario',
        'Id_Cliente',
        'nombre_doc',
        'tipo_doc',
        'ruta_almac',
        'estado_doc',
        'fecha_subida',
        'es_duplicado',
    ];

    protected $casts = [
        'fecha_subida' => 'datetime',
        'es_duplicado' => 'boolean',
    ];

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class, 'id_expediente', 'id_expediente');
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_funcionario', 'id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'Id_Cliente', 'Id_Cliente');
    }
}