<?php

return [
    'api_url' => env('SENTIMENT_API_URL', 'http://127.0.0.1:5001'),
    'timeout_seconds' => (int) env('SENTIMENT_API_TIMEOUT', 5),
];
