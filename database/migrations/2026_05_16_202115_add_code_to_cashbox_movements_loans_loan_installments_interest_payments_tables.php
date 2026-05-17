<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cashbox_movements', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
        });

        Schema::table('loan_installments', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
        });

        Schema::table('interest_payments', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
        });

        $this->populateCodes('cashbox_movements', 'MOV');
        $this->populateCodes('loans', 'PRE');
        $this->populateCodes('loan_installments', 'CUO');
        $this->populateCodes('interest_payments', 'INT');

        Schema::table('cashbox_movements', function (Blueprint $table) {
            $table->string('code')->nullable(false)->unique()->change();
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->string('code')->nullable(false)->unique()->change();
        });

        Schema::table('loan_installments', function (Blueprint $table) {
            $table->string('code')->nullable(false)->unique()->change();
        });

        Schema::table('interest_payments', function (Blueprint $table) {
            $table->string('code')->nullable(false)->unique()->change();
        });
    }

    private function populateCodes(string $table, string $prefix): void
    {
        $rows = DB::table($table)->whereNull('code')->get();
        foreach ($rows as $row) {
            DB::table($table)
                ->where('id', $row->id)
                ->update(['code' => $prefix . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8))]);
        }
    }

    public function down(): void
    {
        Schema::table('interest_payments', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('loan_installments', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('cashbox_movements', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
