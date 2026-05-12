<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orden_trabajo', 'placa_auto')) {
            Schema::table('orden_trabajo', function (Blueprint $table) {
                $table->string('placa_auto', 15)->nullable()->after('fecha_inicio');
            });
        }

        DB::statement('ALTER TABLE orden_trabajo DROP FOREIGN KEY orden_trabajo_nro_proforma_foreign');
        DB::statement('ALTER TABLE orden_trabajo MODIFY COLUMN nro_proforma INT NULL;');
        DB::statement('ALTER TABLE orden_trabajo ADD CONSTRAINT orden_trabajo_nro_proforma_foreign FOREIGN KEY (nro_proforma) REFERENCES proforma(nro) ON DELETE CASCADE ON UPDATE CASCADE;');

        DB::statement('ALTER TABLE orden_trabajo ADD CONSTRAINT orden_trabajo_placa_auto_foreign FOREIGN KEY (placa_auto) REFERENCES auto(placa) ON DELETE SET NULL ON UPDATE CASCADE;');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE orden_trabajo DROP FOREIGN KEY orden_trabajo_nro_proforma_foreign');
        DB::statement('ALTER TABLE orden_trabajo DROP FOREIGN KEY orden_trabajo_placa_auto_foreign');
        DB::statement('ALTER TABLE orden_trabajo MODIFY COLUMN nro_proforma INT NOT NULL;');

        Schema::table('orden_trabajo', function (Blueprint $table) {
            $table->dropColumn('placa_auto');
            $table->foreign('nro_proforma')->references('nro')->on('proforma')->onDelete('cascade')->onUpdate('cascade');
        });
    }
};
