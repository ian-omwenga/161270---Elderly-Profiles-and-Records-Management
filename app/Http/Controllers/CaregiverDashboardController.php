<?php

namespace App\Http\Controllers;

use App\Models\CareVisit;
use App\Models\ElderProfile;
use Illuminate\Support\Facades\Auth;

class CaregiverDashboardController extends Controller
{
    public function index()
    {
        $caregiver = Auth::user();

        // Elders assigned specifically to this caregiver, or all active elders if not yet partitioned
        $assignedElders = ElderProfile::where('assigned_caregiver_id', $caregiver->id)
            ->with(['medicationList', 'allergyList'])
            ->get();

        if ($assignedElders->isEmpty()) {
            // Provide all registered elders for easy demonstration/home-care selection
            $assignedElders = ElderProfile::with(['medicationList', 'allergyList'])->get();
        }

        $myVisits = CareVisit::where('caregiver_id', $caregiver->id)
            ->with(['elder', 'sentimentResult'])
            ->orderByDesc('visit_date')
            ->limit(10)
            ->get();

        $stats = [
            'total_assigned_elders' => $assignedElders->count(),
            'visits_conducted' => CareVisit::where('caregiver_id', $caregiver->id)->count(),
            'visits_this_week' => CareVisit::where('caregiver_id', $caregiver->id)
                ->where('visit_date', '>=', now()->startOfWeek())
                ->count(),
            'alerts_triggered' => CareVisit::where('caregiver_id', $caregiver->id)
                ->whereHas('sentimentResult', fn($q) => $q->where('flagged', true))
                ->count(),
        ];

        return view('caregiver.dashboard', compact('caregiver', 'assignedElders', 'myVisits', 'stats'));
    }
}
