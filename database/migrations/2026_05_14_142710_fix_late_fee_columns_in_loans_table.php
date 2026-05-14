<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'late_fee_daily_amount')) {
                $table->decimal('late_fee_daily_amount', 10, 2)->default(0)->after('late_fee_rate');
            }
            if (!Schema::hasColumn('loans', 'late_fee_type')) {
                $table->string('late_fee_type')->default('diario')->after('late_fee_daily_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['late_fee_daily_amount', 'late_fee_type']);
        });
    }
};
