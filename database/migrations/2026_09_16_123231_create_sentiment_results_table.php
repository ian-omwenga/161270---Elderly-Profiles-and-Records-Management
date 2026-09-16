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
        Schema::create('sentiment_results', function (Blueprint $table) {
    $table->id();

    $table->foreignId('visit_id')
          ->constrained('care_visits')
          ->onDelete('cascade');

    $table->float('compound_score');
    $table->float('positive_score');
    $table->float('neutral_score');
    $table->float('negative_score');

    $table->string('classification', 20)->nullable();

    $table->boolean('flagged')->default(false);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentiment_results');
    }
};
