<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investment_opportunities', function (Blueprint $table) {
            $table->renameColumn('annual_return_rate', 'monthly_return_rate');
        });
    }

    public function down(): void
    {
        Schema::table('investment_opportunities', function (Blueprint $table) {
            $table->renameColumn('monthly_return_rate', 'annual_return_rate');
        });
    }
};
