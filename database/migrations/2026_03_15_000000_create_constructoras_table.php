<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('constructoras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('nit')->unique();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('representante_legal')->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->string('estado')->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('constructoras');
    }
};

