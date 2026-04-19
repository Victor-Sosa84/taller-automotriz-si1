<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proforma', function (Blueprint $table) {
            $table->integer('nro')->autoIncrement()->primary();
            $table->string('ci_cliente', 20);
            $table->integer('id_diagnostico');
            $table->dateTime('fecha');
            $table->decimal('total_aprox', 10, 2)->default(0.00);
            $table->string('estado', 50)->nullable();
            $table->date('plazo')->nullable();

            $table->foreign('ci_cliente')
                  ->references('ci')->on('persona')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('id_diagnostico')
                  ->references('id')->on('diagnostico')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proforma');
    }
};