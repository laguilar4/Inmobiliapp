<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visita_cuerpos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visita_cabecera_id')->constrained('visita_cabeceras')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('cedula');
            $table->string('correo');
            $table->string('estado')->default('pendiente');
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visita_cuerpos');
    }
};
