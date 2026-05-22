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
        Schema::table('investor_payouts', function (Blueprint $table) {
            $table->foreignId('cashbox_id')->nullable()->after('investor_user_id')->constrained('cashboxes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investor_payouts', function (Blueprint $table) {
            $table->dropForeign(['cashbox_id']);
            $table->dropColumn('cashbox_id');
        });
    }
};
