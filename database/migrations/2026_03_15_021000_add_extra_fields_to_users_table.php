<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nombres')->nullable()->after('id');
            $table->string('apellidos')->nullable()->after('nombres');
            $table->string('telefono')->nullable()->after('apellidos');
            $table->string('numero_torre')->nullable()->after('telefono');
            $table->string('numero_apartamento')->nullable()->after('numero_torre');
            $table->foreignId('proyecto_id')->nullable()->constrained('proyectos')->nullOnDelete()->after('numero_apartamento');
            $table->string('cedula')->nullable()->unique()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('proyecto_id');
            $table->dropColumn([
                'nombres',
                'apellidos',
                'telefono',
                'numero_torre',
                'numero_apartamento',
                'cedula',
            ]);
        });
    }
};

