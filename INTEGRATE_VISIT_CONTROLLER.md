# Connect VADER to caregiver visit submission

Do not overwrite your current `VisitController`. Keep your existing validation, authorization checks, and visit creation code. Add the following imports at the top:

```php
use App\Models\SentimentResult;
use App\Models\Alert;
use App\Services\SentimentAnalysisService;
use Illuminate\Support\Facades\Log;
```

Immediately after your existing code successfully creates a `$visit`, run the following. Adapt `$visit` and `$elder` to the variable names already used in your controller.

```php
if (filled($visit->visit_note)) {
    try {
        $analysis = app(SentimentAnalysisService::class)->analyze($visit->visit_note);

        SentimentResult::create([
            'visit_id' => $visit->id,
            'compound_score' => $analysis['compound_score'],
            'positive_score' => $analysis['positive_score'],
            'neutral_score' => $analysis['neutral_score'],
            'negative_score' => $analysis['negative_score'],
            'classification' => $analysis['classification'],
            'flagged' => $analysis['flagged'],
        ]);

        if ($analysis['flagged']) {
            Alert::create([
                'visit_id' => $visit->id,
                'family_user_id' => $elder->created_by,
                'message' => 'A caregiver visit note for ' . $elder->full_name
                    . ' was classified as negative by VADER and needs review.',
                'is_read' => false,
            ]);
        }
    } catch (\Throwable $exception) {
        // The visit remains saved if the Python service is temporarily unavailable.
        Log::warning('VADER analysis failed for visit ' . $visit->id, [
            'error' => $exception->getMessage(),
        ]);
    }
}
```

If your current `store()` method does not have an `$elder` variable, retrieve the elder from the saved visit using your existing relationship or `ElderProfile::findOrFail($visit->elder_id)`. Do not trust an arbitrary elder ID from the request; keep your existing authorization check.

## Required Alert model

If `app/Models/Alert.php` already exists, keep it and confirm that `visit_id`, `family_user_id`, `message`, and `is_read` are fillable. Otherwise, create it:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'visit_id',
        'family_user_id',
        'message',
        'is_read',
    ];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }
}
```

If your `CareVisit` model uses another class name, adjust the relationship in `SentimentResult`. The dashboard itself uses SQL joins and does not depend on that relationship.

## Verify after integration

1. Start Flask in one terminal: `python app.py`.
2. Start Laravel in another terminal: `php artisan serve`.
3. Sign in as a caregiver and submit a test note.
4. Check the `sentiment_results` table for the scores.
5. For a negative test note, check `alerts` for a row for the elder's family user.
6. Sign in as that family user and open `/sentiment-analysis`.

Example test notes:
- Positive: `The elder was cheerful, enjoyed lunch, and took medication as prescribed.`
- Neutral: `The caregiver arrived at 10 AM and recorded the daily visit.`
- Negative: `The elder appeared distressed, refused medication, and complained of severe pain.`

These are functional smoke tests, not proof that VADER detects every clinically important concern.
