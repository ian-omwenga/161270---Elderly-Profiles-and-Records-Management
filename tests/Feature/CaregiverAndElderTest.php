<?php

namespace Tests\Feature;

use App\Models\Alert;
use App\Models\CareVisit;
use App\Models\ElderProfile;
use App\Models\SentimentResult;
use App\Models\User;
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
        $response->assertDontSee('Dr. Stephen Obonyo');

        $profileResponse = $this->actingAs($familyUser)->get(route('elder.show', $elder->id));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee($elder->full_name);
        $profileResponse->assertDontSee('Dr. Stephen Obonyo');
    }

    public function test_family_can_create_elder_profile_without_independence_score(): void
    {
        $familyUser = User::where('role', 'family')->first();
        $this->assertNotNull($familyUser);

        $response = $this->actingAs($familyUser)->post(route('elder.store'), [
            'full_name' => 'Test Created Elder ' . time(),
            'date_of_birth' => '1955-05-15',
            'gender' => 'Female',
            'blood_type' => 'O+',
            'residence' => 'Nairobi Kilimani',
            'physician_name' => 'Dr. David Kimani',
            'physician_phone' => '+254 712 345 678',
            'mobility_status' => 'Assisted',
            'emergency_contact_name' => 'Test Contact',
            'emergency_contact_phone' => '+254 722 999 888',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
    }

    public function test_family_cannot_access_visit_create_or_store(): void
    {
        $elder = ElderProfile::first();
        $familyUser = User::find($elder->created_by);

        // Family should NOT be able to access the visit create form
        $createResponse = $this->actingAs($familyUser)->get(route('visits.create', $elder->id));
        $createResponse->assertStatus(403);

        // Family should NOT be able to submit a visit record
        $storeResponse = $this->actingAs($familyUser)->post(route('visits.store', $elder->id), [
            'visit_date' => date('Y-m-d'),
            'visit_note' => 'Family attempting to write note',
        ]);
        $storeResponse->assertStatus(403);
    }

    public function test_caregiver_cannot_access_unassigned_elder(): void
    {
        $caregivers = User::where('role', 'caregiver')->get();
        if ($caregivers->count() >= 2) {
            $cg1 = $caregivers[0];
            $cg2 = $caregivers[1];

            // Elder assigned to caregiver 1
            $elder = ElderProfile::where('assigned_caregiver_id', $cg1->id)->first();
            if ($elder) {
                // Caregiver 2 tries to view or record visit for elder assigned to Caregiver 1
                $createResponse = $this->actingAs($cg2)->get(route('visits.create', $elder->id));
                $createResponse->assertStatus(403);

                $profileResponse = $this->actingAs($cg2)->get(route('elder.show', $elder->id));
                $profileResponse->assertStatus(403);
            }
        }
    }

    public function test_caregiver_can_open_visit_entry_form_for_assigned_elder(): void
    {
        $caregiver = User::where('role', 'caregiver')->first();
        $elder = ElderProfile::where('assigned_caregiver_id', $caregiver->id)->first();
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
        $elder = ElderProfile::where('assigned_caregiver_id', $caregiver->id)->first();

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

    public function test_logout_route_accepts_get_and_post_without_419(): void
    {
        $user = User::first();

        // GET logout
        $getResponse = $this->actingAs($user)->get(route('logout'));
        $getResponse->assertRedirect(route('login'));

        // POST logout
        $postResponse = $this->actingAs($user)->post(route('logout'));
        $postResponse->assertRedirect(route('login'));
    }

    public function test_elder_photo_endpoint_returns_image_stream(): void
    {
        $elder = ElderProfile::whereNotNull('photo_path')->first();
        if ($elder) {
            $response = $this->get(route('elder.photo', $elder->id));
            $response->assertStatus(200);
            $this->assertStringContainsString('image', $response->headers->get('Content-Type'));
        }
    }

    public function test_caregiver_and_admin_cannot_edit_elder_profile(): void
    {
        $elder = ElderProfile::first();
        $this->assertNotNull($elder);

        $caregiver = User::where('role', 'caregiver')->first();
        $admin = User::where('role', 'admin')->first();

        // Caregiver blocked from edit & update
        $this->actingAs($caregiver)->get(route('elder.profile.edit', $elder->id))->assertStatus(403);
        $this->actingAs($caregiver)->put(route('elder.profile.update', $elder->id), ['full_name' => 'Attempted Hack'])->assertStatus(403);

        // Admin blocked from edit & update
        $this->actingAs($admin)->get(route('elder.profile.edit', $elder->id))->assertStatus(403);
        $this->actingAs($admin)->put(route('elder.profile.update', $elder->id), ['full_name' => 'Attempted Hack'])->assertStatus(403);
    }

    public function test_login_authenticates_without_role_parameter_and_routes_to_correct_dashboard(): void
    {
        // Family login
        $famUser = User::where('role', 'family')->first();
        $famUser->update(['password' => \Illuminate\Support\Facades\Hash::make('password123')]);
        $famResponse = $this->post(route('login.submit'), [
            'email' => $famUser->email,
            'password' => 'password123',
        ]);
        $famResponse->assertRedirect(route('dashboard'));

        // Caregiver login
        $cgUser = User::where('role', 'caregiver')->first();
        $cgUser->update(['password' => \Illuminate\Support\Facades\Hash::make('password123')]);
        $cgResponse = $this->post(route('login.submit'), [
            'email' => $cgUser->email,
            'password' => 'password123',
        ]);
        $cgResponse->assertRedirect(route('caregiver.dashboard'));

        // Admin login
        $adminUser = User::where('role', 'admin')->first();
        $adminUser->update(['password' => \Illuminate\Support\Facades\Hash::make('password123')]);
        $adminResponse = $this->post(route('login.submit'), [
            'email' => $adminUser->email,
            'password' => 'password123',
        ]);
        $adminResponse->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_manage_users_and_elders_and_delete_records(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        // 1. Admin dashboard loads and sees users and elders
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('User Accounts Management');
        $response->assertSee('Elder Care Profiles Management');

        // 2. Admin can edit user minor info
        $targetUser = User::where('role', 'caregiver')->first();
        $updateUserRes = $this->actingAs($admin)->put(route('admin.users.update', $targetUser->id), [
            'name' => 'Updated Caregiver Name',
            'email' => $targetUser->email,
            'phone' => '+254 799 111 222',
            'role' => 'caregiver',
        ]);
        $updateUserRes->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['id' => $targetUser->id, 'name' => 'Updated Caregiver Name']);

        // 3. Admin can edit elder minor info (e.g. reassign caregiver & update emergency contact)
        $elder = ElderProfile::first();
        $updateElderRes = $this->actingAs($admin)->put(route('admin.elders.updateMinor', $elder->id), [
            'assigned_caregiver_id' => $targetUser->id,
            'residence' => 'Updated Admin Residence',
            'physician_name' => 'Dr. David Kimani',
            'physician_phone' => '+254 711 000 111',
            'emergency_contact_name' => 'Updated Emergency Contact',
            'emergency_contact_phone' => '+254 722 333 444',
        ]);
        $updateElderRes->assertSessionHasNoErrors();
        $this->assertDatabaseHas('elder_profiles', [
            'id' => $elder->id,
            'residence' => 'Updated Admin Residence',
            'assigned_caregiver_id' => $targetUser->id,
        ]);

        // 4. Admin can delete a temporary elder
        $tempElder = ElderProfile::create([
            'created_by' => $admin->id,
            'full_name' => 'Delete Me Elder',
            'date_of_birth' => '1950-01-01',
            'gender' => 'Male',
            'emergency_contact_name' => 'Contact',
            'emergency_contact_phone' => '12345678',
        ]);
        $delElderRes = $this->actingAs($admin)->delete(route('admin.elders.delete', $tempElder->id));
        $delElderRes->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('elder_profiles', ['id' => $tempElder->id]);

        // 5. Admin can delete a temporary user (and cannot delete self)
        $delSelfRes = $this->actingAs($admin)->delete(route('admin.users.delete', $admin->id));
        $delSelfRes->assertSessionHasErrors(['error']);

        $tempUser = User::create([
            'name' => 'Temporary User',
            'email' => 'tempuser_' . time() . '@test.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'family',
        ]);
        $delUserRes = $this->actingAs($admin)->delete(route('admin.users.delete', $tempUser->id));
        $delUserRes->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('users', ['id' => $tempUser->id]);
    }

    public function test_non_admin_cannot_access_admin_dashboard_or_actions(): void
    {
        $caregiver = User::where('role', 'caregiver')->first();
        $this->actingAs($caregiver)->get(route('admin.dashboard'))->assertStatus(403);
    }
}
