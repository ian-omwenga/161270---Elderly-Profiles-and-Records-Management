<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Allergy;
use App\Models\CareVisit;
use App\Models\ElderProfile;
use App\Models\Medication;
use App\Models\SentimentResult;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ElderCareSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create System Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@eldercare.co.ke'],
            [
                'name' => 'Sarah Jenkins (Admin)',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '+254 700 111 222',
            ]
        );

        $caregiver1 = User::firstOrCreate(
            ['email' => 'aris.caregiver@eldercare.co.ke'],
            [
                'name' => 'Dr. Aris Ouma',
                'password' => Hash::make('password123'),
                'role' => 'caregiver',
                'phone' => '+254 711 222 333',
            ]
        );

        $caregiver2 = User::firstOrCreate(
            ['email' => 'michael.caregiver@eldercare.co.ke'],
            [
                'name' => 'Michael Ross (Physio)',
                'password' => Hash::make('password123'),
                'role' => 'caregiver',
                'phone' => '+254 722 333 444',
            ]
        );

        $family1 = User::firstOrCreate(
            ['email' => 'ian.family@eldercare.co.ke'],
            [
                'name' => 'Ian Omwenga',
                'password' => Hash::make('password123'),
                'role' => 'family',
                'phone' => '+254 733 444 555',
            ]
        );

        $family2 = User::firstOrCreate(
            ['email' => 'mary.family@eldercare.co.ke'],
            [
                'name' => 'Mary Vance',
                'password' => Hash::make('password123'),
                'role' => 'family',
                'phone' => '+254 744 555 666',
            ]
        );

        // Also update any existing user 1 to ensure family access
        $existingUser1 = User::find(1);
        if ($existingUser1 && $existingUser1->role !== 'family') {
            $existingUser1->update(['role' => 'family']);
        }

        // 2. Create Elder Profiles
        $elder1 = ElderProfile::updateOrCreate(
            ['full_name' => 'James Maarifa'],
            [
                'created_by' => $family1->id,
                'assigned_caregiver_id' => $caregiver1->id,
                'date_of_birth' => Carbon::parse('1944-01-24'),
                'gender' => 'Male',
                'blood_type' => 'O+',
                'residence' => 'Lang\'ata Homes, Wing B, Nairobi',
                'physician_name' => 'Dr. Stephen Obonyo',
                'physician_phone' => '+254 700 888 999',
                'mobility_status' => 'Assisted',
                'independence_score' => 65,
                'medical_conditions' => "Hypertension (Managed)\nType 2 Diabetes\nMild Cognitive Fluctuations",
                'care_preferences' => "Low sodium & less sugary diet. Prefers morning garden strolls with assistance.",
                'emergency_contact_name' => 'Linda Maarifa (Daughter)',
                'emergency_contact_phone' => '+254 712 345 678',
            ]
        );

        // Structured medications for Elder 1
        $elder1->medicationList()->delete();
        Medication::create([
            'elder_profile_id' => $elder1->id,
            'name' => 'Lisinopril',
            'dosage' => '10mg',
            'schedule' => 'Daily (Morning)',
            'category' => 'Hypertension',
            'is_active' => true,
        ]);
        Medication::create([
            'elder_profile_id' => $elder1->id,
            'name' => 'Metformin',
            'dosage' => '500mg',
            'schedule' => 'Twice Daily (Post Meals)',
            'category' => 'Type 2 Diabetes',
            'is_active' => true,
        ]);
        Medication::create([
            'elder_profile_id' => $elder1->id,
            'name' => 'Atorvastatin',
            'dosage' => '20mg',
            'schedule' => 'Nightly',
            'category' => 'Cholesterol',
            'is_active' => true,
        ]);
        Medication::create([
            'elder_profile_id' => $elder1->id,
            'name' => 'Donepezil',
            'dosage' => '5mg',
            'schedule' => 'Nightly',
            'category' => 'Cognitive Support',
            'is_active' => true,
        ]);

        // Structured allergies for Elder 1
        $elder1->allergyList()->delete();
        Allergy::create([
            'elder_profile_id' => $elder1->id,
            'allergen' => 'Penicillin',
            'reaction' => 'Anaphylaxis',
            'severity' => 'High',
        ]);
        Allergy::create([
            'elder_profile_id' => $elder1->id,
            'allergen' => 'Peanuts',
            'reaction' => 'Hives & Facial Swelling',
            'severity' => 'Medium',
        ]);
        Allergy::create([
            'elder_profile_id' => $elder1->id,
            'allergen' => 'Latex',
            'reaction' => 'Contact Dermatitis',
            'severity' => 'Low',
        ]);

        // Elder 2: Regina Waridi
        $elder2 = ElderProfile::updateOrCreate(
            ['full_name' => 'Regina Waridi'],
            [
                'created_by' => $family2->id,
                'assigned_caregiver_id' => $caregiver2->id,
                'date_of_birth' => Carbon::parse('1948-06-15'),
                'gender' => 'Female',
                'blood_type' => 'A+',
                'residence' => 'Kilimani Greens, Nairobi',
                'physician_name' => 'Dr. Elena K',
                'physician_phone' => '+254 711 555 666',
                'mobility_status' => 'Needs Assistance',
                'independence_score' => 55,
                'medical_conditions' => "Osteoarthritis in both knees\nEczema",
                'care_preferences' => "Low impact physiotherapy exercises daily. Low-fat dietary plan.",
                'emergency_contact_name' => 'Marcus Garvy (Son)',
                'emergency_contact_phone' => '+254 722 345 678',
            ]
        );

        $elder2->medicationList()->delete();
        Medication::create([
            'elder_profile_id' => $elder2->id,
            'name' => 'Flogone',
            'dosage' => '50mg',
            'schedule' => 'Daily with lunch',
            'category' => 'Joint Pain',
            'is_active' => true,
        ]);
        Medication::create([
            'elder_profile_id' => $elder2->id,
            'name' => 'Vitamin D3 & Calcium',
            'dosage' => '1000IU',
            'schedule' => 'Morning',
            'category' => 'Bone Density',
            'is_active' => true,
        ]);

        $elder2->allergyList()->delete();
        Allergy::create([
            'elder_profile_id' => $elder2->id,
            'allergen' => 'Sulfa Antibiotics',
            'reaction' => 'Severe skin rash',
            'severity' => 'High',
        ]);

        // 3. Clear and re-populate Visits with Sentiment Results
        CareVisit::whereIn('elder_id', [$elder1->id, $elder2->id])->delete();

        // Visit 1: Positive
        $v1 = CareVisit::create([
            'elder_id' => $elder1->id,
            'caregiver_id' => $caregiver1->id,
            'visit_date' => Carbon::now()->subDays(4),
            'clock_in' => '08:30',
            'clock_out' => '13:00',
            'tasks_completed' => 'Medication Administration, Meal Preparation, Vitals Check',
            'mood_score' => 4,
            'appetite_score' => 4,
            'pain_level' => 1,
            'medication_taken' => true,
            'urgent_concern' => false,
            'visit_note' => 'Patient was in good spirits, completed morning exercises and finished breakfast happily.',
            'cognitive_notes' => 'Attentive, recognized caregivers readily and recalled daily routine.',
        ]);
        SentimentResult::create([
            'visit_id' => $v1->id,
            'compound_score' => 0.68,
            'positive_score' => 0.45,
            'neutral_score' => 0.55,
            'negative_score' => 0.0,
            'classification' => 'positive',
            'flagged' => false,
        ]);

        // Visit 2: Neutral
        $v2 = CareVisit::create([
            'elder_id' => $elder1->id,
            'caregiver_id' => $caregiver1->id,
            'visit_date' => Carbon::now()->subDays(2),
            'clock_in' => '09:00',
            'clock_out' => '14:00',
            'tasks_completed' => 'Vitals & Biometrics Check, Socialization & Companionship',
            'mood_score' => 3,
            'appetite_score' => 3,
            'pain_level' => 2,
            'medication_taken' => true,
            'urgent_concern' => false,
            'visit_note' => 'Caregiver arrived on schedule. Blood pressure was measured at 130/85 and heart rate normal. Routine afternoon reading.',
            'cognitive_notes' => 'Quiet demeanor throughout the afternoon.',
        ]);
        SentimentResult::create([
            'visit_id' => $v2->id,
            'compound_score' => 0.05,
            'positive_score' => 0.1,
            'neutral_score' => 0.9,
            'negative_score' => 0.0,
            'classification' => 'neutral',
            'flagged' => false,
        ]);

        // Visit 3: NEGATIVE / FLAGGED (Triggers VADER Alert)
        $v3 = CareVisit::create([
            'elder_id' => $elder1->id,
            'caregiver_id' => $caregiver1->id,
            'visit_date' => Carbon::now()->subHours(18),
            'clock_in' => '08:30',
            'clock_out' => '13:30',
            'tasks_completed' => 'Medication Administration, Hygiene & Grooming',
            'mood_score' => 1,
            'appetite_score' => 1,
            'pain_level' => 4,
            'medication_taken' => false,
            'urgent_concern' => true,
            'visit_note' => 'The elder appeared distressed and frail this morning, refused medication, complained of severe dizziness and fell near the bathroom.',
            'cognitive_notes' => 'Expressed disorientation regarding the time of day and rejected breakfast.',
        ]);
        SentimentResult::create([
            'visit_id' => $v3->id,
            'compound_score' => -0.74,
            'positive_score' => 0.0,
            'neutral_score' => 0.42,
            'negative_score' => 0.58,
            'classification' => 'negative',
            'flagged' => true,
        ]);

        // Trigger Alert for family user 1
        Alert::create([
            'visit_id' => $v3->id,
            'family_user_id' => $family1->id,
            'message' => 'VADER NLP classified visit note as NEGATIVE (-0.74) for James Maarifa: "The elder appeared distressed and frail this morning, refused medication, complained of severe dizziness and fell near the bathroom."',
            'is_read' => false,
        ]);

        // Also if user 1 exists, provide an alert for them
        if ($existingUser1 && $existingUser1->id !== $family1->id) {
            Alert::create([
                'visit_id' => $v3->id,
                'family_user_id' => $existingUser1->id,
                'message' => 'VADER NLP classified visit note as NEGATIVE (-0.74) for James Maarifa: "The elder appeared distressed and frail, refused medication, and fell."',
                'is_read' => false,
            ]);
        }

        // Visit 4: Recent follow-up
        $v4 = CareVisit::create([
            'elder_id' => $elder1->id,
            'caregiver_id' => $caregiver2->id,
            'visit_date' => Carbon::now()->subHours(2),
            'clock_in' => '14:00',
            'clock_out' => '17:00',
            'tasks_completed' => 'Mobility & Physio Exercises, Meal Preparation',
            'mood_score' => 3,
            'appetite_score' => 4,
            'pain_level' => 2,
            'medication_taken' => true,
            'urgent_concern' => false,
            'visit_note' => 'Gentle physio recovery exercises completed. Resting comfortably in bed, took afternoon tea and vitals have stabilized.',
            'cognitive_notes' => 'Calmer, communicating clearly with caregiver.',
        ]);
        SentimentResult::create([
            'visit_id' => $v4->id,
            'compound_score' => 0.52,
            'positive_score' => 0.38,
            'neutral_score' => 0.62,
            'negative_score' => 0.0,
            'classification' => 'positive',
            'flagged' => false,
        ]);

        // Regina Waridi visit
        $v5 = CareVisit::create([
            'elder_id' => $elder2->id,
            'caregiver_id' => $caregiver2->id,
            'visit_date' => Carbon::now()->subDay(),
            'clock_in' => '10:00',
            'clock_out' => '14:30',
            'tasks_completed' => 'Mobility & Physio Exercises, Socialization & Companionship',
            'mood_score' => 5,
            'appetite_score' => 4,
            'pain_level' => 1,
            'medication_taken' => true,
            'urgent_concern' => false,
            'visit_note' => 'Focus on lower body mobility. Progressing well with assisted walking. Cheerful and highly engaged today.',
            'cognitive_notes' => 'Great conversation about family.',
        ]);
        SentimentResult::create([
            'visit_id' => $v5->id,
            'compound_score' => 0.78,
            'positive_score' => 0.52,
            'neutral_score' => 0.48,
            'negative_score' => 0.0,
            'classification' => 'positive',
            'flagged' => false,
        ]);
    }
}
