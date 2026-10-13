<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Overview | ElderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="app-shell">
    <header class="topbar">
        <a class="brand" href="{{ route('dashboard') }}" aria-label="ElderCare home">
            <span class="brand-mark">E</span>
            <span class="brand-copy"><strong>ElderCare</strong><small>Care, connected.</small></span>
        </a>

        <div class="topbar-tools">
            <div class="breadcrumb"><span>Workspace</span><span class="crumb-divider">/</span><strong>Family overview</strong></div>
            <div class="topbar-user">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                <div class="user-copy"><strong>{{ auth()->user()->name ?? 'Account' }}</strong><span>Family account</span></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-button" type="submit">Sign out</button>
                </form>
            </div>
        </div>
    </header>

    <main class="page-content">
        @if (session('success'))
            <div class="flash flash-success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('info'))
            <div class="flash flash-info" role="status">{{ session('info') }}</div>
        @endif

        <section class="page-heading">
            <div>
                <p class="eyebrow">YOUR CARE SPACE</p>
                <h1>Family overview</h1>
                <p class="page-subtitle">A clear view of your loved one's care, health and recent activity.</p>
            </div>
            <div class="today-chip"><span class="status-dot"></span> Care records at a glance</div>
        </section>

        <section class="elder-hero">
            <div class="elder-identity">
                <div class="elder-avatar">{{ strtoupper(substr($elder->full_name ?? 'E', 0, 1)) }}</div>
                <div class="elder-copy">
                    <p class="eyebrow">ELDER PROFILE</p>
                    <h2>{{ $elder->full_name }}</h2>
                    <p class="elder-condition">
                        {{ filled($elder->medical_conditions ?? null) ? $elder->medical_conditions : 'No medical conditions recorded yet.' }}
                    </p>
                    <div class="elder-meta">
                        <span><i class="meta-dot"></i> Profile connected</span>
                        <span>Mobility: {{ $elder->mobility_status ?: 'Not recorded' }}</span>
                    </div>
                </div>
            </div>
            <div class="hero-actions">
                @if (\Illuminate\Support\Facades\Route::has('elder.profile.edit'))
                    <a class="button button-primary" href="{{ route('elder.profile.edit', $elder->id) }}">Edit profile</a>
                @endif
                @if (\Illuminate\Support\Facades\Route::has('visits.index'))
                    <a class="button button-secondary" href="{{ route('visits.index', $elder->id) }}">View visits</a>
                @endif
            </div>
        </section>

        <section class="section-block">
            <div class="section-heading">
                <div><p class="eyebrow">LATEST CHECK-IN</p><h2>Health snapshot</h2></div>
                <p class="section-note">Based on the most recent recorded care visit</p>
            </div>

            <div class="summary-grid">
                <article class="metric-card metric-mood">
                    <div class="metric-top"><span class="metric-icon">M</span><span class="metric-label">Mood</span></div>
                    <div class="metric-value">{{ $latestVisit && $latestVisit->mood_score !== null ? $latestMood . '/5' : '—' }}</div>
                    <p class="metric-foot">Latest recorded mood</p>
                    <div class="metric-track"><span style="width: {{ $latestMood > 0 ? ($latestMood / 5) * 100 : 0 }}%"></span></div>
                </article>
                <article class="metric-card metric-appetite">
                    <div class="metric-top"><span class="metric-icon">A</span><span class="metric-label">Appetite</span></div>
                    <div class="metric-value">{{ $latestVisit && $latestVisit->appetite_score !== null ? $latestAppetite . '/5' : '—' }}</div>
                    <p class="metric-foot">Latest recorded appetite</p>
                    <div class="metric-track"><span style="width: {{ $latestAppetite > 0 ? ($latestAppetite / 5) * 100 : 0 }}%"></span></div>
                </article>
                <article class="metric-card metric-pain">
                    <div class="metric-top"><span class="metric-icon">P</span><span class="metric-label">Pain level</span></div>
                    <div class="metric-value">{{ $latestVisit && $latestVisit->pain_level !== null ? $latestPain . '/5' : '—' }}</div>
                    <p class="metric-foot">Latest recorded pain</p>
                    <div class="metric-track"><span style="width: {{ $latestPain > 0 ? ($latestPain / 5) * 100 : 0 }}%"></span></div>
                </article>
                <article class="metric-card metric-medication">
                    <div class="metric-top"><span class="metric-icon">Rx</span><span class="metric-label">Medication recorded</span></div>
                    <div class="metric-value">{{ $medicationAdherence }}<span class="value-unit">%</span></div>
                    <p class="metric-foot">Medication marked taken during recorded visits</p>
                    <div class="metric-track"><span style="width: {{ $medicationAdherence }}%"></span></div>
                </article>
            </div>
        </section>

        <section class="main-grid">
            <article class="panel chart-panel">
                <div class="panel-heading">
                    <div><p class="eyebrow">WELLBEING OVER TIME</p><h2>Health trends</h2><p class="panel-subtitle">Mood, appetite and pain scores from recorded visits.</p></div>
                    <span class="panel-badge">Scale: 1–5</span>
                </div>
                @if ($healthTrends->isNotEmpty())
                    <div class="chart-wrap"><canvas id="healthChart" aria-label="Health trends chart"></canvas></div>
                @else
                    <div class="empty-state chart-empty">
                        <span class="empty-symbol">+</span>
                        <h3>Health trends will appear here</h3>
                        <p>Once a caregiver records a visit, the scores will be shown in this chart.</p>
                    </div>
                @endif
            </article>

            <article class="panel alerts-panel">
                <div class="panel-heading">
                    <div><p class="eyebrow">NEEDS ATTENTION</p><h2>Recent alerts</h2><p class="panel-subtitle">Unread notifications for this account.</p></div>
                    <span class="count-badge">{{ $alerts->count() }}</span>
                </div>
                @forelse ($alerts as $alert)
                    <div class="alert-item">
                        <span class="alert-indicator"></span>
                        <div class="alert-body">
                            <div class="alert-title-row"><h3>Care alert</h3><time>{{ \Illuminate\Support\Carbon::parse($alert->created_at)->diffForHumans() }}</time></div>
                            <p>{{ $alert->message }}</p>
                            @if (\Illuminate\Support\Facades\Route::has('alerts.read'))
                                <form method="POST" action="{{ route('alerts.read', $alert->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-action" type="submit">Mark as read</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state compact-empty">
                        <span class="empty-symbol">✓</span>
                        <h3>You're all caught up</h3>
                        <p>There are no unread alerts right now.</p>
                    </div>
                @endforelse
            </article>
        </section>

        <section class="panel visits-panel">
            <div class="panel-heading">
                <div><p class="eyebrow">CARE ACTIVITY</p><h2>Recent caregiver visits</h2><p class="panel-subtitle">Notes and observations from the latest visits.</p></div>
                @if (\Illuminate\Support\Facades\Route::has('visits.index'))
                    <a class="text-action" href="{{ route('visits.index', $elder->id) }}">View visit history <span aria-hidden="true">→</span></a>
                @endif
            </div>

            <div class="visit-list">
                @forelse ($recentVisits as $visit)
                    <article class="visit-row">
                        <div class="caregiver-avatar">{{ strtoupper(substr($visit->caregiver_name ?? 'C', 0, 1)) }}</div>
                        <div class="visit-main">
                            <div class="visit-title-row">
                                <h3>{{ $visit->caregiver_name ?? 'Caregiver' }}</h3>
                                <time>{{ \Illuminate\Support\Carbon::parse($visit->visit_date)->format('d M Y, H:i') }}</time>
                            </div>
                            <p class="visit-note">{{ $visit->visit_note ?: 'No visit note was recorded.' }}</p>
                            <div class="visit-tags">
                                <span>Mood <strong>{{ $visit->mood_score ?? '—' }}/5</strong></span>
                                <span>Appetite <strong>{{ $visit->appetite_score ?? '—' }}/5</strong></span>
                                <span>Pain <strong>{{ $visit->pain_level ?? '—' }}/5</strong></span>
                                <span class="{{ $visit->medication_taken ? 'tag-positive' : 'tag-neutral' }}">Medication <strong>{{ $visit->medication_taken ? 'Taken' : 'Not recorded as taken' }}</strong></span>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        <span class="empty-symbol">+</span>
                        <h3>No visits recorded yet</h3>
                        <p>Caregiver visit records will appear here after the first visit is saved.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="profile-grid">
            <article class="panel">
                <div class="panel-heading">
                    <div><p class="eyebrow">PERSONAL DETAILS</p><h2>Elder profile</h2></div>
                    @if (\Illuminate\Support\Facades\Route::has('elder.profile.edit'))
                        <a class="text-action" href="{{ route('elder.profile.edit', $elder->id) }}">Edit details</a>
                    @endif
                </div>
                <dl class="details-grid">
                    <div><dt>Full name</dt><dd>{{ $elder->full_name }}</dd></div>
                    <div><dt>Date of birth</dt><dd>{{ $elder->date_of_birth ? \Illuminate\Support\Carbon::parse($elder->date_of_birth)->format('d M Y') : 'Not recorded' }}</dd></div>
                    <div><dt>Mobility</dt><dd>{{ $elder->mobility_status ?: 'Not recorded' }}</dd></div>
                    <div><dt>Emergency contact</dt><dd>{{ $elder->emergency_contact_name ?: 'Not recorded' }}</dd></div>
                    <div class="detail-wide"><dt>Emergency phone</dt><dd>{{ $elder->emergency_contact_phone ?: 'Not recorded' }}</dd></div>
                </dl>
            </article>

            <article class="panel">
                <div class="panel-heading"><div><p class="eyebrow">CARE NOTES</p><h2>Medical information</h2></div></div>
                <div class="medical-list">
                    <div><h3>Medical conditions</h3><p>{{ $elder->medical_conditions ?: 'No conditions recorded.' }}</p></div>
                    <div><h3>Medications</h3><p>{{ $elder->medications ?: 'No medications recorded.' }}</p></div>
                    <div><h3>Allergies</h3><p>{{ $elder->allergies ?: 'No allergies recorded.' }}</p></div>
                    <div><h3>Care preferences</h3><p>{{ $elder->care_preferences ?: 'No preferences recorded.' }}</p></div>
                </div>
            </article>
        </section>

        <footer class="page-footer"><span>ElderCare</span><span>Helping families stay connected to care.</span></footer>
    </main>
</div>

@if ($healthTrends->isNotEmpty())
<script>
    const trendData = @json($healthTrends);
    const labels = trendData.map(item => new Date(item.visit_date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short'
    }));

    const chartCanvas = document.getElementById('healthChart');

    new Chart(chartCanvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Mood',
                    data: trendData.map(item => item.mood_score),
                    borderColor: '#4777e8',
                    backgroundColor: 'rgba(71, 119, 232, 0.12)',
                    pointBackgroundColor: '#4777e8',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.38,
                    fill: false,
                    spanGaps: true
                },
                {
                    label: 'Appetite',
                    data: trendData.map(item => item.appetite_score),
                    borderColor: '#16a085',
                    backgroundColor: 'rgba(22, 160, 133, 0.10)',
                    pointBackgroundColor: '#16a085',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.38,
                    fill: false,
                    spanGaps: true
                },
                {
                    label: 'Pain',
                    data: trendData.map(item => item.pain_level),
                    borderColor: '#e47b62',
                    backgroundColor: 'rgba(228, 123, 98, 0.10)',
                    pointBackgroundColor: '#e47b62',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.38,
                    fill: false,
                    spanGaps: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            animation: { duration: 700, easing: 'easeOutQuart' },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        boxHeight: 8,
                        padding: 22,
                        color: '#64748b',
                        font: { family: 'DM Sans', size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: '#172033',
                    padding: 12,
                    cornerRadius: 10,
                    titleFont: { family: 'DM Sans', weight: '700' },
                    bodyFont: { family: 'DM Sans' }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { family: 'DM Sans', size: 11 } },
                    border: { display: false }
                },
                y: {
                    min: 0,
                    max: 5,
                    ticks: { stepSize: 1, color: '#94a3b8', font: { family: 'DM Sans', size: 11 } },
                    grid: { color: '#edf1f6', drawBorder: false },
                    border: { display: false }
                }
            }
        }
    });
</script>
@endif
</body>
</html>
