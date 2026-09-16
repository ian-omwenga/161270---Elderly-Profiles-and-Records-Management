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
        Schema::create('elder_profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('created_by')
          ->constrained('users')
          ->onDelete('cascade');

    $table->string('full_name');
    $table->date('date_of_birth');
    $table->text('medical_conditions')->nullable();
    $table->text('medications')->nullable();
    $table->text('allergies')->nullable();
    $table->string('mobility_status', 100)->nullable();
    $table->text('care_preferences')->nullable();
    $table->string('emergency_contact_name')->nullable();
    $table->string('emergency_contact_phone', 20)->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elder_profiles');
    }
};
