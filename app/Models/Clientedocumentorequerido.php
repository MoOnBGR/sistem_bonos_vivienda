<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class ClienteDocumentoRequerido extends Model
{
    protected $table = 'cliente_documento_requerido';
 
    protected $fillable = [
        'Id_Cliente',
        'Id_DocumentoRequerido',
        'nombre_personalizado',
    ];
 
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'Id_Cliente', 'Id_Cliente');
    }
 
    public function documentoRequerido()
    {
        return $this->belongsTo(DocumentoRequerido::class, 'Id_DocumentoRequerido', 'Id_DocumentoRequerido');
    }
 
    /**
     * The display name of this checklist item, whether it comes from the
     * catalog or is a custom "Otros" entry typed by the funcionario.
     */
    public function getNombreAttribute(): string
    {
        return $this->documentoRequerido?->nombre ?? $this->nombre_personalizado;
    }
}
 
