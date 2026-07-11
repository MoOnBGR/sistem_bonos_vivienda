<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carpetas_expedientes', function (Blueprint $table) {
            $table->id('id_carpeta');

            $table->foreignId('id_expediente')
                ->constrained('expedientes', 'id_expediente')
                ->onDelete('cascade');

            $table->foreignId('id_carpeta_padre')
                ->nullable()
                ->constrained('carpetas_expedientes', 'id_carpeta')
                ->onDelete('cascade');

            $table->string('nombre', 100);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carpetas_expedientes');
    }
};