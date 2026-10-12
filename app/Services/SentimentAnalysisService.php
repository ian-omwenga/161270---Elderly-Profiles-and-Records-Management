<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SentimentAnalysisService
{
    public function analyze(string $note): array
    {
        $baseUrl = rtrim((string) config('sentiment.api_url'), '/');

        $response = Http::acceptJson()
            ->timeout((int) config('sentiment.timeout_seconds', 5))
            ->post($baseUrl . '/analyze', ['note' => $note]);

        if (!$response->successful()) {
            throw new RuntimeException(
                'Sentiment service returned HTTP ' . $response->status()
            );
        }

        $result = $response->json();

        foreach ([
            'positive_score',
            'neutral_score',
            'negative_score',
            'compound_score',
            'classification',
            'flagged',
        ] as $key) {
            if (!array_key_exists($key, $result)) {
                throw new RuntimeException('Sentiment response is missing: ' . $key);
            }
        }

        if (!in_array($result['classification'], ['positive', 'neutral', 'negative'], true)) {
            throw new RuntimeException('Sentiment service returned an invalid classification.');
        }

        return $result;
    }
}
