# ElderCare VADER Sentiment Integration

This is an integration pack for the existing Laravel ElderCare application. It adds a local Python Flask API that runs NLTK VADER, Laravel integration files, and an analysis dashboard styled to match the supplied design.

## Architecture

1. A caregiver submits a visit note in Laravel.
2. Laravel sends the note to the local Flask API.
3. Flask returns VADER positive, neutral, negative, and compound scores plus a classification.
4. Laravel saves the result in `sentiment_results`.
5. Negative classifications can create a row in `alerts` for the family member.
6. The family dashboard reads actual saved results from PostgreSQL.

The synthetic dataset is for evaluation, not for training VADER. The evaluation script expects `caregiver_notes_dataset.csv` with columns `visit_note` and `true_label`, where labels are `negative`, `neutral`, or `positive`.

## 1. Start with a feature branch

From the Laravel project root in VS Code terminal:

```powershell
git status
git switch -c feature/vader-sentiment
```

If you already have a branch with this name, use `git switch feature/vader-sentiment` instead. Do not switch branches with uncommitted changes unless you intend to carry those changes with you.

## 2. Run the Python service

Open a second VS Code terminal and navigate to the `sentiment-service` folder after copying it into your project root.

Windows PowerShell:

```powershell
cd sentiment-service
py -m venv .venv
.\.venv\Scripts\Activate.ps1
python -m pip install --upgrade pip
pip install -r requirements.txt
python app.py
```

The API listens on `http://127.0.0.1:5001`. Keep this terminal running while you use Laravel. Test in another terminal:

```powershell
Invoke-RestMethod http://127.0.0.1:5001/health
```

Test an analysis:

```powershell
$body = @{ note = "The elder was cheerful and took medication as prescribed." } | ConvertTo-Json
Invoke-RestMethod -Uri http://127.0.0.1:5001/analyze -Method Post -ContentType "application/json" -Body $body
```

If PowerShell blocks activation, use this for the current terminal only:

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\.venv\Scripts\Activate.ps1
```

## 3. Copy Laravel files into the correct locations

Copy the contents of `laravel-overlay` into the actual Laravel root, the folder that contains `artisan`.

- `config/sentiment.php` -> `config/sentiment.php`
- `app/Services/SentimentAnalysisService.php` -> `app/Services/SentimentAnalysisService.php`
- `app/Models/SentimentResult.php` -> `app/Models/SentimentResult.php`
- `app/Http/Controllers/SentimentAnalysisController.php` -> `app/Http/Controllers/SentimentAnalysisController.php`
- `resources/views/sentiment/index.blade.php` -> `resources/views/sentiment/index.blade.php`
- `public/css/sentiment.css` -> `public/css/sentiment.css`
- `public/js/sentiment.js` -> `public/js/sentiment.js`

Merge the route lines from `routes-sentiment.php` into `routes/web.php`; do not replace your existing routes.

Add the environment variable to the Laravel `.env`:

```env
SENTIMENT_API_URL=http://127.0.0.1:5001
```

Then run:

```powershell
php artisan config:clear
php artisan route:list --name=sentiment
```

## 4. Add the dashboard routes

The route file provided is a snippet. Merge its `use` statement and route definitions into your existing `routes/web.php`. Keep the routes inside the existing `auth` middleware group.

## 5. Connect analysis to caregiver visit submission

Do not replace your whole existing `VisitController`. Follow `INTEGRATE_VISIT_CONTROLLER.md` and add the analysis block immediately after a visit is successfully created. It assumes your visit model is `CareVisit`, the created record is in `$visit`, and the related elder is in `$elder`. Adjust those two variable names if your controller uses different names.

Your database should already have:
- `care_visits.visit_note`, `care_visits.elder_id`, and `care_visits.visit_date`
- `elder_profiles.created_by` as the family user's ID
- `sentiment_results.visit_id`, `compound_score`, `positive_score`, `neutral_score`, `negative_score`, `classification`, and `flagged`
- `alerts.visit_id`, `family_user_id`, `message`, `is_read`

If the `sentiment_results` migration has not been run, check it before proceeding:

```powershell
php artisan migrate:status
php artisan migrate
```

## 6. Open the dashboard

Start Laravel in one terminal:

```powershell
php artisan serve
```

Open `http://127.0.0.1:8000/sentiment-analysis` while signed in as a family account that owns an elder profile with analyzed visits.

The dashboard is database-driven. It will show empty states until visit notes have been analyzed and stored.

## 7. Evaluate your synthetic dataset

Place `caregiver_notes_dataset.csv` in the `sentiment-service` folder. Activate the Python virtual environment and run:

```powershell
python evaluate_dataset.py
```

It prints accuracy, per-class precision/recall/F1 and a confusion matrix. It also saves `vader_confusion_matrix.png` and `vader_predictions.csv`.

## Important limitation

VADER measures language sentiment, not clinical severity. A positive note can still mention a medical issue, and a negative note is not proof of deterioration. Use flags to prompt family/caregiver review, and keep urgent clinical concerns on a separate explicit safety/escalation path.
