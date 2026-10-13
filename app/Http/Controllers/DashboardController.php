<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Find the elder profile created by this account.
        $elder = DB::table('elder_profiles')
            ->where('created_by', $userId)
            ->first();

        if (!$elder) {
            return redirect()
                ->route('elder.create')
                ->with('info', 'Please complete the elder profile first.');
        }

        // Get the latest visit and the caregiver who submitted it.
        $latestVisit = DB::table('care_visits')
            ->leftJoin('users', 'care_visits.caregiver_id', '=', 'users.id')
            ->where('care_visits.elder_id', $elder->id)
            ->select('care_visits.*', 'users.name as caregiver_name')
            ->orderByDesc('care_visits.visit_date')
            ->first();

        // Show the five most recent visits.
        $recentVisits = DB::table('care_visits')
            ->leftJoin('users', 'care_visits.caregiver_id', '=', 'users.id')
            ->where('care_visits.elder_id', $elder->id)
            ->select('care_visits.*', 'users.name as caregiver_name')
            ->orderByDesc('care_visits.visit_date')
            ->limit(5)
            ->get();

        // Get the latest ten visits for the health chart, then display them oldest first.
        $healthTrends = DB::table('care_visits')
            ->where('elder_id', $elder->id)
            ->orderByDesc('visit_date')
            ->limit(10)
            ->get(['visit_date', 'mood_score', 'appetite_score', 'pain_level'])
            ->reverse()
            ->values();

        // Only unread alerts for the signed-in family account are shown.
        $alerts = DB::table('alerts')
            ->where('family_user_id', $userId)
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Calculate adherence from visit records where medication status was recorded.
        $medicationTotal = DB::table('care_visits')
            ->where('elder_id', $elder->id)
            ->whereNotNull('medication_taken')
            ->count();

        $medicationTaken = DB::table('care_visits')
            ->where('elder_id', $elder->id)
            ->where('medication_taken', true)
            ->count();

        $medicationAdherence = $medicationTotal > 0
            ? (int) round(($medicationTaken / $medicationTotal) * 100)
            : 0;

        // Keep the summary empty until a visit has a recorded score.
        $latestMood = $latestVisit?->mood_score ?? 0;
        $latestAppetite = $latestVisit?->appetite_score ?? 0;
        $latestPain = $latestVisit?->pain_level ?? 0;

        return view('dashboard', compact(
            'elder',
            'latestVisit',
            'recentVisits',
            'healthTrends',
            'alerts',
            'medicationAdherence',
            'latestMood',
            'latestAppetite',
            'latestPain'
        ));
    }
}
