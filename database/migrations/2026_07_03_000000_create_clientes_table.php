<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id('Id_Cliente');
            $table->foreignId('Id_user')->nullable()->constrained('users')->onDelete('set null');
            $table->string('identificacion', 20)->unique();
            $table->string('nombre', 100);
            $table->string('apellidos', 100);
            $table->string('telefono', 20);
            $table->text('direccion');
            $table->string('correo', 150);
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};