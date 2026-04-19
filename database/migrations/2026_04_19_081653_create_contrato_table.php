<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('ci_personal', 20);
            $table->integer('tipo_remuneracion');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('estado', 20)->nullable();
            $table->string('periodo_pago', 50)->nullable();
            $table->decimal('valor', 10, 2);

            $table->foreign('ci_personal')
                  ->references('ci')->on('persona')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('tipo_remuneracion')
                  ->references('nro')->on('tipo_remuneracion')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato');
    }
};