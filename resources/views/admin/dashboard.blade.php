@extends('layouts.app')

@section('title', 'Administrator Dashboard - Eldercare')
@section('page-heading', 'Administrator Console')

@section('content')
<style>
.ad-container { max-width: 1450px; margin: 0 auto; padding: 24px clamp(16px, 2.5vw, 36px) 40px; color: #1e293b; }
.ad-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; }
.ad-header-info h1 { font: 800 clamp(22px, 2.5vw, 30px)/1.2 'Manrope', sans-serif; color: #0f172a; margin: 0 0 6px; }
.ad-header-info p { margin: 0; color: #64748b; font-size: 13px; }
.ad-kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 28px; }
.ad-kpi-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 20px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); }
.ad-kpi-card small { display: block; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.ad-kpi-card strong { font: 800 28px 'Manrope', sans-serif; color: #0f172a; }
.ad-kpi-card p { margin: 4px 0 0; font-size: 11px; color: #16a34a; font-weight: 700; }
.ad-main-grid { display: grid; grid-template-columns: minmax(0, 1.8fr) minmax(320px, 1fr); gap: 24px; align-items: start; }
.ad-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 22px; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03); }
.ad-card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.ad-card-title { font: 800 17px 'Manrope', sans-serif; color: #0f172a; margin: 0; }
.ad-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
.ad-table th { background: #f8fafc; padding: 12px 14px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
.ad-table td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; color: #334155; }
.ad-role-badge { padding: 3px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
.role-family { background: #e0f2fe; color: #0369a1; }
.role-caregiver { background: #fef3c7; color: #b45309; }
.role-admin { background: #fee2e2; color: #b91c1c; }
.ad-feed-list { display: flex; flex-direction: column; gap: 14px; }
.ad-feed-item { display: flex; gap: 12px; align-items: flex-start; padding: 12px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9; }
.ad-feed-icon { width: 32px; height: 32px; border-radius: 8px; display: grid; place-items: center; font-size: 14px; flex-shrink: 0; }
.ad-feed-meta strong { display: block; font-size: 12px; color: #1e293b; margin-bottom: 2px; }
.ad-feed-meta p { margin: 0; font-size: 11px; color: #64748b; }
.ad-feed-meta small { display: block; margin-top: 4px; font-size: 10px; color: #94a3b8; }
@media (max-width: 950px) { .ad-main-grid { grid-template-columns: 1fr; } }
</style>

<div class="ad-container">

    <div class="ad-header">
        <div class="ad-header-info">
            <h1>Welcome back, {{ auth()->user()->name }}</h1>
            <p>System Administrator · Monitoring platform activity, care records, and NLP sentiment metrics</p>
        </div>
        <div style="display:flex; gap:10px;">
            <button type="button" onclick="window.print()" class="ep-btn-secondary" style="padding:10px 16px;">
                Export System Report
            </button>
        </div>
    </div>

    <!-- 4 High Level Metrics (Figure 4.12) -->
    <div class="ad-kpi-grid">
        <div class="ad-kpi-card">
            <small>Total Platform Users</small>
            <strong>{{ $totalUsers }}</strong>
            <p>↑ {{ $totalCaregivers }} Caregivers · {{ $totalFamilies }} Families</p>
        </div>
        <div class="ad-kpi-card">
            <small>Avg. Sentiment Polarity</small>
            <strong>{{ $avgSentimentPercent }}%</strong>
            <p>Lexicon-scored across visit logs</p>
        </div>
        <div class="ad-kpi-card">
            <small>Unresolved Alerts</small>
            <strong style="color: {{ $unresolvedAlerts > 0 ? '#b91c1c' : '#0f172a' }};">{{ $unresolvedAlerts }}</strong>
            <p style="color: {{ $unresolvedAlerts > 0 ? '#b91c1c' : '#64748b' }};">Pending family review</p>
        </div>
        <div class="ad-kpi-card">
            <small>Total Elders Registered</small>
            <strong>{{ $totalElders }}</strong>
            <p>Active Profiles</p>
        </div>
    </div>

    <div class="ad-main-grid">

        <!-- User Management (Figure 4.12) -->
        <div class="ad-card">
            <div class="ad-card-head">
                <div>
                    <h2 class="ad-card-title">User Management</h2>
                    <span style="font-size:12px; color:#64748b;">Manage platform access, role authorizations, and caregiver accounts</span>
                </div>
            </div>

            <table class="ad-table">
                <thead>
                    <tr>
                        <th>Name & Email</th>
                        <th>Role</th>
                        <th>Role Actions</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <strong style="color:#0f172a;">{{ $user->name }}</strong>
                                <span style="display:block; font-size:11px; color:#64748b;">{{ $user->email }}</span>
                            </td>
                            <td>
                                <span class="ad-role-badge role-{{ $user->role }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.users.role', $user->id) }}" style="display:flex; gap:6px; align-items:center;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" style="font-size:11px; padding:4px 8px; border:1px solid #cbd5e1; border-radius:6px;">
                                        <option value="family" {{ $user->role === 'family' ? 'selected' : '' }}>Family</option>
                                        <option value="caregiver" {{ $user->role === 'caregiver' ? 'selected' : '' }}>Caregiver</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <button type="submit" style="padding:4px 8px; font-size:11px; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:6px; cursor:pointer;">
                                        Set
                                    </button>
                                </form>
                            </td>
                            <td>
                                <span style="font-size:11px; color:#64748b;">
                                    {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top:16px;">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Recent Activity Feed (Figure 4.12) -->
        <div class="ad-card">
            <div class="ad-card-head">
                <h2 class="ad-card-title">Live Care Activity</h2>
                <span style="font-size:10px; font-weight:800; background:#dcfce7; color:#15803d; padding:2px 8px; border-radius:9999px;">LIVE FEED</span>
            </div>

            <div class="ad-feed-list">
                @forelse($recentVisits as $visit)
                    <div class="ad-feed-item">
                        <div class="ad-feed-icon" style="background:#e0f2fe; color:#0369a1;">📋</div>
                        <div class="ad-feed-meta">
                            <strong>{{ $visit->caregiver->name ?? 'Caregiver' }} logged visit</strong>
                            <p>For elder: {{ $visit->elder->full_name ?? 'Elder' }}</p>
                            @if($visit->sentimentResult)
                                <small style="color: {{ $visit->sentimentResult->flagged ? '#b91c1c' : '#15803d' }}; font-weight:700;">
                                    VADER: {{ ucfirst($visit->sentimentResult->classification) }} (Score: {{ $visit->sentimentResult->compound_score }})
                                </small>
                            @endif
                            <small>{{ $visit->created_at ? $visit->created_at->diffForHumans() : 'Just now' }}</small>
                        </div>
                    </div>
                @empty
                    <p style="color:#94a3b8; font-size:12px; margin:0;">No care visits recorded recently.</p>
                @endforelse

                <div class="ad-feed-item">
                    <div class="ad-feed-icon" style="background:#fef3c7; color:#b45309;">⚙️</div>
                    <div class="ad-feed-meta">
                        <strong>NLTK VADER Microservice Active</strong>
                        <p>Flask REST API on 127.0.0.1:5001 connected.</p>
                        <small>System Monitor</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
