@extends('layouts.app')

@section('title', 'Administrator Dashboard - Eldercare')
@section('page-heading', 'Administrator Console')

@section('content')
<style>
.adm-container { max-width: 1450px; margin: 0 auto; padding: 24px clamp(16px, 2.5vw, 36px) 40px; color: #1e293b; }
.adm-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; }
.adm-header-info h1 { font: 800 clamp(22px, 2.5vw, 30px)/1.2 'Manrope', sans-serif; color: #0f172a; margin: 0 0 6px; }
.adm-header-info p { margin: 0; color: #64748b; font-size: 13px; }
.adm-kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 28px; }
.adm-kpi-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 20px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); }
.adm-kpi-card small { display: block; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.adm-kpi-card strong { font: 800 28px 'Manrope', sans-serif; color: #0f172a; }
.adm-kpi-card p { margin: 4px 0 0; font-size: 11px; color: #16a34a; font-weight: 700; }

/* Tabs */
.adm-tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; flex-wrap: wrap; }
.adm-tab-btn { background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s; }
.adm-tab-btn:hover { background: #f8fafc; color: #0f172a; }
.adm-tab-btn.active { background: #0f172a; color: #ffffff; border-color: #0f172a; }
.adm-tab-badge { padding: 2px 7px; border-radius: 9999px; font-size: 11px; background: rgba(255,255,255,0.2); }
.adm-tab-btn:not(.active) .adm-tab-badge { background: #e2e8f0; color: #334155; }

/* Filter Bar & Search */
.adm-filter-bar { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; padding: 12px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; }
.adm-filter-pills { display: flex; gap: 8px; flex-wrap: wrap; }
.adm-filter-pill { background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.15s; }
.adm-filter-pill:hover { background: #f1f5f9; color: #0f172a; }
.adm-filter-pill.active { background: #2563eb; color: #ffffff; border-color: #2563eb; }
.adm-search-input { padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 9px; font-size: 12.5px; color: #0f172a; background: #ffffff; min-width: 240px; outline: none; }
.adm-search-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }

/* Cards & Tables */
.adm-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 24px; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03); margin-bottom: 24px; }
.adm-card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.adm-card-title { font: 800 18px 'Manrope', sans-serif; color: #0f172a; margin: 0; }
.adm-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
.adm-table th { background: #f8fafc; padding: 12px 14px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
.adm-table td { padding: 14px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
.adm-role-badge { padding: 4px 9px; border-radius: 6px; font-size: 10.5px; font-weight: 800; text-transform: uppercase; display: inline-block; }
.role-family { background: #e0f2fe; color: #0369a1; }
.role-caregiver { background: #fef3c7; color: #b45309; }
.role-admin { background: #fee2e2; color: #b91c1c; }

/* Session Status Badges */
.adm-session-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; }
.session-online { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.session-offline { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
.session-dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
.session-online .session-dot { background: #16a34a; box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.2); }
.session-offline .session-dot { background: #94a3b8; }

/* Action Buttons */
.btn-action { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border-radius: 8px; font-size: 11.5px; font-weight: 700; text-decoration: none; cursor: pointer; transition: all 0.15s; border: 1px solid transparent; font-family: inherit; }
.btn-delete { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
.btn-delete:hover { background: #fee2e2; color: #b91c1c; }
.btn-view { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
.btn-view:hover { background: #dbeafe; color: #1d4ed8; }
.btn-neutral { background: #f1f5f9; color: #334155; border-color: #cbd5e1; }
.btn-neutral:hover { background: #e2e8f0; color: #0f172a; }

/* Modal */
.adm-modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 100; align-items: center; justify-content: center; padding: 20px; }
.adm-modal { background: #ffffff; border-radius: 18px; width: 100%; max-width: 560px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden; animation: popIn 0.2s ease-out; }
.adm-modal-head { display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-bottom: 1px solid #f1f5f9; }
.adm-modal-head h3 { margin: 0; font: 800 18px 'Manrope', sans-serif; color: #0f172a; }
.adm-modal-close { background: none; border: none; font-size: 22px; color: #94a3b8; cursor: pointer; line-height: 1; }
.adm-modal-body { padding: 24px; max-height: 75vh; overflow-y: auto; }
.adm-modal-foot { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 16px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; }

.adm-detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px; }
.adm-detail-box { padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; }
.adm-detail-box small { display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 4px; }
.adm-detail-box strong { font-size: 13.5px; color: #0f172a; word-break: break-word; }

@keyframes popIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
</style>

<div class="adm-container">

    <div class="adm-header">
        <div class="adm-header-info">
            <h1>Welcome back, {{ auth()->user()->name }}</h1>
            <p>System Administrator Console · View and manage all registered & logged-in users and elder care profiles</p>
        </div>
        <div style="display:flex; gap:10px;">
            <button type="button" onclick="window.print()" class="btn-action btn-neutral" style="padding:10px 16px; border-radius:10px;">
                Export System Report
            </button>
        </div>
    </div>

    @if($errors->any())
        <div style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:14px 18px; border-radius:12px; margin-bottom:20px; font-size:13px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 4 High Level Metrics -->
    <div class="adm-kpi-grid">
        <div class="adm-kpi-card">
            <small>Registered & Logged-In Users</small>
            <strong>{{ $totalUsers }}</strong>
            <p>🟢 {{ $loggedInCount ?? 1 }} Logged In · {{ $totalCaregivers }} Caregivers · {{ $totalFamilies }} Families</p>
        </div>
        <div class="adm-kpi-card">
            <small>Total Elders Registered</small>
            <strong>{{ $totalElders }}</strong>
            <p>Active Patient Care Profiles</p>
        </div>
        <div class="adm-kpi-card">
            <small>Avg. Sentiment Polarity</small>
            <strong>{{ $avgSentimentPercent }}%</strong>
            <p>Lexicon-scored across visit logs</p>
        </div>
        <div class="adm-kpi-card">
            <small>Unresolved Alerts</small>
            <strong style="color: {{ $unresolvedAlerts > 0 ? '#b91c1c' : '#0f172a' }};">{{ $unresolvedAlerts }}</strong>
            <p style="color: {{ $unresolvedAlerts > 0 ? '#b91c1c' : '#64748b' }};">Pending family review</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="adm-tabs">
        <button type="button" class="adm-tab-btn active" onclick="switchAdTab('tab-users', this)">
            <span>👥 Registered & Logged-In Users</span>
            <span class="adm-tab-badge">{{ $totalUsers }}</span>
        </button>
        <button type="button" class="adm-tab-btn" onclick="switchAdTab('tab-elders', this)">
            <span>🩺 Elder Care Profiles</span>
            <span class="adm-tab-badge">{{ $totalElders }}</span>
        </button>
        <button type="button" class="adm-tab-btn" onclick="switchAdTab('tab-activity', this)">
            <span>📋 Live Care Activity</span>
            <span class="adm-tab-badge">{{ $recentVisits->count() }}</span>
        </button>
    </div>

    <!-- TAB 1: SYSTEM USERS MANAGEMENT (View & Delete Only) -->
    <div id="tab-users" class="adm-tab-pane">
        <div class="adm-card">
            <div class="adm-card-head">
                <div>
                    <h2 class="adm-card-title">System Users Directory (Registered & Logged-In)</h2>
                    <span style="font-size:12px; color:#64748b;">Inspect user profiles, monitor live login status, or permanently remove accounts</span>
                </div>
            </div>

            <!-- Interactive Filter & Search Bar -->
            <div class="adm-filter-bar">
                <div class="adm-filter-pills">
                    <button type="button" class="adm-filter-pill active" onclick="filterUsersTable('all', this)">
                        All Registered ({{ $totalUsers }})
                    </button>
                    <button type="button" class="adm-filter-pill" onclick="filterUsersTable('online', this)">
                        🟢 Currently Logged In ({{ $loggedInCount ?? 1 }})
                    </button>
                    <button type="button" class="adm-filter-pill" onclick="filterUsersTable('family', this)">
                        Family Members ({{ $totalFamilies }})
                    </button>
                    <button type="button" class="adm-filter-pill" onclick="filterUsersTable('caregiver', this)">
                        Caregivers ({{ $totalCaregivers }})
                    </button>
                    <button type="button" class="adm-filter-pill" onclick="filterUsersTable('admin', this)">
                        Administrators ({{ $totalAdmins ?? 1 }})
                    </button>
                </div>
                <input type="text" id="userSearchInput" class="adm-search-input" placeholder="Search users by name, email, or phone..." oninput="applyUserFilters()">
            </div>

            <div style="overflow-x:auto;">
                <table class="adm-table" id="adminUsersTable">
                    <thead>
                        <tr>
                            <th>User Name & Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Session Status</th>
                            <th>Linked Care Records</th>
                            <th>Registered</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php
                                $isOnline = in_array((int) $user->id, $loggedInUserIds ?? [(int) auth()->id()], true);
                                $lastSeenText = $userLastActive[$user->id] ?? ($isOnline ? 'Active session' : 'Registered');
                                if ($user->role === 'family') {
                                    $elderNames = $user->createdElders->pluck('full_name')->implode(', ');
                                    $recordsSummary = ($user->created_elders_count ?? $user->createdElders->count()) . ' Elder Profile(s)';
                                    $recordsDetail = $elderNames ?: 'No elder profiles created yet';
                                } elseif ($user->role === 'caregiver') {
                                    $assignedNames = $user->assignedElders->pluck('full_name')->implode(', ');
                                    $recordsSummary = ($user->assigned_elders_count ?? $user->assignedElders->count()) . ' Assigned · ' . ($user->care_visits_count ?? 0) . ' Visits';
                                    $recordsDetail = $assignedNames ? ('Assigned to: ' . $assignedNames) : 'No elders currently assigned';
                                } else {
                                    $recordsSummary = 'Full System Access';
                                    $recordsDetail = 'Platform Administrator';
                                }
                            @endphp
                            <tr class="adm-user-row"
                                data-role="{{ $user->role }}"
                                data-online="{{ $isOnline ? '1' : '0' }}"
                                data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . ($user->phone ?? '') . ' ' . $user->role) }}">
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div style="width:36px; height:36px; border-radius:10px; background:#eff6ff; color:#1d4ed8; display:grid; place-items:center; font-weight:800; font-size:13px; flex-shrink:0;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong style="color:#0f172a; display:block;">{{ $user->name }}</strong>
                                            <span style="font-size:11.5px; color:#64748b;">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size:12px; color:#475569;">{{ $user->phone ?: 'Not provided' }}</span>
                                </td>
                                <td>
                                    <span class="adm-role-badge role-{{ $user->role }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    @if($isOnline)
                                        <span class="adm-session-badge session-online">
                                            <span class="session-dot"></span>
                                            <span>Logged In ({{ $lastSeenText }})</span>
                                        </span>
                                    @else
                                        <span class="adm-session-badge session-offline">
                                            <span class="session-dot"></span>
                                            <span>Registered</span>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <strong style="font-size:12px; color:#1e293b; display:block;">{{ $recordsSummary }}</strong>
                                    <span style="font-size:11px; color:#64748b;">{{ \Illuminate\Support\Str::limit($recordsDetail, 42) }}</span>
                                </td>
                                <td>
                                    <span style="font-size:11.5px; color:#64748b;">
                                        {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:inline-flex; gap:6px; align-items:center;">
                                        <button type="button"
                                                class="btn-action btn-view"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}"
                                                data-user-email="{{ $user->email }}"
                                                data-user-phone="{{ $user->phone ?: 'Not provided' }}"
                                                data-user-role="{{ ucfirst($user->role) }}"
                                                data-user-status="{{ $isOnline ? ('Logged In (' . $lastSeenText . ')') : 'Registered (Offline)' }}"
                                                data-user-joined="{{ $user->created_at ? $user->created_at->format('F d, Y \a\t H:i') : 'N/A' }}"
                                                data-user-summary="{{ $recordsSummary }}"
                                                data-user-detail="{{ $recordsDetail }}"
                                                data-can-delete="{{ (int) $user->id !== (int) auth()->id() ? '1' : '0' }}"
                                                data-delete-url="{{ route('admin.users.delete', $user->id, false) }}"
                                                onclick="openViewUserModal(this)">
                                            👁️ View
                                        </button>

                                        @if((int) $user->id !== (int) auth()->id())
                                            <form method="POST" action="{{ route('admin.users.delete', $user->id, false) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently delete the user profile for {{ addslashes($user->name) }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-delete">
                                                    🗑️ Delete
                                                </button>
                                            </form>
                                        @else
                                            <span style="font-size:11px; color:#64748b; font-weight:700; padding:6px 8px; background:#f1f5f9; border-radius:6px;">Active Admin</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: ELDER PROFILES MANAGEMENT (View & Delete Only) -->
    <div id="tab-elders" class="adm-tab-pane" style="display:none;">
        <div class="adm-card">
            <div class="adm-card-head">
                <div>
                    <h2 class="adm-card-title">Elder Care Profiles Directory</h2>
                    <span style="font-size:12px; color:#64748b;">Inspect full elder care profiles in read-only view or permanently remove profiles</span>
                </div>
                <input type="text" id="elderSearchInput" class="adm-search-input" placeholder="Search elder profiles by name or residence..." oninput="filterEldersTable()">
            </div>

            <div style="overflow-x:auto;">
                <table class="adm-table" id="adminEldersTable">
                    <thead>
                        <tr>
                            <th>Elder Patient</th>
                            <th>Family Owner</th>
                            <th>Residence</th>
                            <th>Assigned Caregiver</th>
                            <th>Primary Physician</th>
                            <th>Emergency Contact</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($elders as $elder)
                            <tr class="adm-elder-row"
                                data-search="{{ strtolower($elder->full_name . ' ' . ($elder->residence ?? '') . ' ' . ($elder->creator->name ?? '') . ' ' . ($elder->assignedCaregiver->name ?? '')) }}">
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div style="width:38px; height:38px; border-radius:10px; background:#eff6ff; color:#2563eb; display:grid; place-items:center; font-weight:800; font-size:13px; flex-shrink:0; overflow:hidden;">
                                            @if($elder->photo_url)
                                                <img src="{{ $elder->photo_url }}" alt="{{ $elder->full_name }}" style="width:100%; height:100%; object-fit:cover;" onerror="if (!this.dataset.fb1) { this.dataset.fb1 = '1'; this.src = '{{ asset('storage/' . $elder->photo_path) }}'; } else if (!this.dataset.fb2) { this.dataset.fb2 = '1'; this.src = '{{ asset('elder_photos/' . basename($elder->photo_path)) }}'; } else { this.style.display='none'; this.nextElementSibling.style.display='grid'; }">
                                                <span style="display:none; width:100%; height:100%; place-items:center;">{{ strtoupper(substr($elder->full_name, 0, 1)) }}</span>
                                            @else
                                                {{ strtoupper(substr($elder->full_name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <strong style="color:#0f172a; display:block;">{{ $elder->full_name }}</strong>
                                            <span style="font-size:11px; color:#64748b;">{{ $elder->age ? $elder->age . ' yrs' : 'Elder' }} · {{ $elder->gender ?? 'Not Specified' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong style="font-size:12px; color:#1e293b; display:block;">{{ $elder->creator->name ?? 'Family Account' }}</strong>
                                    <span style="font-size:10.5px; color:#64748b;">{{ $elder->creator->email ?? '' }}</span>
                                </td>
                                <td>
                                    <span style="font-size:12px; color:#475569;">{{ $elder->residence ?? '—' }}</span>
                                </td>
                                <td>
                                    @if($elder->assignedCaregiver)
                                        <span style="font-size:12px; font-weight:700; color:#1e293b; display:block;">{{ $elder->assignedCaregiver->name }}</span>
                                        <span style="font-size:10.5px; color:#64748b;">{{ $elder->assignedCaregiver->email }}</span>
                                    @else
                                        <span style="font-size:11.5px; color:#94a3b8; font-style:italic;">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size:12px; color:#334155; display:block;">{{ $elder->physician_name ?? '—' }}</span>
                                    <span style="font-size:10.5px; color:#64748b;">{{ $elder->physician_phone ?? '' }}</span>
                                </td>
                                <td>
                                    <span style="font-size:12px; color:#334155; display:block;">{{ $elder->emergency_contact_name }}</span>
                                    <span style="font-size:10.5px; color:#64748b;">{{ $elder->emergency_contact_phone }}</span>
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:inline-flex; gap:6px; align-items:center;">
                                        <a href="{{ route('elder.show', $elder->id, false) }}" class="btn-action btn-view" title="View Complete Elder Profile">
                                            👁️ View
                                        </a>

                                        <form method="POST" action="{{ route('admin.elders.delete', $elder->id, false) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently delete the elder profile for {{ addslashes($elder->full_name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center; padding:32px; color:#94a3b8;">No elder profiles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: LIVE ACTIVITY FEED -->
    <div id="tab-activity" class="adm-tab-pane" style="display:none;">
        <div class="adm-card">
            <div class="adm-card-head">
                <div>
                    <h2 class="adm-card-title">Live Care Activity Feed</h2>
                    <span style="font-size:12px; color:#64748b;">Chronological clinical log submissions and VADER sentiment classifications</span>
                </div>
                <span style="font-size:10px; font-weight:800; background:#dcfce7; color:#15803d; padding:3px 10px; border-radius:9999px;">SYSTEM ACTIVE</span>
            </div>

            <div class="adm-feed-list" style="display:flex; flex-direction:column; gap:12px;">
                @forelse($recentVisits as $visit)
                    <div style="display:flex; gap:14px; align-items:flex-start; padding:14px; background:#f8fafc; border-radius:12px; border:1px solid #f1f5f9;">
                        <div style="width:38px; height:38px; border-radius:10px; display:grid; place-items:center; font-size:15px; flex-shrink:0; background:#e0f2fe; color:#0369a1;">
                            📋
                        </div>
                        <div style="flex:1;">
                            <strong style="font-size:13px; color:#0f172a; display:block;">{{ $visit->caregiver->name ?? 'Caregiver' }} logged visit for {{ $visit->elder->full_name ?? 'Elder' }}</strong>
                            <p style="margin:4px 0 6px; font-size:12px; color:#64748b;">"{{ \Illuminate\Support\Str::limit($visit->visit_note ?? 'Routine care completed.', 120) }}"</p>
                            @if($visit->sentimentResult)
                                <span style="font-size:11px; font-weight:700; color: {{ $visit->sentimentResult->flagged ? '#b91c1c' : '#15803d' }};">
                                    VADER: {{ ucfirst($visit->sentimentResult->classification) }} (Score: {{ $visit->sentimentResult->compound_score }})
                                </span>
                            @endif
                            <small style="color:#94a3b8; font-size:11px; margin-left:8px;">{{ $visit->created_at ? $visit->created_at->diffForHumans() : 'Just now' }}</small>
                        </div>
                        @if($visit->elder)
                            <a href="{{ route('elder.show', $visit->elder->id, false) }}" class="btn-action btn-view">
                                👁️ View Elder
                            </a>
                        @endif
                    </div>
                @empty
                    <p style="color:#94a3b8; font-size:13px; margin:0;">No care visits recorded recently.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- MODAL: VIEW USER PROFILE DETAILS (Read-Only Inspection + Delete Option) -->
<div id="modalViewUser" class="adm-modal-backdrop">
    <div class="adm-modal">
        <div class="adm-modal-head">
            <h3>System User Profile Inspection</h3>
            <button type="button" class="adm-modal-close" onclick="closeModal('modalViewUser')">&times;</button>
        </div>
        <div class="adm-modal-body">
            <div style="display:flex; align-items:center; gap:14px; margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid #f1f5f9;">
                <div id="view_user_avatar" style="width:52px; height:52px; border-radius:14px; background:#eff6ff; color:#1d4ed8; display:grid; place-items:center; font-weight:800; font-size:20px; flex-shrink:0;">
                    U
                </div>
                <div>
                    <h4 id="view_user_name" style="margin:0 0 4px; font:800 18px 'Manrope', sans-serif; color:#0f172a;">User Name</h4>
                    <span id="view_user_email" style="font-size:13px; color:#64748b;">user@example.com</span>
                </div>
            </div>

            <div class="adm-detail-grid">
                <div class="adm-detail-box">
                    <small>Account Role</small>
                    <strong id="view_user_role">Family</strong>
                </div>
                <div class="adm-detail-box">
                    <small>Session Status</small>
                    <strong id="view_user_status">Logged In</strong>
                </div>
                <div class="adm-detail-box">
                    <small>Phone Number</small>
                    <strong id="view_user_phone">Not provided</strong>
                </div>
                <div class="adm-detail-box">
                    <small>Registered On</small>
                    <strong id="view_user_joined">N/A</strong>
                </div>
            </div>

            <div class="adm-detail-box" style="margin-bottom:4px;">
                <small>Linked Care Records & Activity</small>
                <strong id="view_user_summary" style="display:block; margin-bottom:4px;">0 Records</strong>
                <span id="view_user_detail" style="font-size:12.5px; color:#475569;">—</span>
            </div>
        </div>
        <div class="adm-modal-foot">
            <form id="formModalDeleteUser" method="POST" action="" style="margin:0;" onsubmit="return confirm('Are you sure you want to permanently delete this user profile?');">
                @csrf
                @method('DELETE')
                <button type="submit" id="btnModalDeleteUser" class="btn-action btn-delete" style="padding:8px 14px;">
                    🗑️ Delete User Profile
                </button>
            </form>
            <button type="button" class="btn-action btn-neutral" style="padding:8px 16px;" onclick="closeModal('modalViewUser')">
                Close
            </button>
        </div>
    </div>
</div>

<script>
let currentUserFilter = 'all';

function switchAdTab(tabId, btn) {
    document.querySelectorAll('.adm-tab-pane').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.adm-tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).style.display = 'block';
    btn.classList.add('active');
}

function filterUsersTable(filterType, btn) {
    currentUserFilter = filterType;
    document.querySelectorAll('.adm-filter-pill').forEach(el => el.classList.remove('active'));
    btn.classList.add('active');
    applyUserFilters();
}

function applyUserFilters() {
    const query = (document.getElementById('userSearchInput').value || '').toLowerCase().trim();
    document.querySelectorAll('.adm-user-row').forEach(row => {
        const role = row.getAttribute('data-role');
        const online = row.getAttribute('data-online') === '1';
        const searchBlob = row.getAttribute('data-search') || '';

        let matchesFilter = true;
        if (currentUserFilter === 'online') {
            matchesFilter = online;
        } else if (currentUserFilter !== 'all') {
            matchesFilter = (role === currentUserFilter);
        }

        const matchesQuery = !query || searchBlob.includes(query);
        row.style.display = (matchesFilter && matchesQuery) ? '' : 'none';
    });
}

function filterEldersTable() {
    const query = (document.getElementById('elderSearchInput').value || '').toLowerCase().trim();
    document.querySelectorAll('.adm-elder-row').forEach(row => {
        const searchBlob = row.getAttribute('data-search') || '';
        row.style.display = (!query || searchBlob.includes(query)) ? '' : 'none';
    });
}

function openViewUserModal(btn) {
    const name = btn.getAttribute('data-user-name') || 'User';
    document.getElementById('view_user_avatar').textContent = name.charAt(0).toUpperCase();
    document.getElementById('view_user_name').textContent = name;
    document.getElementById('view_user_email').textContent = btn.getAttribute('data-user-email') || '';
    document.getElementById('view_user_phone').textContent = btn.getAttribute('data-user-phone') || 'Not provided';
    document.getElementById('view_user_role').textContent = btn.getAttribute('data-user-role') || '';
    document.getElementById('view_user_status').textContent = btn.getAttribute('data-user-status') || '';
    document.getElementById('view_user_joined').textContent = btn.getAttribute('data-user-joined') || '';
    document.getElementById('view_user_summary').textContent = btn.getAttribute('data-user-summary') || '';
    document.getElementById('view_user_detail').textContent = btn.getAttribute('data-user-detail') || '';

    const canDelete = btn.getAttribute('data-can-delete') === '1';
    const deleteForm = document.getElementById('formModalDeleteUser');
    if (canDelete) {
        deleteForm.action = btn.getAttribute('data-delete-url');
        deleteForm.style.display = 'block';
    } else {
        deleteForm.style.display = 'none';
    }

    document.getElementById('modalViewUser').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

window.onclick = function(e) {
    if (e.target.classList.contains('adm-modal-backdrop')) {
        e.target.style.display = 'none';
    }
};
</script>
@endsection
