<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('person_type')->default('natural')->after('email');
            $table->string('ruc', 11)->nullable()->after('person_type');
            $table->string('business_name')->nullable()->after('ruc');
        });

        // Convert existing users to natural
        DB::table('users')->update(['person_type' => 'natural']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['person_type', 'ruc', 'business_name']);
        });
    }
};
