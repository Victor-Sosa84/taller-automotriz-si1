<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recoge', function (Blueprint $table) {
            $table->integer('nro_orden_trabajo');
            $table->string('ci_persona', 20);
            $table->string('relacion', 50)->nullable();
            $table->dateTime('fecha');

            $table->primary(['nro_orden_trabajo', 'ci_persona']);

            $table->foreign('nro_orden_trabajo')
                  ->references('nro')->on('orden_trabajo')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('ci_persona')
                  ->references('ci')->on('persona')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recoge');
    }
};