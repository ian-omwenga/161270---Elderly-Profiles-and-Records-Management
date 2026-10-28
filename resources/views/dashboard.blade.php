@extends('layouts.app')

@section('title', 'Family Dashboard - ' . $elder->full_name)
@section('page-heading', 'Family Overview')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .app-container { min-height: auto; }
        .dashboard-content { max-width: 1450px; margin: 0 auto; padding: 10px 0 30px; }
        .elder-avatar { width: 76px; height: 76px; border-radius: 50%; overflow: hidden; position: relative; background: #e8efff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .elder-avatar-img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block; }
        .view-profile-btn { background: #0f172a; color: #ffffff !important; }
        .view-profile-btn:hover { background: #1e293b; }
        .alert-dismiss-btn { border: none; background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 11px; cursor: pointer; }
    </style>
@endpush

@section('content')
<div class="dashboard-content">

    <!-- ELDER SWITCHER IF MULTIPLE -->
    @if(isset($allFamilyElders) && $allFamilyElders->count() > 1)
        <div style="margin-bottom: 20px; display:flex; align-items:center; gap:12px; background:#fff; padding:12px 18px; border-radius:14px; border:1px solid #e2e8f0;">
            <span style="font-size:11px; font-weight:800; color:#64748b; text-transform:uppercase;">Select Elder:</span>
            <div style="display:flex; gap:8px;">
                @foreach($allFamilyElders as $famElder)
                    <a href="{{ route('dashboard', ['elder_id' => $famElder->id]) }}" 
                       style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none; {{ $famElder->id === $elder->id ? 'background:#2563eb; color:#fff;' : 'background:#f1f5f9; color:#475569;' }}">
                        {{ $famElder->full_name }}
                    </a>
                @endforeach
            </div>
            <a href="{{ route('elder.create') }}" style="margin-left:auto; font-size:11px; font-weight:700; color:#2563eb; text-decoration:none;">
                + Add Another Elder
            </a>
        </div>
    @endif

    <!-- ELDER HEADER -->
    <section class="elder-header">
        <div class="elder-information">
            <div class="elder-avatar">
                @if($elder->photo_url)
                    <img src="{{ $elder->photo_url }}" alt="{{ $elder->full_name }}" class="elder-avatar-img">
                @else
                    {{ strtoupper(substr($elder->full_name, 0, 1)) }}
                @endif
            </div>

            <div>
                <p class="section-label">ELDER CARE PROFILE</p>
                <h1>{{ $elder->full_name }}</h1>
                <p class="elder-description">
                    {{ $elder->age ? $elder->age . ' Years Old' : 'Elder Patient' }}
                    @if($elder->residence) · {{ $elder->residence }} @endif
                    @if($elder->medical_conditions) · {{ \Illuminate\Support\Str::limit($elder->medical_conditions, 90) }} @endif
                </p>
            </div>
        </div>

        <div class="elder-actions">
            <a href="{{ route('elder.show', $elder->id) }}" class="primary-button view-profile-btn">
                View Full Profile
            </a>
            <a href="{{ route('elder.profile.edit', $elder->id) }}" class="secondary-button">
                Edit Record
            </a>
            <a href="{{ route('visits.index', $elder->id) }}" class="secondary-button">
                Visit History
            </a>
        </div>
    </section>

    <!-- HEALTH SUMMARY CARDS -->
    <section class="summary-grid">
        <div class="summary-card">
            <p class="card-label">MOOD</p>
            <h2>{{ $latestMood }}/5</h2>
            <p class="card-description">Latest recorded mood</p>
        </div>

        <div class="summary-card">
            <p class="card-label">APPETITE</p>
            <h2>{{ $latestAppetite }}/5</h2>
            <p class="card-description">Latest recorded appetite</p>
        </div>

        <div class="summary-card">
            <p class="card-label">PAIN LEVEL</p>
            <h2>{{ $latestPain }}/5</h2>
            <p class="card-description">Latest recorded pain</p>
        </div>

        <div class="summary-card">
            <p class="card-label">MEDICATION ADHERENCE</p>
            <h2>{{ $medicationAdherence }}%</h2>
            <p class="card-description">Based on recorded visits</p>
        </div>
    </section>

    <!-- TWO COLUMN SECTION: HEALTH TRENDS & ALERTS -->
    <section class="dashboard-grid">

        <!-- HEALTH TRENDS CHART -->
        <div class="panel health-panel">
            <div class="panel-header">
                <div>
                    <p class="section-label">HEALTH MONITORING</p>
                    <h2>Health Trends</h2>
                </div>
            </div>

            <div class="chart-container" style="height: 280px;">
                <canvas id="healthChart"></canvas>
            </div>
        </div>

        <!-- VADER AI ALERTS -->
        <div class="panel alerts-panel">
            <div class="panel-header">
                <div>
                    <p class="section-label">AI MONITORING</p>
                    <h2>System Alerts</h2>
                </div>
                <a href="{{ route('sentiment.index') }}" class="view-link">
                    Review AI Insights →
                </a>
            </div>

            <div class="alert-list">
                @forelse($alerts as $alert)
                    <div class="alert-item">
                        <div class="alert-content">
                            <h4>⚠️ Observation Flagged</h4>
                            <p>{{ $alert->message }}</p>
                            <small>{{ \Carbon\Carbon::parse($alert->created_at)->diffForHumans() }}</small>
                        </div>

                        <form method="POST" action="{{ route('alerts.read', $alert->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="alert-dismiss-btn" title="Dismiss Alert">
                                Dismiss
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="empty-state">
                        <h3>No Active Alerts</h3>
                        <p>No critical concerns or negative sentiment flagged recently.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </section>

    <!-- TWO COLUMN SECTION: RECENT VISITS & PROFILE QUICK SUMMARY -->
    <section class="dashboard-grid" style="margin-top:24px;">

        <!-- RECENT CAREGIVER VISITS -->
        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="section-label">CARE ACTIVITY</p>
                    <h2>Caregiver Visit Records</h2>
                </div>
                <a href="{{ route('visits.index', $elder->id) }}" class="view-link">
                    View All Visits ({{ $elder->careVisits->count() }}) →
                </a>
            </div>

            <div class="visit-list">
                @forelse($recentVisits as $visit)
                    <div class="visit-item">
                        <div class="visit-avatar">
                            {{ strtoupper(substr($visit->caregiver->name ?? 'C', 0, 1)) }}
                        </div>

                        <div class="visit-details">
                            <div class="visit-header">
                                <h3>{{ $visit->caregiver->name ?? 'Caregiver' }}</h3>
                                <span>{{ \Carbon\Carbon::parse($visit->visit_date)->format('d M Y') }}</span>
                            </div>

                            @if($visit->tasks_completed)
                                <span style="font-size:11px; font-weight:700; color:#2563eb; display:block; margin:2px 0 4px;">
                                    Tasks: {{ $visit->tasks_completed }}
                                </span>
                            @endif

                            <p style="margin:4px 0;">"{{ $visit->visit_note ?? 'Routine care completed.' }}"</p>

                            <div class="visit-metrics">
                                <span>Mood: {{ $visit->mood_score ?? 'N/A' }}/5</span>
                                <span>Appetite: {{ $visit->appetite_score ?? 'N/A' }}/5</span>
                                <span>Pain: {{ $visit->pain_level ?? 'N/A' }}/5</span>
                                <span>Medication: {{ $visit->medication_taken ? 'Taken' : 'Not recorded' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <h3>No care visits recorded</h3>
                        <p>Caregiver visit records will appear here once they are submitted.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ACTIVE PRESCRIPTIONS & CONTACTS QUICK CARD -->
        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="section-label">CLINICAL OVERVIEW</p>
                    <h2>Active Care Profile</h2>
                </div>
                <a href="{{ route('elder.show', $elder->id) }}" class="view-link">
                    Full Profile Details →
                </a>
            </div>

            <div class="profile-details">
                <div>
                    <span>Full Name</span>
                    <strong>{{ $elder->full_name }}</strong>
                </div>
                <div>
                    <span>Date of Birth</span>
                    <strong>{{ optional($elder->date_of_birth)->format('d M Y') }}</strong>
                </div>
                <div>
                    <span>Blood Type</span>
                    <strong>{{ $elder->blood_type ?? 'O+' }}</strong>
                </div>
                <div>
                    <span>Mobility Status</span>
                    <strong>{{ $elder->mobility_status ?? 'Assisted' }} ({{ $elder->independence_score ?? 65 }}%)</strong>
                </div>
                <div>
                    <span>Primary Physician</span>
                    <strong>{{ $elder->physician_name ?? 'Dr. Stephen Obonyo' }}</strong>
                </div>
                <div>
                    <span>Emergency Contact</span>
                    <strong>{{ $elder->emergency_contact_name }} ({{ $elder->emergency_contact_phone }})</strong>
                </div>
            </div>

            @if($elder->medicationList->isNotEmpty())
                <div style="margin-top:16px; padding-top:14px; border-top:1px solid #f1f5f9;">
                    <span style="display:block; font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase; margin-bottom:8px;">Prescriptions ({{ $elder->medicationList->count() }})</span>
                    <div style="display:flex; flex-wrap:wrap; gap:6px;">
                        @foreach($elder->medicationList as $med)
                            <span style="padding:4px 8px; background:#eff6ff; color:#1d4ed8; border-radius:6px; font-size:11px; font-weight:700;">
                                {{ $med->name }} ({{ $med->dosage }})
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </section>

</div>
@endsection

<<<<<<< Updated upstream
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
=======
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const trendData = @json($healthTrends);
    const labels = trendData.map(item => {
        const date = new Date(item.visit_date);
        return date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
    });

    const moodData = trendData.map(item => item.mood_score);
    const appetiteData = trendData.map(item => item.appetite_score);
    const painData = trendData.map(item => item.pain_level);

    const ctx = document.getElementById('healthChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length ? labels : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [
                {
                    label: 'Mood',
                    data: moodData.length ? moodData : [3, 4, 3, 2, 4, 4, 4],
                    borderColor: '#2563eb',
                    backgroundColor: '#2563eb',
                    tension: 0.35,
                    borderWidth: 2,
                    fill: false
                },
                {
                    label: 'Appetite',
                    data: appetiteData.length ? appetiteData : [4, 4, 3, 2, 4, 5, 4],
                    borderColor: '#10b981',
                    backgroundColor: '#10b981',
                    tension: 0.35,
                    borderWidth: 2,
                    fill: false
                },
                {
                    label: 'Pain Level',
                    data: painData.length ? painData : [1, 1, 2, 3, 1, 1, 1],
                    borderColor: '#ef4444',
                    backgroundColor: '#ef4444',
                    tension: 0.35,
                    borderWidth: 2,
                    fill: false
>>>>>>> Stashed changes
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
<<<<<<< Updated upstream
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
=======
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { display: true }
>>>>>>> Stashed changes
            }
        }
    });
</script>
<<<<<<< Updated upstream
@endif
</body>
</html>
=======
@endpush
>>>>>>> Stashed changes
