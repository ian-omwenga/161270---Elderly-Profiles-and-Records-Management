<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\CareVisit;
use App\Models\ElderProfile;
use App\Models\SentimentResult;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

    /**
     * Detect currently logged-in users from active Laravel session storage (file or database).
     *
     * @return array{ids: array<int, int>, last_active: array<int, string>}
     */
    private function getLoggedInUsersData(): array
    {
        $loggedInIds = [];
        $lastActive = [];
        $lifetimeSeconds = ((int) config('session.lifetime', 120)) * 60;
        $cutoffTimestamp = time() - $lifetimeSeconds;

        // Always include the currently authenticated admin
        $currentAdminId = (int) Auth::id();
        if ($currentAdminId > 0) {
            $loggedInIds[$currentAdminId] = $currentAdminId;
            $lastActive[$currentAdminId] = 'Active now';
        }

        // 1. Check file sessions in storage/framework/sessions
        $sessionDir = storage_path('framework/sessions');
        if (is_dir($sessionDir)) {
            $files = @scandir($sessionDir) ?: [];
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || $file === '.gitignore') {
                    continue;
                }
                $fullPath = $sessionDir . DIRECTORY_SEPARATOR . $file;
                if (!is_file($fullPath)) {
                    continue;
                }
                $mtime = @filemtime($fullPath);
                if ($mtime === false || $mtime < $cutoffTimestamp) {
                    continue;
                }
                $raw = @file_get_contents($fullPath);
                if (!is_string($raw) || $raw === '') {
                    continue;
                }
                // Match serialized login_web_* key pointing to integer user ID
                if (preg_match('/"login_web_[^"] transform?";i:(\d+);|"login_web_[^"]+";i:(\d+);/', $raw, $matches)) {
                    $uid = (int) ($matches[2] ?? $matches[1] ?? 0);
                    if ($uid > 0) {
                        $loggedInIds[$uid] = $uid;
                        if ($uid !== $currentAdminId) {
                            $lastActive[$uid] = Carbon::createFromTimestamp($mtime)->diffForHumans();
                        }
                    }
                }
            }
        }

        // 2. Check database sessions table if present
        try {
            if (Schema::hasTable('sessions')) {
                $dbSessions = DB::table('sessions')
                    ->whereNotNull('user_id')
                    ->where('last_activity', '>=', $cutoffTimestamp)
                    ->get(['user_id', 'last_activity']);

                foreach ($dbSessions as $sess) {
                    $uid = (int) $sess->user_id;
                    if ($uid > 0) {
                        $loggedInIds[$uid] = $uid;
                        if ($uid !== $currentAdminId && !isset($lastActive[$uid])) {
                            $lastActive[$uid] = Carbon::createFromTimestamp((int) $sess->last_activity)->diffForHumans();
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore if sessions table is not used
        }

        return [
            'ids' => array_values($loggedInIds),
            'last_active' => $lastActive,
        ];
    }

    public function index()
    {
        $this->checkAdmin();

        $users = User::with(['createdElders', 'assignedElders'])
            ->withCount(['createdElders', 'assignedElders', 'careVisits'])
            ->orderByDesc('created_at')
            ->get();

        $elders = ElderProfile::with(['creator', 'assignedCaregiver'])
            ->withCount('careVisits')
            ->orderByDesc('created_at')
            ->get();

        $allCaregivers = User::where('role', 'caregiver')->orderBy('name')->get();

        $sessionData = $this->getLoggedInUsersData();
        $loggedInUserIds = $sessionData['ids'];
        $userLastActive = $sessionData['last_active'];

        $totalUsers = $users->count();
        $totalCaregivers = $users->where('role', 'caregiver')->count();
        $totalFamilies = $users->where('role', 'family')->count();
        $totalAdmins = $users->where('role', 'admin')->count();
        $loggedInCount = count($loggedInUserIds);
        $totalElders = $elders->count();
        $unresolvedAlerts = Alert::where('is_read', false)->count();

        $avgSentiment = SentimentResult::avg('compound_score');
        $avgSentimentPercent = $avgSentiment !== null
            ? round((($avgSentiment + 1) / 2) * 100)
            : 84;

        $recentVisits = CareVisit::with(['elder', 'caregiver', 'sentimentResult'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'users',
            'elders',
            'allCaregivers',
            'loggedInUserIds',
            'userLastActive',
            'loggedInCount',
            'totalUsers',
            'totalCaregivers',
            'totalFamilies',
            'totalAdmins',
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

        return redirect(route('admin.dashboard', [], false))
            ->with('status', 'User role updated for ' . $user->name . ' to ' . ucfirst($validated['role']) . '.');
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

        return redirect(route('admin.dashboard', [], false))
            ->with('status', 'User profile updated successfully for ' . $user->name . '.');
    }

    public function deleteUser(User $user)
    {
        $this->checkAdmin();

        if ((int) $user->id === (int) Auth::id()) {
            return redirect(route('admin.dashboard', [], false))
                ->withErrors(['error' => 'You cannot delete your own active administrator account.']);
        }

        $userName = $user->name;
        $targetUserId = (int) $user->id;

        DB::transaction(function () use ($user) {
            // 1. Unassign caregiver from any elders
            ElderProfile::where('assigned_caregiver_id', $user->id)
                ->update(['assigned_caregiver_id' => null]);

            // 2. Cleanly remove care visits logged by this caregiver (caregiver_id is NOT NULL in care_visits)
            $caregiverVisitIds = CareVisit::where('caregiver_id', $user->id)->pluck('id');
            if ($caregiverVisitIds->isNotEmpty()) {
                SentimentResult::whereIn('visit_id', $caregiverVisitIds)->delete();
                Alert::whereIn('visit_id', $caregiverVisitIds)->delete();
                CareVisit::whereIn('id', $caregiverVisitIds)->delete();
            }

            // 3. Delete alerts tied directly to this family user
            Alert::where('family_user_id', $user->id)->delete();

            // 4. Delete elders created by this family user and their dependent records
            $elders = ElderProfile::where('created_by', $user->id)->get();
            foreach ($elders as $elder) {
                if ($elder->photo_path) {
                    Storage::disk('public')->delete($elder->photo_path);
                    @unlink(public_path('storage/' . $elder->photo_path));
                    @unlink(public_path($elder->photo_path));
                }
                $elderVisitIds = CareVisit::where('elder_id', $elder->id)->pluck('id');
                if ($elderVisitIds->isNotEmpty()) {
                    SentimentResult::whereIn('visit_id', $elderVisitIds)->delete();
                    Alert::whereIn('visit_id', $elderVisitIds)->delete();
                    CareVisit::whereIn('id', $elderVisitIds)->delete();
                }
                $elder->medicationList()->delete();
                $elder->allergyList()->delete();
                $elder->delete();
            }

            // 5. Delete the user record
            $user->delete();
        });

        // Terminate any active file sessions for the deleted user
        $sessionDir = storage_path('framework/sessions');
        if (is_dir($sessionDir)) {
            foreach (@scandir($sessionDir) ?: [] as $file) {
                if ($file === '.' || $file === '..' || $file === '.gitignore') {
                    continue;
                }
                $fullPath = $sessionDir . DIRECTORY_SEPARATOR . $file;
                if (is_file($fullPath)) {
                    $raw = @file_get_contents($fullPath);
                    if (is_string($raw) && preg_match('/"login_web_[^"]+";i:' . $targetUserId . ';/', $raw)) {
                        @unlink($fullPath);
                    }
                }
            }
        }

        return redirect(route('admin.dashboard', [], false))
            ->with('status', 'User profile for ' . $userName . ' was deleted permanently.');
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

        return redirect(route('admin.dashboard', [], false))
            ->with('status', 'Administrative details updated for elder ' . $elder->full_name . '.');
    }

    public function deleteElder(ElderProfile $elder)
    {
        $this->checkAdmin();

        $elderName = $elder->full_name;

        DB::transaction(function () use ($elder) {
            if ($elder->photo_path) {
                Storage::disk('public')->delete($elder->photo_path);
                @unlink(public_path('storage/' . $elder->photo_path));
                @unlink(public_path($elder->photo_path));
            }

            $visitIds = CareVisit::where('elder_id', $elder->id)->pluck('id');
            if ($visitIds->isNotEmpty()) {
                SentimentResult::whereIn('visit_id', $visitIds)->delete();
                Alert::whereIn('visit_id', $visitIds)->delete();
                CareVisit::whereIn('id', $visitIds)->delete();
            }

            $elder->medicationList()->delete();
            $elder->allergyList()->delete();
            $elder->delete();
        });

        return redirect(route('admin.dashboard', [], false))
            ->with('status', 'Elder care profile for ' . $elderName . ' was permanently deleted.');
    }
}
