<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Información académica
            $table->string('institution');           // Universidad o instituto
            $table->string('career');                // Carrera profesional
            $table->string('academic_status');       // Situación académica

            // Situación social
            $table->boolean('has_children');
            $table->string('instagram')->nullable();
            $table->boolean('is_employed');
            $table->string('marital_status');        // soltero, casado_separado, casado_compartido

            // Solo si casado con bienes compartidos
            $table->string('spouse_name')->nullable();
            $table->string('spouse_dni')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_profiles');
    }
};
