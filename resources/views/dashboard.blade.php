<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Family Overview | ElderCare</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="app-container">

    <!-- TOP NAVIGATION -->

    <header class="topbar">

        <div class="breadcrumb">
            <span>Dashboard</span>
            <span>/</span>
            <strong>Family Overview</strong>
        </div>

        <div class="topbar-right">

            <div class="search-box">
                <input
                    type="text"
                    placeholder="Search records, patients..."
                >
            </div>

            <div class="notification">
                <a href="#">
                    Notifications
                    @if($alerts->count() > 0)
                        <span class="notification-count">
                            {{ $alerts->count() }}
                        </span>
                    @endif
                </a>
            </div>

            <div class="user-menu">
                <span class="user-name">
                    {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="logout-btn">
                        Logout
                    </button>
                </form>
            </div>

        </div>

    </header>


    <!-- MAIN CONTENT -->

    <main class="dashboard-content">

        <!-- ELDER HEADER -->

        <section class="elder-header">

            <div class="elder-information">

                <div class="elder-avatar">
                    {{ strtoupper(substr($elder->full_name, 0, 1)) }}
                </div>

                <div>

                    <p class="section-label">
                        ELDER PROFILE
                    </p>

                    <h1>
                        {{ $elder->full_name }}
                    </h1>

                    <p class="elder-description">

                        @if($elder->medical_conditions)
                            {{ $elder->medical_conditions }}
                        @else
                            No medical conditions recorded.
                        @endif

                    </p>

                </div>

            </div>


            <div class="elder-actions">

                <a
                    href="{{ route('elder.profile.edit', $elder->id) }}"
                    class="primary-button"
                >
                    Edit Profile
                </a>

                <a
                    href="{{ route('visits.index', $elder->id) }}"
                    class="secondary-button"
                >
                    View Visits
                </a>

            </div>

        </section>


        <!-- HEALTH SUMMARY -->

        <section class="summary-grid">

            <div class="summary-card">

                <p class="card-label">
                    MOOD
                </p>

                <h2>
                    {{ $latestMood }}/5
                </h2>

                <p class="card-description">
                    Latest recorded mood
                </p>

            </div>


            <div class="summary-card">

                <p class="card-label">
                    APPETITE
                </p>

                <h2>
                    {{ $latestAppetite }}/5
                </h2>

                <p class="card-description">
                    Latest recorded appetite
                </p>

            </div>


            <div class="summary-card">

                <p class="card-label">
                    PAIN LEVEL
                </p>

                <h2>
                    {{ $latestPain }}/5
                </h2>

                <p class="card-description">
                    Latest recorded pain
                </p>

            </div>


            <div class="summary-card">

                <p class="card-label">
                    MEDICATION ADHERENCE
                </p>

                <h2>
                    {{ $medicationAdherence }}%
                </h2>

                <p class="card-description">
                    Based on recorded visits
                </p>

            </div>

        </section>


        <!-- TWO COLUMN SECTION -->

        <section class="dashboard-grid">

            <!-- HEALTH TRENDS -->

            <div class="panel health-panel">

                <div class="panel-header">

                    <div>
                        <p class="section-label">
                            HEALTH MONITORING
                        </p>

                        <h2>
                            Health Trends
                        </h2>
                    </div>

                </div>

                <div class="chart-container">

                    <canvas id="healthChart"></canvas>

                </div>

            </div>


            <!-- ALERTS -->

            <div class="panel alerts-panel">

                <div class="panel-header">

                    <div>
                        <p class="section-label">
                            MONITORING
                        </p>

                        <h2>
                            System Alerts
                        </h2>
                    </div>

                    <span class="alert-count">
                        {{ $alerts->count() }}
                    </span>

                </div>


                @forelse($alerts as $alert)

                    <div class="alert-item">

                        <div class="alert-content">

                            <h3>
                                Alert
                            </h3>

                            <p>
                                {{ $alert->message }}
                            </p>

                            <small>
                                {{ \Carbon\Carbon::parse($alert->created_at)->diffForHumans() }}
                            </small>

                        </div>

                        <div class="alert-actions">

                            <form
                                method="POST"
                                action="{{ route('alerts.read', $alert->id) }}"
                            >

                                @csrf
                                @method('PATCH')

                                <button type="submit">
                                    Mark as read
                                </button>

                            </form>

                        </div>

                    </div>

                @empty

                    <div class="empty-state">

                        <h3>
                            No active alerts
                        </h3>

                        <p>
                            There are currently no unread alerts for this elder.
                        </p>

                    </div>

                @endforelse

            </div>

        </section>


        <!-- CARE VISITS -->

        <section class="panel visits-panel">

            <div class="panel-header">

                <div>

                    <p class="section-label">
                        CARE HISTORY
                    </p>

                    <h2>
                        Recent Caregiver Visits
                    </h2>

                </div>

                <a
                    href="{{ route('visits.index', $elder->id) }}"
                    class="view-link"
                >
                    View History
                </a>

            </div>


            <div class="visits-list">

                @forelse($recentVisits as $visit)

                    <div class="visit-item">

                        <div class="caregiver-avatar">

                            {{ strtoupper(substr($visit->caregiver_name ?? 'C', 0, 1)) }}

                        </div>


                        <div class="visit-information">

                            <div class="visit-top">

                                <h3>
                                    {{ $visit->caregiver_name ?? 'Caregiver' }}
                                </h3>

                                <span>

                                    {{ \Carbon\Carbon::parse($visit->visit_date)->format('d M Y, H:i') }}

                                </span>

                            </div>


                            <p>

                                @if($visit->visit_note)
                                    {{ $visit->visit_note }}
                                @else
                                    No visit note was recorded.
                                @endif

                            </p>


                            <div class="visit-metrics">

                                <span>
                                    Mood: {{ $visit->mood_score ?? 'N/A' }}/5
                                </span>

                                <span>
                                    Appetite: {{ $visit->appetite_score ?? 'N/A' }}/5
                                </span>

                                <span>
                                    Pain: {{ $visit->pain_level ?? 'N/A' }}/5
                                </span>

                                <span>
                                    Medication:

                                    @if($visit->medication_taken)
                                        Taken
                                    @else
                                        Not taken
                                    @endif

                                </span>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="empty-state">

                        <h3>
                            No care visits recorded
                        </h3>

                        <p>
                            Caregiver visit records will appear here once they are submitted.
                        </p>

                    </div>

                @endforelse

            </div>

        </section>


        <!-- ELDER PROFILE SUMMARY -->

        <section class="profile-grid">

            <div class="panel">

                <div class="panel-header">

                    <div>

                        <p class="section-label">
                            PERSONAL INFORMATION
                        </p>

                        <h2>
                            Elder Profile
                        </h2>

                    </div>

                    <a
                        href="{{ route('elder.profile.edit', $elder->id) }}"
                        class="view-link"
                    >
                        Edit
                    </a>

                </div>


                <div class="profile-details">

                    <div>
                        <span>Full Name</span>
                        <strong>
                            {{ $elder->full_name }}
                        </strong>
                    </div>

                    <div>
                        <span>Date of Birth</span>
                        <strong>
                            {{ \Carbon\Carbon::parse($elder->date_of_birth)->format('d M Y') }}
                        </strong>
                    </div>

                    <div>
                        <span>Mobility</span>
                        <strong>
                            {{ $elder->mobility_status ?? 'Not recorded' }}
                        </strong>
                    </div>

                    <div>
                        <span>Emergency Contact</span>
                        <strong>
                            {{ $elder->emergency_contact_name ?? 'Not recorded' }}
                        </strong>
                    </div>

                    <div>
                        <span>Emergency Phone</span>
                        <strong>
                            {{ $elder->emergency_contact_phone ?? 'Not recorded' }}
                        </strong>
                    </div>

                </div>

            </div>


            <div class="panel">

                <div class="panel-header">

                    <div>

                        <p class="section-label">
                            MEDICAL INFORMATION
                        </p>

                        <h2>
                            Care Information
                        </h2>

                    </div>

                </div>


                <div class="medical-information">

                    <div>

                        <h3>
                            Medical Conditions
                        </h3>

                        <p>
                            {{ $elder->medical_conditions ?? 'No conditions recorded.' }}
                        </p>

                    </div>


                    <div>

                        <h3>
                            Medications
                        </h3>

                        <p>
                            {{ $elder->medications ?? 'No medications recorded.' }}
                        </p>

                    </div>


                    <div>

                        <h3>
                            Allergies
                        </h3>

                        <p>
                            {{ $elder->allergies ?? 'No allergies recorded.' }}
                        </p>

                    </div>


                    <div>

                        <h3>
                            Care Preferences
                        </h3>

                        <p>
                            {{ $elder->care_preferences ?? 'No preferences recorded.' }}
                        </p>

                    </div>

                </div>

            </div>

        </section>

    </main>

</div>


<!-- HEALTH CHART -->

<script>

    const trendData = @json($healthTrends);

    const labels = trendData.map(item => {

        return new Date(item.visit_date).toLocaleDateString(
            'en-GB',
            {
                day: '2-digit',
                month: 'short'
            }
        );

    });


    const moodData = trendData.map(item => item.mood_score);
    const appetiteData = trendData.map(item => item.appetite_score);
    const painData = trendData.map(item => item.pain_level);


    const ctx = document
        .getElementById('healthChart')
        .getContext('2d');


    new Chart(ctx, {

        type: 'line',

        data: {

            labels: labels,

            datasets: [

                {
                    label: 'Mood',
                    data: moodData,
                    tension: 0.35,
                    borderWidth: 2,
                    fill: false
                },

                {
                    label: 'Appetite',
                    data: appetiteData,
                    tension: 0.35,
                    borderWidth: 2,
                    fill: false
                },

                {
                    label: 'Pain',
                    data: painData,
                    tension: 0.35,
                    borderWidth: 2,
                    fill: false
                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            scales: {

                y: {
                    beginAtZero: true,
                    max: 5,
                    ticks: {
                        stepSize: 1
                    }
                }

            },

            plugins: {

                legend: {
                    display: true
                }

            }

        }

    });

</script>

</body>

</html>