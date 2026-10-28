@extends('layouts.app')

@section('title', 'Caregiver Portal - Eldercare')
@section('page-heading', 'Caregiver Portal')

@section('content')
<style>
.cg-container { max-width: 1450px; margin: 0 auto; padding: 24px clamp(16px, 2.5vw, 36px) 40px; color: #1e293b; }
.cg-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #eef2f7; }
.cg-header h1 { font: 800 clamp(24px, 3vw, 32px)/1.2 'Manrope', sans-serif; color: #0f172a; margin: 0 0 6px; }
.cg-header p { margin: 0; color: #64748b; font-size: 13px; }
.cg-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 30px; }
.cg-stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); }
.cg-stat-card small { display: block; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.cg-stat-card strong { font: 800 28px 'Manrope', sans-serif; color: #0f172a; }
.cg-stat-card p { margin: 4px 0 0; font-size: 11px; color: #64748b; }
.cg-section-title { font: 800 18px 'Manrope', sans-serif; color: #0f172a; margin: 0 0 16px; display: flex; align-items: center; justify-content: space-between; }
.cg-elders-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; margin-bottom: 36px; }
.cg-elder-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 22px; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03); display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease; }
.cg-elder-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06); border-color: #cbd5e1; }
.cg-elder-top { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
.cg-elder-avatar { width: 52px; height: 52px; border-radius: 14px; background: #e0f2fe; color: #0284c7; display: grid; place-items: center; font: 800 20px 'Manrope', sans-serif; overflow: hidden; }
.cg-elder-avatar img { width: 100%; height: 100%; object-fit: cover; }
.cg-elder-info h3 { margin: 0 0 3px; font: 800 16px 'Manrope', sans-serif; color: #0f172a; }
.cg-elder-info p { margin: 0; font-size: 11px; color: #64748b; }
.cg-elder-badges { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 16px; }
.cg-badge { padding: 3px 8px; border-radius: 6px; font-size: 10px; font-weight: 700; background: #f1f5f9; color: #475569; }
.cg-elder-actions { display: grid; grid-template-columns: 1.5fr 1fr; gap: 8px; margin-top: auto; padding-top: 14px; border-top: 1px solid #f1f5f9; }
.cg-btn-visit { padding: 9px 12px; background: #2563eb; color: #ffffff; border-radius: 10px; font: 700 12px 'Manrope', sans-serif; text-decoration: none; text-align: center; box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25); }
.cg-btn-visit:hover { background: #1d4ed8; }
.cg-btn-view { padding: 9px 12px; background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; border-radius: 10px; font: 700 12px 'Manrope', sans-serif; text-decoration: none; text-align: center; }
.cg-btn-view:hover { background: #e2e8f0; }
.cg-table-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; overflow: hidden; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03); }
.cg-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
.cg-table th { background: #f8fafc; padding: 14px 18px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
.cg-table td { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; color: #334155; }
.cg-table tr:hover td { background: #f8fafc; }
.cg-pill-pos { padding: 3px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; background: #dcfce7; color: #15803d; }
.cg-pill-neu { padding: 3px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; background: #f1f5f9; color: #475569; }
.cg-pill-neg { padding: 3px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; background: #fee2e2; color: #b91c1c; }
</style>

<div class="cg-container">

    <div class="cg-header">
        <div>
            <h1>Welcome back, {{ $caregiver->name }}</h1>
            <p>Professional Caregiver Workspace · Monitoring home visits and clinical observations</p>
        </div>
        <div>
            <span style="font-size:12px; color:#64748b; font-weight:700;">Local Service Time: {{ now()->format('l, d F Y') }}</span>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="cg-stats-grid">
        <div class="cg-stat-card">
            <small>Assigned Elders</small>
            <strong>{{ $stats['total_assigned_elders'] }}</strong>
            <p>Active care recipients</p>
        </div>
        <div class="cg-stat-card">
            <small>Visits Logged</small>
            <strong>{{ $stats['visits_conducted'] }}</strong>
            <p>Total recorded sessions</p>
        </div>
        <div class="cg-stat-card">
            <small>Visits This Week</small>
            <strong>{{ $stats['visits_this_week'] }}</strong>
            <p>Since Monday</p>
        </div>
        <div class="cg-stat-card">
            <small>Flagged Observations</small>
            <strong style="color: {{ $stats['alerts_triggered'] > 0 ? '#b91c1c' : '#0f172a' }};">
                {{ $stats['alerts_triggered'] }}
            </strong>
            <p>VADER negative signals</p>
        </div>
    </div>

    <!-- Assigned Elders -->
    <div class="cg-section-title">
        <span>Assigned Elder Profiles</span>
        <span style="font-size:12px; font-weight:600; color:#64748b;">Select an elder to record a care visit</span>
    </div>

    <div class="cg-elders-grid">
        @forelse($assignedElders as $elder)
            <div class="cg-elder-card">
                <div>
                    <div class="cg-elder-top">
                        <div class="cg-elder-avatar">
                            @if($elder->photo_url)
                                <img src="{{ $elder->photo_url }}" alt="{{ $elder->full_name }}">
                            @else
                                {{ strtoupper(substr($elder->full_name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="cg-elder-info">
                            <h3>{{ $elder->full_name }}</h3>
                            <p>{{ $elder->age ? $elder->age . ' yrs' : 'Elder' }} · {{ $elder->residence ?? 'Home Care' }}</p>
                        </div>
                    </div>

                    <div class="cg-elder-badges">
                        <span class="cg-badge">Mobility: {{ $elder->mobility_status ?? 'Assisted' }}</span>
                        @if($elder->blood_type)
                            <span class="cg-badge">Blood: {{ $elder->blood_type }}</span>
                        @endif
                        <span class="cg-badge">{{ $elder->medicationList->count() }} Meds</span>
                        @if($elder->allergyList->count() > 0)
                            <span class="cg-badge" style="background:#fee2e2; color:#b91c1c;">{{ $elder->allergyList->count() }} Allergies</span>
                        @endif
                    </div>
                </div>

                <div class="cg-elder-actions">
                    <a href="{{ route('visits.create', $elder->id) }}" class="cg-btn-visit">
                        + Record Visit
                    </a>
                    <a href="{{ route('elder.show', $elder->id) }}" class="cg-btn-view">
                        View Profile
                    </a>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; padding: 30px; background:#fff; border-radius:16px; text-align:center; border:1px solid #e2e8f0;">
                <p style="color:#64748b; margin:0;">No elders currently assigned. Family members can register profiles or assign your caregiver account.</p>
            </div>
        @endforelse
    </div>

    <!-- Recent Care Visits by Caregiver -->
    <div class="cg-section-title">
        <span>Recent Visit Logs & VADER Sentiment Feedback</span>
        <a href="{{ route('sentiment.index') }}" style="font-size:12px; font-weight:700; color:#2563eb; text-decoration:none;">
            View Detailed Sentiment Analytics →
        </a>
    </div>

    <div class="cg-table-card">
        @if($myVisits->isEmpty())
            <div style="padding: 40px; text-align: center; color: #94a3b8;">
                <p style="margin: 0;">No care visits recorded yet. Click <strong>+ Record Visit</strong> above to submit your first visit report.</p>
            </div>
        @else
            <table class="cg-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Elder Name</th>
                        <th>Tasks Performed</th>
                        <th>VADER Sentiment</th>
                        <th>Observation Snippet</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($myVisits as $visit)
                        <tr>
                            <td>
                                <strong>{{ $visit->visit_date ? $visit->visit_date->format('M d, Y') : 'N/A' }}</strong>
                                @if($visit->clock_in)
                                    <span style="display:block; font-size:11px; color:#64748b;">{{ $visit->clock_in }} - {{ $visit->clock_out }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $visit->elder->full_name ?? 'Elder' }}</strong>
                            </td>
                            <td>
                                <span style="font-size:12px; color:#475569;">
                                    {{ \Illuminate\Support\Str::limit($visit->tasks_completed ?? 'Routine check', 35) }}
                                </span>
                            </td>
                            <td>
                                @if($visit->sentimentResult)
                                    @php $class = $visit->sentimentResult->classification; @endphp
                                    <span class="cg-pill-{{ substr($class, 0, 3) }}">
                                        {{ ucfirst($class) }} ({{ $visit->sentimentResult->compound_score }})
                                    </span>
                                    @if($visit->sentimentResult->flagged)
                                        <span title="Flagged for family review" style="color:#b91c1c; font-weight:800; margin-left:4px;">⚠️</span>
                                    @endif
                                @else
                                    <span class="cg-pill-neu">Pending</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size:12px; color:#64748b;">
                                    "{{ \Illuminate\Support\Str::limit($visit->visit_note ?? 'No notes recorded.', 55) }}"
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('visits.index', $visit->elder_id) }}" style="color:#2563eb; font-weight:700; text-decoration:none; font-size:12px;">
                                    History →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection
