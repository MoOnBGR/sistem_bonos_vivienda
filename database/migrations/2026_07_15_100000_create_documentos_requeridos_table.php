<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('documentos_requeridos', function (Blueprint $table) {
            $table->id('Id_DocumentoRequerido');
            $table->string('nombre', 150)->unique();
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }
 
   
    public function down(): void
    {
        Schema::dropIfExists('documentos_requeridos');
    }
};
 
