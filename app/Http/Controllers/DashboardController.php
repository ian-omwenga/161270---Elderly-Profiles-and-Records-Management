<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        /*
        Get the elder profile belonging to the logged-in user
        */
        $elder = DB::table('elder_profiles')
            ->where('created_by', $userId)
            ->first();

        /*
        If the user has not created an elder profile yet
        */
        if (!$elder) {
            return redirect()
                ->route('elder.profile.create')
                ->with('info', 'Please complete the elder profile first.');
        }

        /*
        Latest care visit
        */
        $latestVisit = DB::table('care_visits')
            ->leftJoin('users', 'care_visits.caregiver_id', '=', 'users.id')
            ->where('care_visits.elder_id', $elder->id)
            ->select(
                'care_visits.*',
                'users.name as caregiver_name'
            )
            ->orderByDesc('care_visits.visit_date')
            ->first();

        /*
        Recent care visits
        */
        $recentVisits = DB::table('care_visits')
            ->leftJoin('users', 'care_visits.caregiver_id', '=', 'users.id')
            ->where('care_visits.elder_id', $elder->id)
            ->select(
                'care_visits.*',
                'users.name as caregiver_name'
            )
            ->orderByDesc('care_visits.visit_date')
            ->limit(5)
            ->get();

        /*
        Health trend data
        */
        $healthTrends = DB::table('care_visits')
            ->where('elder_id', $elder->id)
            ->orderBy('visit_date')
            ->limit(10)
            ->get([
                'visit_date',
                'mood_score',
                'appetite_score',
                'pain_level'
            ]);

        /*
        Active alerts
        */
        $alerts = DB::table('alerts')
            ->where('family_user_id', $userId)
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        /*
        Medication adherence based on care visits where medication_taken was recorded.
        */
        $medicationTotal = DB::table('care_visits')
            ->where('elder_id', $elder->id)
            ->whereNotNull('medication_taken')
            ->count();

        $medicationTaken = DB::table('care_visits')
            ->where('elder_id', $elder->id)
            ->where('medication_taken', true)
            ->count();

        $medicationAdherence = $medicationTotal > 0
            ? round(($medicationTaken / $medicationTotal) * 100)
            : 0;

        /*
        Latest health measurements
        */
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