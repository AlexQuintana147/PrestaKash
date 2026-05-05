<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('dni', 8)->nullable()->unique()->after('last_name');
            $table->string('department')->nullable()->after('phone');
            $table->string('province')->nullable()->after('department');
            $table->string('district')->nullable()->after('province');
            $table->string('address')->nullable()->after('district');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'dni', 'department', 'province', 'district', 'address']);
        });
    }
};
