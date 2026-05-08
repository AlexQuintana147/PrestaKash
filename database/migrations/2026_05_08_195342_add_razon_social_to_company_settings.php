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
        // Aseguramos que el campo exista en la tabla de configuraciones
        DB::table('company_settings')->updateOrInsert(
            ['key' => 'company_razon_social'],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('company_settings')->where('key', 'company_razon_social')->delete();
    }
};
