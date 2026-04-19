<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnostico', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->dateTime('fecha');
            $table->string('ci_personal', 20);
            $table->string('placa_auto', 15);

            $table->foreign('ci_personal')
                  ->references('ci')->on('persona')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('placa_auto')
                  ->references('placa')->on('auto')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostico');
    }
};