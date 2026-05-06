<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, limpiar datos inválidos o duplicados
        DB::statement('UPDATE users SET phone = NULL WHERE phone = "" OR LENGTH(phone) != 9');

        // Eliminar duplicados manteniendo solo el primer registro
        DB::statement('
            DELETE t1 FROM users t1
            INNER JOIN users t2
            WHERE t1.id > t2.id
            AND t1.phone = t2.phone
            AND t1.phone IS NOT NULL
        ');

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 9)->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->string('phone')->nullable()->change();
        });
    }
};
