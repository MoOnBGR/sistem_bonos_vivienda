<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('cliente_documento_requerido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Id_Cliente')->constrained('clientes', 'Id_Cliente')->onDelete('cascade');
 
            // Nullable: null means this row is a custom ("Otros") item typed by the funcionario.
            $table->foreignId('Id_DocumentoRequerido')->nullable()
                ->constrained('documentos_requeridos', 'Id_DocumentoRequerido')->onDelete('cascade');
 
            // Only used when Id_DocumentoRequerido is null (a custom "Otros" item).
            $table->string('nombre_personalizado', 150)->nullable();
 
            $table->timestamps();
        });
    }
 
   
    public function down(): void
    {
        Schema::dropIfExists('cliente_documento_requerido');
    }
};
 
