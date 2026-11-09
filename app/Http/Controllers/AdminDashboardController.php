<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\CareVisit;
use App\Models\ElderProfile;
use App\Models\SentimentResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    private function checkAdmin(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Unauthorized access to administrator actions.');
        }
    }

    public function index()
    {
        $this->checkAdmin();

        $users = User::orderByDesc('created_at')->paginate(15, ['*'], 'users_page');
        $elders = ElderProfile::with(['creator', 'assignedCaregiver'])->orderByDesc('created_at')->paginate(15, ['*'], 'elders_page');
        $allCaregivers = User::where('role', 'caregiver')->orderBy('name')->get();

        $totalUsers = User::count();
        $totalCaregivers = User::where('role', 'caregiver')->count();
        $totalFamilies = User::where('role', 'family')->count();
        $totalElders = ElderProfile::count();
        $unresolvedAlerts = Alert::where('is_read', false)->count();

        $avgSentiment = SentimentResult::avg('compound_score');
        $avgSentimentPercent = $avgSentiment !== null
            ? round((($avgSentiment + 1) / 2) * 100)
            : 84;

        $recentVisits = CareVisit::with(['elder', 'caregiver', 'sentimentResult'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'users',
            'elders',
            'allCaregivers',
            'totalUsers',
            'totalCaregivers',
            'totalFamilies',
            'totalElders',
            'unresolvedAlerts',
            'avgSentimentPercent',
            'recentVisits'
        ));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'role' => ['required', 'in:family,caregiver,admin'],
        ]);

        $user->update(['role' => $validated['role']]);

        return back()->with('status', 'User role updated for ' . $user->name . ' to ' . ucfirst($validated['role']) . '.');
    }

    public function updateUser(Request $request, User $user)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:family,caregiver,admin'],
        ]);

        $user->update($validated);

        return back()->with('status', 'User profile updated successfully for ' . $user->name . '.');
    }

    public function deleteUser(User $user)
    {
        $this->checkAdmin();

        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot delete your own active administrator account.']);
        }

        $userName = $user->name;

        DB::transaction(function () use ($user) {
            // Unassign caregiver from any elders
            ElderProfile::where('assigned_caregiver_id', $user->id)
                ->update(['assigned_caregiver_id' => null]);

            // Unlink caregiver from visits
            CareVisit::where('caregiver_id', $user->id)
                ->update(['caregiver_id' => null]);

            // Delete alerts tied to family user
            Alert::where('family_user_id', $user->id)->delete();

            // Delete elders created by family user
            $elders = ElderProfile::where('created_by', $user->id)->get();
            foreach ($elders as $elder) {
                if ($elder->photo_path) {
                    Storage::disk('public')->delete($elder->photo_path);
                    @unlink(public_path('storage/' . $elder->photo_path));
                }
                foreach ($elder->careVisits as $visit) {
                    SentimentResult::where('visit_id', $visit->id)->delete();
                    Alert::where('visit_id', $visit->id)->delete();
                    $visit->delete();
                }
                $elder->medicationList()->delete();
                $elder->allergyList()->delete();
                $elder->delete();
            }

            $user->delete();
        });

        return back()->with('status', 'User profile for ' . $userName . ' was deleted permanently.');
    }

    public function updateElderMinor(Request $request, ElderProfile $elder)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'assigned_caregiver_id' => ['nullable', 'exists:users,id'],
            'residence' => ['nullable', 'string', 'max:255'],
            'physician_name' => ['nullable', 'string', 'max:255'],
            'physician_phone' => ['nullable', 'string', 'max:30'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:30'],
        ]);

        $elder->update($validated);

        return back()->with('status', 'Administrative details updated for elder ' . $elder->full_name . '.');
    }

    public function deleteElder(ElderProfile $elder)
    {
        $this->checkAdmin();

        $elderName = $elder->full_name;

        DB::transaction(function () use ($elder) {
            if ($elder->photo_path) {
                Storage::disk('public')->delete($elder->photo_path);
                @unlink(public_path('storage/' . $elder->photo_path));
            }

            foreach ($elder->careVisits as $visit) {
                SentimentResult::where('visit_id', $visit->id)->delete();
                Alert::where('visit_id', $visit->id)->delete();
                $visit->delete();
            }

            $elder->medicationList()->delete();
            $elder->allergyList()->delete();
            $elder->delete();
        });

        return back()->with('status', 'Elder care profile for ' . $elderName . ' was permanently deleted.');
    }
}
