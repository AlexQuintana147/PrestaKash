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
        Schema::table('investor_applications', function (Blueprint $table) {
            $table->string('currency', 3)->default('PEN')->after('occupation'); // PEN o USD
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investor_applications', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
