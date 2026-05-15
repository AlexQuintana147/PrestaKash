<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status'); // 'transfer', 'cash'
            $table->string('bank_name')->nullable()->after('payment_method');
            $table->string('operation_number')->nullable()->after('bank_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'bank_name', 'operation_number']);
        });
    }
};
