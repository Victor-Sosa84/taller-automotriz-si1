<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_diagnostico', function (Blueprint $table) {
            $table->integer('id_diagnostico');
            $table->integer('id_detalle_diagnostico');
            $table->text('descripcion');

            $table->primary(['id_diagnostico', 'id_detalle_diagnostico']);

            $table->foreign('id_diagnostico')
                  ->references('id')->on('diagnostico')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_diagnostico');
    }
};