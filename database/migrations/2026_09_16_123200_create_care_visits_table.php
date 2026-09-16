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
        Schema::create('care_visits', function (Blueprint $table) {
    $table->id();

    $table->foreignId('elder_id')
          ->constrained('elder_profiles')
          ->onDelete('cascade');

    $table->foreignId('caregiver_id')
          ->constrained('users')
          ->onDelete('cascade');

    $table->timestamp('visit_date');

    $table->text('tasks_completed')->nullable();

    $table->integer('mood_score')->nullable();
    $table->integer('appetite_score')->nullable();
    $table->integer('pain_level')->nullable();

    $table->boolean('medication_taken')->default(false);

    $table->text('visit_note')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('care_visits');
    }
};
