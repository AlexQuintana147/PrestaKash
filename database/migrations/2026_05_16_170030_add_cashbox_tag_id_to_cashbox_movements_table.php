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
        Schema::table('cashbox_movements', function (Blueprint $table) {
            $table->foreignId('cashbox_tag_id')->nullable()->after('interest_payment_id')->constrained('cashbox_tags')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cashbox_movements', function (Blueprint $table) {
            $table->dropForeign(['cashbox_tag_id']);
            $table->dropColumn('cashbox_tag_id');
        });
    }
};
