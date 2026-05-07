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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->integer('months');
            $table->decimal('interest_rate', 5, 2)->default(20.00);
            $table->decimal('technology_fee', 12, 2);
            $table->decimal('igv', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('monthly_payment', 12, 2);
            $table->string('status')->default('pending'); // pending, approved, rejected, paid
            $table->enum('type', ['cuotas', 'unico'])->default('cuotas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
