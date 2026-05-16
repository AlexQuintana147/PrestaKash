<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interest_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_installment_id')->constrained()->onDelete('cascade');
            $table->string('payment_voucher');
            $table->string('payment_method');
            $table->string('bank_name')->nullable();
            $table->string('operation_number')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->timestamp('paid_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interest_payments');
    }
};
