<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_documents', function (Blueprint $table) {
            $table->timestamp('rejected_at')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('user_documents', function (Blueprint $table) {
            $table->dropColumn('rejected_at');
        });
    }
};
