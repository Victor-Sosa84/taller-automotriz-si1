<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persona', function (Blueprint $table) {
            $table->string('ci', 20)->primary();
            $table->string('nombre', 100);
            $table->string('telefono', 20)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->boolean('es_cliente')->default(false);
            $table->boolean('es_personal')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persona');
    }
};