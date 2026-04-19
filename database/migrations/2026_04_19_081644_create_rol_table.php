<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rol', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nombre', 50);
            $table->string('descripcion', 150)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol');
    }
};