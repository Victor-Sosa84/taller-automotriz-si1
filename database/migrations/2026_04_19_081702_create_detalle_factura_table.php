<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_factura', function (Blueprint $table) {
            $table->integer('id_detalle_factura')->autoIncrement()->primary();
            $table->integer('nro_factura');
            $table->string('descripcion', 255);
            $table->string('tipo', 50)->nullable();
            $table->integer('cantidad');
            $table->decimal('precio', 10, 2);
            $table->decimal('precio_unitario', 10, 2);

            $table->foreign('nro_factura')
                  ->references('nro')->on('factura')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_factura');
    }
};