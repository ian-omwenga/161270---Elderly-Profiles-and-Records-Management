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
        Schema::table('elder_profiles', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('full_name');
            $table->string('gender', 20)->nullable()->after('date_of_birth');
            $table->string('blood_type', 10)->nullable()->after('gender');
            $table->string('residence')->nullable()->after('blood_type');
            $table->string('physician_name')->nullable()->after('residence');
            $table->string('physician_phone', 30)->nullable()->after('physician_name');
            $table->unsignedSmallInteger('independence_score')->default(70)->after('mobility_status');
            $table->foreignId('assigned_caregiver_id')
                ->nullable()
                ->after('created_by')
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elder_profile_id')
                ->constrained('elder_profiles')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('dosage')->nullable();
            $table->string('schedule')->nullable();
            $table->string('category')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elder_profile_id')
                ->constrained('elder_profiles')
                ->cascadeOnDelete();
            $table->string('allergen');
            $table->string('reaction')->nullable();
            $table->string('severity', 20)->default('Medium'); // High, Medium, Low
            $table->timestamps();
        });

        Schema::table('care_visits', function (Blueprint $table) {
            $table->string('clock_in', 20)->nullable()->after('visit_date');
            $table->string('clock_out', 20)->nullable()->after('clock_in');
            $table->text('cognitive_notes')->nullable()->after('visit_note');
            $table->boolean('urgent_concern')->default(false)->after('medication_taken');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('care_visits', function (Blueprint $table) {
            $table->dropColumn(['clock_in', 'clock_out', 'cognitive_notes', 'urgent_concern']);
        });

        Schema::dropIfExists('allergies');
        Schema::dropIfExists('medications');

        Schema::table('elder_profiles', function (Blueprint $table) {
            $table->dropForeign(['assigned_caregiver_id']);
            $table->dropColumn([
                'photo_path',
                'gender',
                'blood_type',
                'residence',
                'physician_name',
                'physician_phone',
                'independence_score',
                'assigned_caregiver_id',
            ]);
        });
    }
};
