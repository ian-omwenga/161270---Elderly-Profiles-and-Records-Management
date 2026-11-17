<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SentimentAnalysisService
{
    /**
     * Keywords that indicate clinical or behavioral concern.
     */
    protected array $clinicalAlertKeywords = [
        'refused medication',
        'refused meds',
        'refused food',
        'fell',
        'fall',
        'severe pain',
        'chest pain',
        'unresponsive',
        'disoriented',
        'confused',
        'dizziness',
        'lightheaded',
        'nauseous',
        'vomiting',
        'fever',
        'frail',
        'distressed',
        'bleeding',
    ];

    public function analyze(string $note): array
    {
        $baseUrl = rtrim((string) config('sentiment.api_url', 'http://127.0.0.1:5001'), '/');
        $timeout = (int) config('sentiment.timeout_seconds', 5);

        $lowerNote = strtolower($note);
        $foundKeywords = [];
        foreach ($this->clinicalAlertKeywords as $keyword) {
            if (str_contains($lowerNote, $keyword)) {
                $foundKeywords[] = $keyword;
            }
        }

        try {
            $response = Http::acceptJson()
                ->timeout($timeout)
                ->post($baseUrl . '/analyze', ['note' => $note]);

            if ($response->successful()) {
                $data = $response->json();

                $compound = (float) ($data['compound_score'] ?? 0.0);
                $classification = $data['classification'] ?? 'neutral';
                $flagged = (bool) ($data['flagged'] ?? false);

                // If alarming keywords exist, ensure it is flagged for family safety
                if (!empty($foundKeywords)) {
                    $flagged = true;
                    if ($compound > -0.05 && $classification !== 'negative') {
                        $classification = 'negative';
                    }
                }

                return [
                    'compound_score' => $compound,
                    'positive_score' => (float) ($data['positive_score'] ?? 0.0),
                    'neutral_score' => (float) ($data['neutral_score'] ?? 0.0),
                    'negative_score' => (float) ($data['negative_score'] ?? 0.0),
                    'classification' => $classification,
                    'flagged' => $flagged,
                    'flagged_keywords' => implode(', ', $foundKeywords),
                ];
            }

            Log::warning('Sentiment microservice returned non-200 code: ' . $response->status());
        } catch (\Throwable $e) {
            Log::warning('Sentiment microservice connection error: ' . $e->getMessage());
        }

        // Heuristic fallback if Python service is offline
        $hasConcerningKeyword = !empty($foundKeywords);
        return [
            'compound_score' => $hasConcerningKeyword ? -0.45 : 0.0,
            'positive_score' => 0.0,
            'neutral_score' => $hasConcerningKeyword ? 0.3 : 1.0,
            'negative_score' => $hasConcerningKeyword ? 0.7 : 0.0,
            'classification' => $hasConcerningKeyword ? 'negative' : 'neutral',
            'flagged' => $hasConcerningKeyword,
            'flagged_keywords' => implode(', ', $foundKeywords),
        ];
    }
}
