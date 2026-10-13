@extends('layouts.app')

@section('title', 'AI Sentiment Analysis')

@section('content')
<link rel="stylesheet" href="{{ asset('css/sentiment.css') }}">

<div class="sentiment-page">
    <div class="sentiment-page-heading">
        <div>
            <div class="eyebrow">Dashboard / Family Overview</div>
            <h1>AI Sentiment Analysis</h1>
            <p>Review emotional language and potential concerns in caregiver visit notes.</p>
        </div>
        <div class="heading-actions">
            <a class="button button-light" href="{{ route('sentiment.index') }}">Reset filters</a>
            <a class="button button-primary" href="{{ route('sentiment.export') }}">Export report</a>
        </div>
    </div>

    @if(session('status'))
        <div class="sentiment-message">{{ session('status') }}</div>
    @endif

    @if($flaggedCount > 0)
        <section class="flag-banner" aria-live="polite">
            <div class="flag-icon">!</div>
            <div class="flag-content">
                <h2>{{ $flaggedCount }} note{{ $flaggedCount === 1 ? '' : 's' }} flagged for family review</h2>
                <p>VADER detected negative sentiment in one or more notes. Review the original observations and contact the caregiver when clarification is needed.</p>
            </div>
            <a class="button button-danger" href="#recent-results">Review notes</a>
        </section>
    @else
        <section class="calm-banner">
            <div>
                <h2>No negative-sentiment notes are currently flagged</h2>
                <p>New analyses will appear here after caregiver notes have been processed.</p>
            </div>
        </section>
    @endif

    <form class="filter-panel" method="GET" action="{{ route('sentiment.index') }}">
        <div class="filter-field">
            <label for="from">From date</label>
            <input id="from" name="from" type="date" value="{{ $filters['from'] ?? '' }}">
        </div>
        <div class="filter-field">
            <label for="to">To date</label>
            <input id="to" name="to" type="date" value="{{ $filters['to'] ?? '' }}">
        </div>
        <div class="filter-field">
            <label for="classification">Sentiment</label>
            <select id="classification" name="classification">
                <option value="">All classifications</option>
                @foreach(['negative' => 'Negative', 'neutral' => 'Neutral', 'positive' => 'Positive'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['classification'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="button button-primary" type="submit">Apply filters</button>
    </form>

    <section class="metric-grid">
        <article class="metric-card">
            <div class="metric-label">Average compound score</div>
            <div class="metric-value">{{ $averageScore === null ? '—' : number_format($averageScore, 2) }}</div>
            <div class="metric-note">Last 48 hours · range −1 to +1</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Flagged notes</div>
            <div class="metric-value">{{ number_format($flaggedCount) }}</div>
            <div class="metric-note">Negative VADER classification</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Unread family alerts</div>
            <div class="metric-value">{{ number_format($unreadAlerts) }}</div>
            <div class="metric-note">Alerts awaiting review</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Last analysis</div>
            <div class="metric-value metric-value-small">
                {{ $latestAnalysis ? \Carbon\Carbon::parse($latestAnalysis)->diffForHumans() : 'No analyses yet' }}
            </div>
            <div class="metric-note">Based on saved analysis records</div>
        </article>
    </section>

    <section class="chart-grid">
        <article class="panel chart-panel">
            <div class="panel-heading">
                <div>
                    <h2>7-day sentiment trend</h2>
                    <p>Average compound score by visit date. Empty dates are shown as gaps.</p>
                </div>
                <span class="score-key">Compound score</span>
            </div>
            <div class="chart-wrap">
                <canvas id="sentimentTrendChart" aria-label="Seven-day sentiment trend chart"></canvas>
                <p id="trendEmpty" class="chart-empty" hidden>No analysed visits in the last seven days.</p>
            </div>
        </article>

        <article class="panel chart-panel distribution-panel">
            <div class="panel-heading">
                <div>
                    <h2>Component distribution</h2>
                    <p>Saved classifications across your records.</p>
                </div>
            </div>
            <div class="distribution-list">
                @foreach(['negative' => 'Negative', 'neutral' => 'Neutral', 'positive' => 'Positive'] as $key => $label)
                    @php
                        $totalAnalyses = array_sum($distribution);
                        $percentage = $totalAnalyses > 0 ? round(($distribution[$key] / $totalAnalyses) * 100) : 0;
                    @endphp
                    <div class="distribution-row">
                        <div class="distribution-label">
                            <span class="sentiment-dot dot-{{ $key }}"></span>
                            <span>{{ $label }}</span>
                            <strong>{{ $distribution[$key] }}</strong>
                        </div>
                        <div class="distribution-track">
                            <div class="distribution-fill fill-{{ $key }}" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="distribution-percent">{{ $percentage }}%</span>
                    </div>
                @endforeach
            </div>
            <p class="privacy-note">Sentiment reflects wording, not a medical diagnosis.</p>
        </article>
    </section>

    <section class="panel results-panel" id="recent-results">
        <div class="panel-heading">
            <div>
                <h2>Recent visit-note analyses</h2>
                <p>Showing up to 100 records matching your filters.</p>
            </div>
            <span class="result-count">{{ $results->count() }} records</span>
        </div>

        @if($results->isEmpty())
            <div class="empty-state">
                <h3>No sentiment results found</h3>
                <p>Once a caregiver submits a visit note and the Python service analyses it, the saved result will appear here.</p>
            </div>
        @else
            <div class="table-scroll">
                <table class="sentiment-table">
                    <thead>
                        <tr>
                            <th>Visit date</th>
                            <th>Elder</th>
                            <th>Caregiver note</th>
                            <th>Compound</th>
                            <th>Classification</th>
                            <th>Review status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                            <tr>
                                <td>{{ $result->visit_date ? \Carbon\Carbon::parse($result->visit_date)->format('d M Y') : '—' }}</td>
                                <td>{{ $result->elder_name }}</td>
                                <td class="note-cell">{{ \Illuminate\Support\Str::limit($result->visit_note, 180) }}</td>
                                <td class="score-cell">{{ number_format((float) $result->compound_score, 4) }}</td>
                                <td><span class="sentiment-pill pill-{{ $result->classification }}">{{ ucfirst($result->classification) }}</span></td>
                                <td>
                                    @if($result->flagged)
                                        <span class="review-status review-needed">Needs review</span>
                                    @else
                                        <span class="review-status">Not flagged</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <p class="disclaimer">This feature is decision support only. Review the original note and use professional judgement; VADER can miss clinical risks that are expressed in neutral or positive language.</p>
</div>

<script>
    window.elderCareSentimentData = {
        labels: @json($trendLabels),
        scores: @json($trendScores)
    };
</script>
<script src="{{ asset('js/sentiment.js') }}" defer></script>
@endsection
