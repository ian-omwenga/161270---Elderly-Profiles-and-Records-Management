<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SentimentAnalysisController extends Controller
{
    private function familyResultsQuery()
    {
        return DB::table('sentiment_results as sr')
            ->join('care_visits as cv', 'cv.id', '=', 'sr.visit_id')
            ->join('elder_profiles as ep', 'ep.id', '=', 'cv.elder_id')
            ->where('ep.created_by', Auth::id());
    }

    public function index(Request $request)
    {
        $query = $this->familyResultsQuery();

        if ($request->filled('classification') &&
            in_array($request->query('classification'), ['positive', 'neutral', 'negative'], true)) {
            $query->where('sr.classification', $request->query('classification'));
        }

        if ($request->filled('from')) {
            $query->whereDate('cv.visit_date', '>=', $request->query('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('cv.visit_date', '<=', $request->query('to'));
        }

        $results = (clone $query)
            ->select([
                'sr.id',
                'sr.compound_score',
                'sr.positive_score',
                'sr.neutral_score',
                'sr.negative_score',
                'sr.classification',
                'sr.flagged',
                'sr.created_at as analyzed_at',
                'cv.id as visit_id',
                'cv.visit_date',
                'cv.visit_note',
                'ep.full_name as elder_name',
            ])
            ->orderByDesc('cv.visit_date')
            ->limit(100)
            ->get();

        $last48HoursAverage = $this->familyResultsQuery()
            ->where('cv.visit_date', '>=', now()->subHours(48))
            ->avg('sr.compound_score');

        $distributionRows = $this->familyResultsQuery()
            ->select('sr.classification', DB::raw('COUNT(*) as total'))
            ->groupBy('sr.classification')
            ->get()
            ->keyBy('classification');

        $distribution = [
            'positive' => (int) ($distributionRows['positive']->total ?? 0),
            'neutral' => (int) ($distributionRows['neutral']->total ?? 0),
            'negative' => (int) ($distributionRows['negative']->total ?? 0),
        ];

        $trendRows = $this->familyResultsQuery()
            ->whereDate('cv.visit_date', '>=', now()->subDays(6)->toDateString())
            ->selectRaw('DATE(cv.visit_date) as day, AVG(sr.compound_score) as average_score')
            ->groupByRaw('DATE(cv.visit_date)')
            ->orderByRaw('DATE(cv.visit_date)')
            ->get()
            ->keyBy('day');

        $trendLabels = [];
        $trendScores = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $trendLabels[] = Carbon::parse($day)->format('D, d M');
            $trendScores[] = isset($trendRows[$day])
                ? round((float) $trendRows[$day]->average_score, 4)
                : null;
        }

        $flaggedCount = $this->familyResultsQuery()
            ->where('sr.flagged', true)
            ->count();

        $latestAnalysis = $this->familyResultsQuery()
            ->orderByDesc('sr.created_at')
            ->value('sr.created_at');

        $unreadAlerts = DB::table('alerts')
            ->where('family_user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('sentiment.index', [
            'results' => $results,
            'averageScore' => $last48HoursAverage === null ? null : round((float) $last48HoursAverage, 2),
            'distribution' => $distribution,
            'trendLabels' => $trendLabels,
            'trendScores' => $trendScores,
            'flaggedCount' => $flaggedCount,
            'latestAnalysis' => $latestAnalysis,
            'unreadAlerts' => $unreadAlerts,
            'filters' => $request->only(['from', 'to', 'classification']),
        ]);
    }

    public function export(): StreamedResponse
    {
        $rows = $this->familyResultsQuery()
            ->select([
                'cv.visit_date',
                'ep.full_name as elder_name',
                'cv.visit_note',
                'sr.compound_score',
                'sr.positive_score',
                'sr.neutral_score',
                'sr.negative_score',
                'sr.classification',
                'sr.flagged',
                'sr.created_at as analyzed_at',
            ])
            ->orderByDesc('cv.visit_date')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $output = fopen('php://output', 'w');
            fputcsv($output, [
                'Visit date', 'Elder', 'Visit note', 'Compound score',
                'Positive score', 'Neutral score', 'Negative score',
                'Classification', 'Flagged', 'Analysed at',
            ]);

            foreach ($rows as $row) {
                fputcsv($output, [
                    $row->visit_date,
                    $row->elder_name,
                    $row->visit_note,
                    $row->compound_score,
                    $row->positive_score,
                    $row->neutral_score,
                    $row->negative_score,
                    $row->classification,
                    $row->flagged ? 'Yes' : 'No',
                    $row->analyzed_at,
                ]);
            }
            fclose($output);
        }, 'eldercare-sentiment-report.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
