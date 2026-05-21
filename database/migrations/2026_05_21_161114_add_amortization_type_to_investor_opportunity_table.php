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
        Schema::table('investor_opportunity', function (Blueprint $table) {
            $table->string('amortization_type')->default('frances')->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('investor_opportunity', function (Blueprint $table) {
            $table->dropColumn('amortization_type');
        });
    }
};
