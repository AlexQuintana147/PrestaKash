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
        Schema::table('investment_opportunities', function (Blueprint $table) {
            $table->json('bank_accounts')->nullable()->after('risks');
        });

        Schema::table('investor_opportunity', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('admin_notes');
            $table->string('voucher_path')->nullable()->after('payment_method');
            $table->string('operation_number')->nullable()->after('voucher_path');
            $table->timestamp('approved_at')->nullable()->after('operation_number');
            $table->timestamp('payment_submitted_at')->nullable()->after('approved_at');
            $table->string('payment_status')->nullable()->after('payment_submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_opportunities', function (Blueprint $table) {
            $table->dropColumn('bank_accounts');
        });

        Schema::table('investor_opportunity', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'voucher_path', 'operation_number', 'approved_at', 'payment_submitted_at', 'payment_status']);
        });
    }
};
