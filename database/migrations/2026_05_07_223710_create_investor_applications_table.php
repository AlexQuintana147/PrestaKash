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
        Schema::create('investor_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Datos del Inversor
            $table->string('phone');
            $table->string('occupation');
            $table->decimal('intended_amount', 15, 2);
            $table->string('source_of_funds');
            $table->text('experience')->nullable();
            $table->string('preferred_term'); // ej: 3 meses, 6 meses, 12 meses
            
            // Estado de la solicitud
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
        });

        // Opcional: Agregar un campo 'is_investor' al usuario si no existe
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_investor')) {
                $table->boolean('is_investor')->default(false)->after('role_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_applications');
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_investor')) {
                $table->dropColumn('is_investor');
            }
        });
    }
};
