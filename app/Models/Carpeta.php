<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carpeta extends Model
{
    use HasFactory;

    protected $table = 'carpetas_expedientes';
    protected $primaryKey = 'id_carpeta';

    protected $fillable = [
        'id_expediente',
        'id_carpeta_padre',
        'nombre',
    ];

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class, 'id_expediente', 'id_expediente');
    }

    public function carpetaPadre(): BelongsTo
    {
        return $this->belongsTo(Carpeta::class, 'id_carpeta_padre', 'id_carpeta');
    }

    public function subcarpetas(): HasMany
    {
        return $this->hasMany(Carpeta::class, 'id_carpeta_padre', 'id_carpeta');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'id_carpeta', 'id_carpeta');
    }
}