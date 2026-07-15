<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class DocumentoRequerido extends Model
{
    protected $table = 'documentos_requeridos';
    protected $primaryKey = 'Id_DocumentoRequerido';
 
    protected $fillable = [
        'nombre',
        'orden',
    ];
 
    public function clienteDocumentoRequeridos()
    {
        return $this->hasMany(ClienteDocumentoRequerido::class, 'Id_DocumentoRequerido', 'Id_DocumentoRequerido');
    }
}
 
