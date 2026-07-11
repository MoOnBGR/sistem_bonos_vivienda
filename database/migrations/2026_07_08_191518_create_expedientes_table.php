<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id('id_expediente');

            $table->foreignId('Id_Cliente')
                ->constrained('clientes', 'Id_Cliente')
                ->onDelete('cascade');

            $table->foreignId('id_funcionario')
                ->constrained('users', 'id')
                ->onDelete('cascade');

            $table->date('fecha_creacion')->default(now());

            $table->enum('estado', ['En proceso', 'Completado', 'Inactivo'])
                ->default('En proceso');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};