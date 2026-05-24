<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_clauses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('content');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['contract_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_clauses');
    }
};
