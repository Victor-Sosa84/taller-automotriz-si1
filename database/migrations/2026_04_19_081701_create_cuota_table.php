<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuota', function (Blueprint $table) {
            $table->integer('nro_factura');
            $table->integer('nro_cuota');
            $table->decimal('monto', 10, 2);
            $table->date('fecha');

            $table->primary(['nro_factura', 'nro_cuota']);

            $table->foreign('nro_factura')
                  ->references('nro')->on('factura')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuota');
    }
};