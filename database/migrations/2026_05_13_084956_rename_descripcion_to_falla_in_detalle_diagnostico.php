<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detalle_diagnostico', function (Blueprint $table) {
            $table->renameColumn('descripcion', 'falla');
        });
    }

    public function down(): void
    {
        Schema::table('detalle_diagnostico', function (Blueprint $table) {
            $table->renameColumn('falla', 'descripcion');
        });
    }
};
