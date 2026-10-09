<?php

namespace App\Http\Controllers;

use App\Models\CareVisit;
use App\Models\ElderProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    public function index(ElderProfile $elder)
    {
        $user = Auth::user();

        // Family users can view their own profiles; caregivers can view visit records.
        $isOwner = (int) $elder->created_by === (int) $user->id;
        $isCaregiver = ($user->role ?? null) === 'caregiver';

        abort_unless($isOwner || $isCaregiver, 403);

        $visits = CareVisit::with('caregiver')
            ->where('elder_id', $elder->id)
            ->orderByDesc('visit_date')
            ->get();

        return view('visits.index', compact('elder', 'visits'));
    }

    public function create(ElderProfile $elder)
    {
        $this->ensureCanRecordVisit($elder);

        return view('visits.create', compact('elder'));
    }

    public function store(Request $request, ElderProfile $elder)
    {
        $this->ensureCanRecordVisit($elder);

        $validated = $request->validate([
            'visit_date' => ['required', 'date'],
            'tasks_completed' => ['nullable', 'string', 'max:5000'],
            'mood_score' => ['required', 'integer', 'between:1,5'],
            'appetite_score' => ['required', 'integer', 'between:1,5'],
            'pain_level' => ['required', 'integer', 'between:1,5'],
            'visit_note' => ['required', 'string', 'max:10000'],
        ]);

        $validated['elder_id'] = $elder->id;
        $validated['caregiver_id'] = Auth::id();
        $validated['medication_taken'] = $request->boolean('medication_taken');

        CareVisit::create($validated);

        return redirect()->route('visits.index', $elder)
            ->with('success', 'The care visit has been saved.');
    }

    private function ensureCanRecordVisit(ElderProfile $elder): void
    {
        $user = Auth::user();
        $isOwner = (int) $elder->created_by === (int) $user->id;
        $isCaregiver = ($user->role ?? null) === 'caregiver';

        // Caregiver assignment should be checked here once the project has that relationship.
        abort_unless($isOwner || $isCaregiver, 403);
    }
}
