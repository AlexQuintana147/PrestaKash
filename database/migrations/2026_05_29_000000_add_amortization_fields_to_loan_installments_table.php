<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->string('amortization_type')->nullable()->after('requested_type');
            $table->decimal('amortization_extra', 12, 2)->nullable()->after('amortization_type');
        });
    }

    public function down(): void
    {
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->dropColumn(['amortization_type', 'amortization_extra']);
        });
    }
};
