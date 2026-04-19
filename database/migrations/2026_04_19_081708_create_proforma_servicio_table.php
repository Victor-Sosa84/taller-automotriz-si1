<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proforma_servicio', function (Blueprint $table) {
            $table->integer('nro_proforma');
            $table->integer('id_mano_obra');
            $table->decimal('costo', 10, 2);
            $table->string('estado', 50)->nullable();
            $table->integer('cantidad');

            $table->primary(['nro_proforma', 'id_mano_obra']);

            $table->foreign('nro_proforma')
                  ->references('nro')->on('proforma')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('id_mano_obra')
                  ->references('id')->on('mano_obra')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proforma_servicio');
    }
};