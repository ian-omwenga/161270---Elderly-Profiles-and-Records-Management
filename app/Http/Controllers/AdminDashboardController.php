<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\CareVisit;
use App\Models\ElderProfile;
use App\Models\SentimentResult;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('created_at')->paginate(15);
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
        $validated = $request->validate([
            'role' => ['required', 'in:family,caregiver,admin'],
        ]);

        $user->update(['role' => $validated['role']]);

        return back()->with('status', 'User role updated for ' . $user->name . ' to ' . ucfirst($validated['role']) . '.');
    }
}
