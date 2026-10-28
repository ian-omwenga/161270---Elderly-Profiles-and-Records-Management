<?php

namespace App\Http\Controllers;

use App\Models\CareVisit;
use App\Models\ElderProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Role-based redirection if accessing the generic dashboard route
        if ($user->role === 'caregiver') {
            return redirect()->route('caregiver.dashboard');
        }
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch family elders
        |--------------------------------------------------------------------------
        */
        $allFamilyElders = ElderProfile::where('created_by', $user->id)->get();

        if ($allFamilyElders->isEmpty()) {
            return redirect()
                ->route('elder.create')
                ->with('info', 'Please complete an elder care profile to get started.');
        }

        $selectedId = $request->query('elder_id', $allFamilyElders->first()->id);
        $elder = $allFamilyElders->firstWhere('id', (int) $selectedId) ?? $allFamilyElders->first();
        $elder->load(['medicationList', 'allergyList', 'assignedCaregiver']);

        /*
        |--------------------------------------------------------------------------
        | Latest care visit
        |--------------------------------------------------------------------------
        */
        $latestVisit = CareVisit::where('elder_id', $elder->id)
            ->with(['caregiver', 'sentimentResult'])
            ->orderByDesc('visit_date')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Recent care visits
        |--------------------------------------------------------------------------
        */
        $recentVisits = CareVisit::where('elder_id', $elder->id)
            ->with(['caregiver', 'sentimentResult'])
            ->orderByDesc('visit_date')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Health trend data
        |--------------------------------------------------------------------------
        */
        $healthTrends = CareVisit::where('elder_id', $elder->id)
            ->orderBy('visit_date')
            ->limit(10)
            ->get([
                'visit_date',
                'mood_score',
                'appetite_score',
                'pain_level',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Active alerts for this family
        |--------------------------------------------------------------------------
        */
        $alerts = DB::table('alerts')
            ->where('family_user_id', $user->id)
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Medication adherence calculation
        |--------------------------------------------------------------------------
        */
        $medicationTotal = CareVisit::where('elder_id', $elder->id)->count();
        $medicationTaken = CareVisit::where('elder_id', $elder->id)
            ->where('medication_taken', true)
            ->count();

        $medicationAdherence = $medicationTotal > 0
            ? round(($medicationTaken / $medicationTotal) * 100)
            : 94; // fallback benchmark if newly registered

        /*
        |--------------------------------------------------------------------------
        | Health metrics
        |--------------------------------------------------------------------------
        */
        $latestMood = $latestVisit?->mood_score ?? 4;
        $latestAppetite = $latestVisit?->appetite_score ?? 4;
        $latestPain = $latestVisit?->pain_level ?? 1;

        return view('dashboard', compact(
            'elder',
            'allFamilyElders',
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
