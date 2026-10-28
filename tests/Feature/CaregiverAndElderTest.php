<?php

namespace Tests\Feature;

use App\Models\Alert;
use App\Models\CareVisit;
use App\Models\ElderProfile;
use App\Models\SentimentResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CaregiverAndElderTest extends TestCase
{
    public function test_family_can_view_dashboard_and_dedicated_profile(): void
    {
        $elder = ElderProfile::first();
        $this->assertNotNull($elder);
        $familyUser = User::find($elder->created_by);
        $this->assertNotNull($familyUser);

        $response = $this->actingAs($familyUser)->get(route('dashboard'));
        $response->assertStatus(200);

        $profileResponse = $this->actingAs($familyUser)->get(route('elder.show', $elder->id));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee($elder->full_name);
    }

    public function test_caregiver_can_open_visit_entry_form(): void
    {
        $caregiver = User::where('role', 'caregiver')->first();
        $elder = ElderProfile::first();
        $this->assertNotNull($caregiver);
        $this->assertNotNull($elder);

        $response = $this->actingAs($caregiver)->get(route('visits.create', $elder->id));
        $response->assertStatus(200);
        $response->assertSee('Visit Entry Record');
        $response->assertSee('Care', false);
    }

    public function test_caregiver_can_submit_visit_and_trigger_vader_alert(): void
    {
        $caregiver = User::where('role', 'caregiver')->first();
        $elder = ElderProfile::first();

        $postData = [
            'visit_date' => date('Y-m-d'),
            'clock_in' => '08:30',
            'clock_out' => '13:00',
            'tasks' => ['Medication Administration', 'Meal Preparation', 'Vitals & Biometrics Check'],
            'mood_score' => 1,
            'appetite_score' => 1,
            'pain_level' => 4,
            'medication_taken' => '0',
            'urgent_concern' => '1',
            'visit_note' => 'The elder refused medication and complained of extreme dizziness after a fall.',
            'cognitive_notes' => 'Patient seemed very confused.',
        ];

        $response = $this->actingAs($caregiver)->post(route('visits.store', $elder->id), $postData);
        $response->assertRedirect(route('caregiver.dashboard'));

        $latestVisit = CareVisit::where('elder_id', $elder->id)->latest('id')->first();
        $this->assertNotNull($latestVisit);
        $this->assertEquals('The elder refused medication and complained of extreme dizziness after a fall.', $latestVisit->visit_note);

        // Check sentiment result created
        $sentiment = SentimentResult::where('visit_id', $latestVisit->id)->first();
        $this->assertNotNull($sentiment);
        $this->assertTrue($sentiment->flagged);

        // Check alert generated for the family user
        $alert = Alert::where('visit_id', $latestVisit->id)->first();
        $this->assertNotNull($alert);
        $this->assertEquals($elder->created_by, $alert->family_user_id);
    }

    public function test_caregiver_dashboard_and_sentiment_page(): void
    {
        $caregiver = User::where('role', 'caregiver')->first();
        $response = $this->actingAs($caregiver)->get(route('caregiver.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Caregiver Portal');

        $sentimentResponse = $this->actingAs($caregiver)->get(route('sentiment.index'));
        $sentimentResponse->assertStatus(200);
    }

    public function test_elder_photo_endpoint_returns_image_stream(): void
    {
        $elder = ElderProfile::whereNotNull('photo_path')->first();
        if ($elder) {
            $user = User::find($elder->created_by);
            $response = $this->actingAs($user)->get(route('elder.photo', $elder->id));
            $response->assertStatus(200);
            $this->assertStringContainsString('image', $response->headers->get('Content-Type'));
        }
    }
}
