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
            $table->integer('nro');
            $table->decimal('monto', 10, 2);
            $table->date('fecha');
            $table->string('tipo_pago', 10)->nullable();
            $table->string('referencia_stripe', 40)->nullable();

            $table->primary(['nro_factura', 'nro']);

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