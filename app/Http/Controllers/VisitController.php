<?php

namespace App\Http\Controllers;

use App\Models\ElderProfile;
use App\Models\CareVisit;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    /**
     * Display all care visits for an elder.
     */
    public function index(ElderProfile $elder)
    {
        // Only allow the family user who created
        // the elder profile to view the visits.
        if ($elder->created_by !== Auth::id()) {
            abort(403);
        }

        $visits = CareVisit::where('elder_id', $elder->id)
            ->with('caregiver')
            ->orderByDesc('visit_date')
            ->get();

        return view('visits.index', compact('elder', 'visits'));
    }
}