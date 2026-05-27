<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE referral_withdrawals MODIFY COLUMN status ENUM('pending', 'paid', 'confirmed') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE referral_withdrawals MODIFY COLUMN status ENUM('pending', 'paid') NOT NULL DEFAULT 'pending'");
    }
};
