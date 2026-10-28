<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\CareVisit;
use App\Models\ElderProfile;
use App\Models\SentimentResult;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VisitController extends Controller
{
    public function index(ElderProfile $elder)
    {
        $user = Auth::user();

        // Family members can only see their own elder's visits. Caregivers and Admins have access.
        if ($user->role === 'family' && (int) $elder->created_by !== (int) $user->id) {
            abort(403, 'Unauthorized access to elder visit records.');
        }

        $visits = CareVisit::where('elder_id', $elder->id)
            ->with(['caregiver', 'sentimentResult'])
            ->orderByDesc('visit_date')
            ->get();

        return view('visits.index', compact('elder', 'visits'));
    }

    /**
     * Show the Caregiver Visit Entry form (Figure 4.10).
     */
    public function create(ElderProfile $elder)
    {
        $elder->load(['medicationList', 'allergyList', 'creator']);

        return view('visits.create', compact('elder'));
    }

    /**
     * Store a newly submitted caregiver visit and run VADER sentiment analysis.
     */
    public function store(Request $request, ElderProfile $elder)
    {
        $validated = $request->validate([
            'visit_date' => ['required', 'date'],
            'clock_in' => ['nullable', 'string', 'max:20'],
            'clock_out' => ['nullable', 'string', 'max:20'],
            'tasks' => ['nullable', 'array'],
            'tasks.*' => ['string'],
            'tasks_completed' => ['nullable', 'string'],
            'mood_score' => ['nullable', 'integer', 'min:1', 'max:5'],
            'appetite_score' => ['nullable', 'integer', 'min:1', 'max:5'],
            'pain_level' => ['nullable', 'integer', 'min:1', 'max:5'],
            'medication_taken' => ['nullable', 'boolean'],
            'urgent_concern' => ['nullable', 'boolean'],
            'visit_note' => ['nullable', 'string'],
            'cognitive_notes' => ['nullable', 'string'],
        ]);

        // Process selected care tasks
        $tasksList = [];
        if (!empty($validated['tasks'])) {
            $tasksList = $validated['tasks'];
        }
        if (!empty($validated['tasks_completed'])) {
            $tasksList = array_merge($tasksList, array_map('trim', explode(',', $validated['tasks_completed'])));
        }
        $tasksString = !empty($tasksList) ? implode(', ', array_unique($tasksList)) : null;

        $caregiverId = Auth::id();

        $visit = CareVisit::create([
            'elder_id' => $elder->id,
            'caregiver_id' => $caregiverId,
            'visit_date' => $validated['visit_date'],
            'clock_in' => $validated['clock_in'] ?? null,
            'clock_out' => $validated['clock_out'] ?? null,
            'tasks_completed' => $tasksString,
            'mood_score' => $validated['mood_score'] ?? 3,
            'appetite_score' => $validated['appetite_score'] ?? 3,
            'pain_level' => $validated['pain_level'] ?? 1,
            'medication_taken' => $request->boolean('medication_taken'),
            'urgent_concern' => $request->boolean('urgent_concern'),
            'visit_note' => $validated['visit_note'] ?? null,
            'cognitive_notes' => $validated['cognitive_notes'] ?? null,
        ]);

        // Run VADER Sentiment Analysis if notes are provided
        $fullNoteText = trim(($validated['visit_note'] ?? '') . ' ' . ($validated['cognitive_notes'] ?? ''));

        if (!empty($fullNoteText)) {
            try {
                $analysis = app(SentimentAnalysisService::class)->analyze($fullNoteText);

                $sentiment = SentimentResult::create([
                    'visit_id' => $visit->id,
                    'compound_score' => $analysis['compound_score'],
                    'positive_score' => $analysis['positive_score'],
                    'neutral_score' => $analysis['neutral_score'],
                    'negative_score' => $analysis['negative_score'],
                    'classification' => $analysis['classification'],
                    'flagged' => $analysis['flagged'],
                ]);

                // Create alert if flagged by VADER or marked as urgent concern
                if ($analysis['flagged'] || $visit->urgent_concern) {
                    $reason = $visit->urgent_concern
                        ? 'Caregiver flagged an URGENT CONCERN during visit for ' . $elder->full_name . '.'
                        : 'VADER NLP classified visit note as NEGATIVE (Score: ' . $analysis['compound_score'] . ') for ' . $elder->full_name . '.';

                    Alert::create([
                        'visit_id' => $visit->id,
                        'family_user_id' => $elder->created_by,
                        'message' => $reason . ' Observation: "' . \Illuminate\Support\Str::limit($fullNoteText, 140) . '"',
                        'is_read' => false,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Sentiment analysis pipeline error for visit ' . $visit->id, [
                    'error' => $e->getMessage(),
                ]);
            }
        } elseif ($visit->urgent_concern) {
            Alert::create([
                'visit_id' => $visit->id,
                'family_user_id' => $elder->created_by,
                'message' => 'Caregiver flagged an URGENT CONCERN during visit for ' . $elder->full_name . ' with no notes attached.',
                'is_read' => false,
            ]);
        }

        $redirectRoute = Auth::user()->role === 'caregiver'
            ? route('caregiver.dashboard')
            : route('visits.index', $elder->id);

        return redirect($redirectRoute)->with(
            'status',
            'Care visit recorded successfully. Observations have been processed and analyzed.'
        );
    }
}
