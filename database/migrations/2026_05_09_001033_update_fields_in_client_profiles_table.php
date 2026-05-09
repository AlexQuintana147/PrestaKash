<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            // Agregar tipo de empleo
            $table->string('employment_type')->nullable()->after('is_employed');

            // Dividir nombres del cónyuge
            $table->string('spouse_first_name')->nullable()->after('marital_status');
            $table->string('spouse_last_name')->nullable()->after('spouse_first_name');
            
            // Eliminar el campo viejo si existe
            if (Schema::hasColumn('client_profiles', 'spouse_name')) {
                $table->dropColumn('spouse_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->dropColumn(['employment_type', 'spouse_first_name', 'spouse_last_name']);
            $table->string('spouse_name')->nullable()->after('marital_status');
        });
    }
};
