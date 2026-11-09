@extends('layouts.app')

@section('title', 'Care Visit History - ' . $elder->full_name)
@section('page-heading', 'Care Visit History')

@section('content')
<link rel="stylesheet" href="{{ asset('css/visits.css') }}">
<style>
.visits-sentiment-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 800;
}
.vs-positive { background: #dcfce7; color: #15803d; }
.vs-neutral { background: #f1f5f9; color: #475569; }
.vs-negative { background: #fee2e2; color: #b91c1c; }
</style>

<div class="visits-page">
    <div class="visits-page-heading">
        <div>
            <div class="visits-eyebrow">
                <a href="{{ auth()->user()->role === 'caregiver' ? route('caregiver.dashboard') : route('dashboard') }}">Dashboard</a> / Care history
            </div>
            <h1>Care Visit History</h1>
            <p>Chronological timeline of caregiver visits, procedures performed, and VADER sentiment evaluations for {{ $elder->full_name }}.</p>
        </div>

        @if(auth()->user()->role === 'caregiver' || auth()->user()->isAdmin())
            <div>
                <a href="{{ route('visits.create', $elder->id) }}" class="visits-button visits-button-primary">
                    + Record New Care Visit
                </a>
            </div>
        @endif
    </div>

    <section class="visits-profile-card">
        <div class="visits-profile-avatar" style="overflow: hidden; border-radius: 17px;">
            @if($elder->photo_url)
                <img src="{{ $elder->photo_url }}" alt="{{ $elder->full_name }}" style="width:100%; height:100%; object-fit:cover;" onerror="if (!this.dataset.fallback) { this.dataset.fallback = '1'; this.src = '{{ asset('storage/' . $elder->photo_path) }}'; } else { this.style.display='none'; this.nextElementSibling.style.display='flex'; }">
                <span style="display:none; width:100%; height:100%; align-items:center; justify-content:center;">{{ strtoupper(substr($elder->full_name, 0, 1)) }}</span>
            @else
                {{ strtoupper(substr($elder->full_name, 0, 1)) }}
            @endif
        </div>
        <div class="visits-profile-copy">
            <span>ELDER CARE PROFILE</span>
            <h2>{{ $elder->full_name }}</h2>
            <p>{{ $visits->count() }} {{ \Illuminate\Support\Str::plural('visit', $visits->count()) }} recorded · {{ $elder->residence ?? 'Home Care' }}</p>
        </div>
        <div style="margin-left:auto; display:flex; gap:12px; align-items:center;">
            <a href="{{ route('elder.show', $elder->id) }}" style="font-size:12px; font-weight:700; color:#2563eb; text-decoration:none;">
                View Profile Hub →
            </a>
            <a href="{{ auth()->user()->role === 'caregiver' ? route('caregiver.dashboard') : route('dashboard') }}" class="visits-back-link">
                Back to dashboard
            </a>
        </div>
    </section>

    @if(session('status'))
        <div class="visits-notice">{{ session('status') }}</div>
    @endif
    @if(session('success'))
        <div class="visits-notice">{{ session('success') }}</div>
    @endif

    @if($visits->isEmpty())
        <section class="visits-empty-card">
            <div class="visits-empty-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><rect x="4" y="5" width="16" height="15" rx="3"/><path d="M8 3v4M16 3v4M4 10h16M8 14h3"/></svg>
            </div>
            <h2>No visits recorded yet</h2>
            <p>When a caregiver records a visit, its date, procedures performed, observations, and VADER sentiment score will appear here.</p>
            @if(auth()->user()->role === 'caregiver' || auth()->user()->isAdmin())
                <a href="{{ route('visits.create', $elder->id) }}" class="visits-button visits-button-primary" style="margin-top:14px;">
                    + Record First Visit Now
                </a>
            @endif
        </section>
    @else
        <div class="visits-list-heading">
            <div><span class="visits-eyebrow">TIMELINE</span><h2>Recorded Sessions</h2></div>
            <span class="visits-count">{{ $visits->count() }} records</span>
        </div>

        <div class="visits-timeline">
            @foreach($visits as $visit)
                <article class="visits-record">
                    <div class="visits-timeline-marker"><span></span></div>
                    <div class="visits-record-card">
                        <div class="visits-record-top">
                            <div class="visits-caregiver">
                                <div class="visits-caregiver-avatar">{{ strtoupper(substr($visit->caregiver->name ?? 'C', 0, 1)) }}</div>
                                <div>
                                    <h3>{{ $visit->caregiver->name ?? 'Caregiver' }}</h3>
                                    <p>Care Session @if($visit->clock_in)· {{ $visit->clock_in }} - {{ $visit->clock_out }}@endif</p>
                                </div>
                            </div>

                            <div style="display:flex; align-items:center; gap:12px;">
                                @if($visit->sentimentResult)
                                    @php $class = $visit->sentimentResult->classification; @endphp
                                    <span class="visits-sentiment-badge vs-{{ $class }}">
                                        VADER: {{ ucfirst($class) }} ({{ $visit->sentimentResult->compound_score }})
                                        @if($visit->sentimentResult->flagged) ⚠️ @endif
                                    </span>
                                @endif
                                <time datetime="{{ optional($visit->visit_date)->toIso8601String() }}">
                                    {{ $visit->visit_date ? $visit->visit_date->format('d M Y') : 'Date not available' }}
                                </time>
                            </div>
                        </div>

                        @if($visit->tasks_completed)
                            <div class="visits-tasks">
                                <span>PROCEDURES & TASKS COMPLETED</span>
                                <p>{{ $visit->tasks_completed }}</p>
                            </div>
                        @endif

                        @if($visit->visit_note)
                            <div class="visits-note">
                                <span>PHYSICAL OBSERVATIONS & CLINICAL NOTE</span>
                                <p>{{ $visit->visit_note }}</p>
                            </div>
                        @endif

                        @if($visit->cognitive_notes)
                            <div class="visits-note" style="background:#fefce8; border:1px solid #fef08a;">
                                <span style="color:#854d0e;">COGNITIVE & EMOTIONAL NOTE</span>
                                <p style="color:#713f12;">{{ $visit->cognitive_notes }}</p>
                            </div>
                        @endif

                        <div class="visits-metrics">
                            @if($visit->mood_score !== null)<div><span>Mood</span><strong>{{ $visit->mood_score }}/5</strong></div>@endif
                            @if($visit->appetite_score !== null)<div><span>Appetite</span><strong>{{ $visit->appetite_score }}/5</strong></div>@endif
                            @if($visit->pain_level !== null)<div><span>Pain</span><strong>{{ $visit->pain_level }}/5</strong></div>@endif
                            <div><span>Medication</span><strong class="{{ $visit->medication_taken ? 'visits-good' : 'visits-muted' }}">{{ $visit->medication_taken ? 'Administered' : 'Not recorded' }}</strong></div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
