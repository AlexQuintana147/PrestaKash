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
        Schema::table('users', function (Blueprint $table) {
            $table->string('ghost_code', 30)->nullable()->after('is_ghost');
            $table->string('ghost_type', 20)->nullable()->after('ghost_code');
            $table->unsignedBigInteger('ghost_cashbox_id')->nullable()->after('ghost_type');
            $table->string('ghost_currency', 3)->nullable()->after('ghost_cashbox_id');

            $table->foreign('ghost_cashbox_id')->references('id')->on('cashboxes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ghost_cashbox_id']);
            $table->dropColumn(['ghost_code', 'ghost_type', 'ghost_cashbox_id', 'ghost_currency']);
        });
    }
};
