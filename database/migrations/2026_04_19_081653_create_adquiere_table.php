<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adquiere', function (Blueprint $table) {
            // PK compuesta: ci_personal + id_tipo_trabajador
            $table->string('ci_personal', 20);
            $table->integer('id_tipo_trabajador');

            $table->primary(['ci_personal', 'id_tipo_trabajador']);

            $table->foreign('ci_personal')
                  ->references('ci')->on('persona')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('id_tipo_trabajador')
                  ->references('id')->on('tipo_trabajador')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adquiere');
    }
};