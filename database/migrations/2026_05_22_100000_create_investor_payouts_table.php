<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investor_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_opportunity_id')->constrained()->onDelete('cascade');
            $table->foreignId('investor_user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('month_number');
            $table->decimal('amount', 12, 2);
            $table->decimal('interest_amount', 12, 2)->default(0);
            $table->decimal('principal_amount', 12, 2)->default(0);
            $table->string('payment_type')->default('full');
            $table->string('payment_method')->nullable();
            $table->string('voucher_path')->nullable();
            $table->string('operation_number')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['investment_opportunity_id', 'investor_user_id', 'month_number'], 'inv_pay_opp_inv_month_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investor_payouts');
    }
};
