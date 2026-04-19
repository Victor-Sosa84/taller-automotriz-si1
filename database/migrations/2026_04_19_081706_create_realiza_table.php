<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realiza', function (Blueprint $table) {
            $table->string('ci_personal', 20);
            $table->integer('nro_orden_trabajo');
            $table->integer('id_mano_obra');
            $table->string('tipo_participacion', 100)->nullable();

            $table->primary(['ci_personal', 'nro_orden_trabajo', 'id_mano_obra']);

            $table->foreign('ci_personal')
                  ->references('ci')->on('persona')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('nro_orden_trabajo')
                  ->references('nro')->on('orden_trabajo')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('id_mano_obra')
                  ->references('id')->on('mano_obra')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realiza');
    }
};