<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->boolean('late_fee_enabled')->default(false)->after('cci');
            $table->unsignedTinyInteger('grace_days')->default(0)->after('late_fee_enabled');
            $table->decimal('late_fee_rate', 5, 2)->default(0)->after('grace_days'); // % diario
            $table->decimal('late_fee_daily_amount', 10, 2)->default(0)->after('late_fee_rate'); // Monto fijo diario
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['late_fee_enabled', 'grace_days', 'late_fee_rate', 'late_fee_daily_amount']);
        });
    }
};
