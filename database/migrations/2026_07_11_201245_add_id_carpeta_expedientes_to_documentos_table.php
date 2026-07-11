<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->foreignId('id_carpeta')
                ->nullable()
                ->after('id_expediente')
                ->constrained('carpetas_expedientes', 'id_carpeta')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_carpeta');
        });
    }
};