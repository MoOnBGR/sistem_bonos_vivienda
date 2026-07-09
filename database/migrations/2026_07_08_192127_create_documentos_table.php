<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id('id_documento');

            $table->foreignId('id_expediente')
                ->constrained('expedientes', 'id_expediente')
                ->onDelete('cascade');

            $table->foreignId('id_funcionario')
                ->nullable()
                ->constrained('users', 'id')
                ->onDelete('set null');

            $table->foreignId('Id_Cliente')
                ->nullable()
                ->constrained('clientes', 'Id_Cliente')
                ->onDelete('set null');

            $table->string('nombre_doc', 200);
            $table->string('tipo_doc', 80);
            $table->text('ruta_almac');

            $table->enum('estado_doc', ['Pendiente', 'Validado', 'Rechazado'])
                ->default('Pendiente');

            $table->dateTime('fecha_subida')->default(now());
            $table->boolean('es_duplicado')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};