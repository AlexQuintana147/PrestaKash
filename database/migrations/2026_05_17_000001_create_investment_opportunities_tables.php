<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description');
            $table->decimal('target_amount', 15, 2);
            $table->decimal('min_investment', 15, 2)->default(500);
            $table->string('currency', 3)->default('PEN');
            $table->decimal('annual_return_rate', 5, 2);
            $table->integer('term_months');
            $table->text('risks')->nullable();
            $table->string('status')->default('open');
            $table->date('deadline')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('investor_opportunity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('investment_opportunity_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'investment_opportunity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investor_opportunity');
        Schema::dropIfExists('investment_opportunities');
    }
};
