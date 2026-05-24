<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contract_clauses', function (Blueprint $table) {
            $table->string('box_type')->nullable()->after('contract_id');
        });
    }

    public function down(): void
    {
        Schema::table('contract_clauses', function (Blueprint $table) {
            $table->dropColumn('box_type');
        });
    }
};
