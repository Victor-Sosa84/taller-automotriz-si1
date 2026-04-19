<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_repuesto', function (Blueprint $table) {
            $table->integer('nro_orden_trabajo');
            $table->integer('id_repuesto');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0.00);

            $table->primary(['nro_orden_trabajo', 'id_repuesto']);

            $table->foreign('nro_orden_trabajo')
                  ->references('nro')->on('orden_trabajo')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('id_repuesto')
                  ->references('id')->on('repuesto')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_repuesto');
    }
};