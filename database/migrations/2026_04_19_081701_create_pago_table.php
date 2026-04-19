<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pago', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_contrato');
            $table->dateTime('fecha_pago');
            $table->decimal('monto', 10, 2);
            $table->string('tipo', 50)->nullable();
            $table->string('metodo', 50)->nullable();

            $table->foreign('id_contrato')
                  ->references('id')->on('contrato')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago');
    }
};