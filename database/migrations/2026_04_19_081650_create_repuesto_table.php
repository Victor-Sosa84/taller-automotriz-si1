<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repuesto', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nombre', 100);
            $table->string('estado', 50)->nullable();
            $table->string('marca', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repuesto');
    }
};