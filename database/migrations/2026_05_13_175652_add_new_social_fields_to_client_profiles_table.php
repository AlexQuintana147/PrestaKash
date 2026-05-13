<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->integer('children_count')->nullable()->after('has_children');
            $table->string('workplace')->nullable()->after('employment_type');
            $table->string('job_title')->nullable()->after('workplace');
            $table->decimal('monthly_income', 12, 2)->nullable()->after('job_title');
            $table->string('independent_type')->nullable()->after('monthly_income');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->dropColumn(['children_count', 'workplace', 'job_title', 'monthly_income', 'independent_type']);
        });
    }
};
