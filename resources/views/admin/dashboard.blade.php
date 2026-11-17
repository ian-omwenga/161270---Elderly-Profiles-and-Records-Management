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

/* Tabs */
.ad-tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; }
.ad-tab-btn { background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s; }
.ad-tab-btn:hover { background: #f8fafc; color: #0f172a; }
.ad-tab-btn.active { background: #0f172a; color: #ffffff; border-color: #0f172a; }
.ad-tab-badge { padding: 2px 7px; border-radius: 9999px; font-size: 11px; background: rgba(255,255,255,0.2); }
.ad-tab-btn:not(.active) .ad-tab-badge { background: #e2e8f0; color: #334155; }

/* Cards & Tables */
.ad-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 24px; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03); margin-bottom: 24px; }
.ad-card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
.ad-card-title { font: 800 18px 'Manrope', sans-serif; color: #0f172a; margin: 0; }
.ad-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
.ad-table th { background: #f8fafc; padding: 12px 14px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
.ad-table td { padding: 14px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
.ad-role-badge { padding: 4px 9px; border-radius: 6px; font-size: 10.5px; font-weight: 800; text-transform: uppercase; display: inline-block; }
.role-family { background: #e0f2fe; color: #0369a1; }
.role-caregiver { background: #fef3c7; color: #b45309; }
.role-admin { background: #fee2e2; color: #b91c1c; }

/* Action Buttons */
.btn-action { display: inline-flex; align-items: center; gap: 4px; padding: 6px 11px; border-radius: 7px; font-size: 11.5px; font-weight: 700; text-decoration: none; cursor: pointer; transition: all 0.15s; border: 1px solid transparent; }
.btn-edit { background: #f1f5f9; color: #334155; border-color: #cbd5e1; }
.btn-edit:hover { background: #e2e8f0; color: #0f172a; }
.btn-delete { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
.btn-delete:hover { background: #fee2e2; color: #b91c1c; }
.btn-view { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
.btn-view:hover { background: #dbeafe; color: #1d4ed8; }

/* Modal */
.ad-modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 100; align-items: center; justify-content: center; padding: 20px; }
.ad-modal { background: #ffffff; border-radius: 18px; width: 100%; max-width: 540px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden; animation: popIn 0.2s ease-out; }
.ad-modal-head { display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-bottom: 1px solid #f1f5f9; }
.ad-modal-head h3 { margin: 0; font: 800 17px 'Manrope', sans-serif; color: #0f172a; }
.ad-modal-close { background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer; line-height: 1; }
.ad-modal-body { padding: 24px; max-height: 75vh; overflow-y: auto; }
.ad-form-group { margin-bottom: 16px; display: flex; flex-direction: column; gap: 6px; }
.ad-form-group label { font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
.ad-form-group input, .ad-form-group select { padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 9px; font-size: 13px; color: #0f172a; background: #f8fafc; font-family: inherit; }
.ad-form-group input:focus, .ad-form-group select:focus { outline: none; border-color: #2563eb; background: #fff; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
.ad-modal-foot { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; }

@keyframes popIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
</style>

<div class="ad-container">

    <div class="ad-header">
        <div class="ad-header-info">
            <h1>Welcome back, {{ auth()->user()->name }}</h1>
            <p>System Administrator Console · Managing users, caregivers, elder profiles, and NLP clinical analytics</p>
        </div>
        <div style="display:flex; gap:10px;">
            <button type="button" onclick="window.print()" class="ep-btn-secondary" style="padding:10px 16px; border-radius:10px; font-weight:700; cursor:pointer;">
                Export System Report
            </button>
        </div>
    </div>

    @if(session('status'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:14px 18px; border-radius:12px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            ✓ {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:14px 18px; border-radius:12px; margin-bottom:20px; font-size:13px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
            <p>Active Patient Records</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="ad-tabs">
        <button type="button" class="ad-tab-btn active" onclick="switchAdTab('tab-users', this)">
            <span>👥 User Accounts</span>
            <span class="ad-tab-badge">{{ $totalUsers }}</span>
        </button>
        <button type="button" class="ad-tab-btn" onclick="switchAdTab('tab-elders', this)">
            <span>🩺 Elder Care Profiles</span>
            <span class="ad-tab-badge">{{ $totalElders }}</span>
        </button>
        <button type="button" class="ad-tab-btn" onclick="switchAdTab('tab-activity', this)">
            <span>📋 Live Care Activity</span>
            <span class="ad-tab-badge">{{ $recentVisits->count() }}</span>
        </button>
    </div>

    <!-- TAB 1: USER ACCOUNTS (Caregivers, Families, Admins) -->
    <div id="tab-users" class="ad-tab-pane">
        <div class="ad-card">
            <div class="ad-card-head">
                <div>
                    <h2 class="ad-card-title">User Accounts Management</h2>
                    <span style="font-size:12px; color:#64748b;">Manage caregivers, family members, and administrative platform access</span>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>User Name & Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Registered</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <strong style="color:#0f172a; display:block;">{{ $user->name }}</strong>
                                    <span style="font-size:11.5px; color:#64748b;">{{ $user->email }}</span>
                                </td>
                                <td>
                                    <span style="font-size:12px; color:#475569;">{{ $user->phone ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="ad-role-badge role-{{ $user->role }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size:11.5px; color:#64748b;">
                                        {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:inline-flex; gap:6px;">
                                        <button type="button" class="btn-action btn-edit" 
                                                onclick="openEditUserModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ addslashes($user->phone ?? '') }}', '{{ $user->role }}')">
                                            ✏️ Edit
                                        </button>

                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete the user profile for {{ addslashes($user->name) }}? This will permanently delete their account and unassign related records.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-delete">
                                                    🗑️ Delete
                                                </button>
                                            </form>
                                        @else
                                            <span style="font-size:11px; color:#94a3b8; padding:6px 8px;">(Current Admin)</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:18px;">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- TAB 2: ELDER PROFILES MANAGEMENT -->
    <div id="tab-elders" class="ad-tab-pane" style="display:none;">
        <div class="ad-card">
            <div class="ad-card-head">
                <div>
                    <h2 class="ad-card-title">Elder Care Profiles Management</h2>
                    <span style="font-size:12px; color:#64748b;">Review all elder profiles, reassign caregivers, update contacts/physicians, or delete profiles</span>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Elder Patient</th>
                            <th>Residence</th>
                            <th>Assigned Caregiver</th>
                            <th>Primary Physician</th>
                            <th>Emergency Contact</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($elders as $elder)
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div style="width:36px; height:36px; border-radius:10px; background:#eff6ff; color:#2563eb; display:grid; place-items:center; font-weight:800; font-size:13px; flex-shrink:0; overflow:hidden;">
                                            @if($elder->photo_url)
                                                <img src="{{ $elder->photo_url }}" alt="{{ $elder->full_name }}" style="width:100%; height:100%; object-fit:cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';">
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
                                    <div style="display:inline-flex; gap:6px;">
                                        <a href="{{ route('elder.show', $elder->id) }}" class="btn-action btn-view" title="Inspect Profile">
                                            👁️ View
                                        </a>

                                        <button type="button" class="btn-action btn-edit"
                                                onclick="openEditElderModal({{ $elder->id }}, '{{ addslashes($elder->full_name) }}', '{{ $elder->assigned_caregiver_id }}', '{{ addslashes($elder->residence ?? '') }}', '{{ addslashes($elder->physician_name ?? '') }}', '{{ addslashes($elder->physician_phone ?? '') }}', '{{ addslashes($elder->emergency_contact_name) }}', '{{ addslashes($elder->emergency_contact_phone) }}')">
                                            ✏️ Edit Info
                                        </button>

                                        <form method="POST" action="{{ route('admin.elders.delete', $elder->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete the ENTIRE profile for {{ addslashes($elder->full_name) }}? This will permanently delete all associated care visits, sentiment records, and clinical logs.');">
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
                                <td colspan="6" style="text-align:center; padding:32px; color:#94a3b8;">No elder profiles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:18px;">
                {{ $elders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- TAB 3: LIVE ACTIVITY FEED -->
    <div id="tab-activity" class="ad-tab-pane" style="display:none;">
        <div class="ad-card">
            <div class="ad-card-head">
                <div>
                    <h2 class="ad-card-title">Live Care Activity Feed</h2>
                    <span style="font-size:12px; color:#64748b;">Chronological clinical log submissions and VADER sentiment classifications</span>
                </div>
                <span style="font-size:10px; font-weight:800; background:#dcfce7; color:#15803d; padding:3px 10px; border-radius:9999px;">SYSTEM ACTIVE</span>
            </div>

            <div class="ad-feed-list" style="display:flex; flex-direction:column; gap:12px;">
                @forelse($recentVisits as $visit)
                    <div style="display:flex; gap:14px; align-items:flex-start; padding:14px; background:#f8fafc; border-radius:12px; border:1px solid #f1f5f9;">
                        <div style="width:38px; height:38px; border-radius:10px; display:grid; place-items:center; font-size:15px; flex-shrink:0; background:#e0f2fe; color:#0369a1;">
                            📋
                        </div>
                        <div style="flex:1;">
                            <strong style="font-size:13px; color:#0f172a; display:block;">{{ $visit->caregiver->name ?? 'Caregiver' }} logged visit for {{ $visit->elder->full_name ?? 'Elder' }}</strong>
                            <p style="margin:4px 0 6px; font-size:12px; color:#64748b;">"{{ \Illuminate\Support\Str::limit($visit->visit_note ?? 'Routine care completed.', 110) }}"</p>
                            @if($visit->sentimentResult)
                                <span style="font-size:11px; font-weight:700; color: {{ $visit->sentimentResult->flagged ? '#b91c1c' : '#15803d' }};">
                                    VADER: {{ ucfirst($visit->sentimentResult->classification) }} (Score: {{ $visit->sentimentResult->compound_score }})
                                </span>
                            @endif
                            <small style="color:#94a3b8; font-size:11px; margin-left:8px;">{{ $visit->created_at ? $visit->created_at->diffForHumans() : 'Just now' }}</small>
                        </div>
                    </div>
                @empty
                    <p style="color:#94a3b8; font-size:13px; margin:0;">No care visits recorded recently.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- MODAL 1: EDIT USER (Admin edits minor user info) -->
<div id="modalEditUser" class="ad-modal-backdrop">
    <div class="ad-modal">
        <div class="ad-modal-head">
            <h3>Edit User Account</h3>
            <button type="button" class="ad-modal-close" onclick="closeModal('modalEditUser')">&times;</button>
        </div>
        <form id="formEditUser" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="ad-modal-body">
                <div class="ad-form-group">
                    <label for="edit_user_name">Full Name *</label>
                    <input type="text" id="edit_user_name" name="name" required>
                </div>
                <div class="ad-form-group">
                    <label for="edit_user_email">Email Address *</label>
                    <input type="email" id="edit_user_email" name="email" required>
                </div>
                <div class="ad-form-group">
                    <label for="edit_user_phone">Phone Number</label>
                    <input type="text" id="edit_user_phone" name="phone">
                </div>
                <div class="ad-form-group">
                    <label for="edit_user_role">Account Role *</label>
                    <select id="edit_user_role" name="role" required>
                        <option value="family">Family Member</option>
                        <option value="caregiver">Professional Caregiver</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
            </div>
            <div class="ad-modal-foot">
                <button type="button" class="btn-action btn-edit" style="padding:8px 14px;" onclick="closeModal('modalEditUser')">Cancel</button>
                <button type="submit" class="btn-action" style="background:#0f172a; color:#fff; padding:8px 16px;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: EDIT ELDER MINOR INFO (Admin edits minor info: caregiver assignment, contacts, physician) -->
<div id="modalEditElder" class="ad-modal-backdrop">
    <div class="ad-modal">
        <div class="ad-modal-head">
            <h3>Edit Elder Details (Administrative)</h3>
            <button type="button" class="ad-modal-close" onclick="closeModal('modalEditElder')">&times;</button>
        </div>
        <form id="formEditElder" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="ad-modal-body">
                <div class="ad-form-group">
                    <label>Elder Full Name</label>
                    <input type="text" id="edit_elder_name_display" disabled style="background:#f1f5f9; color:#64748b;">
                </div>
                <div class="ad-form-group">
                    <label for="edit_elder_caregiver">Assigned Caregiver</label>
                    <select id="edit_elder_caregiver" name="assigned_caregiver_id">
                        <option value="">-- Unassigned --</option>
                        @foreach($allCaregivers as $cg)
                            <option value="{{ $cg->id }}">{{ $cg->name }} ({{ $cg->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="ad-form-group">
                    <label for="edit_elder_residence">Residence / Location</label>
                    <input type="text" id="edit_elder_residence" name="residence" placeholder="e.g. Fedha, Nairobi">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="ad-form-group">
                        <label for="edit_elder_physician">Primary Physician</label>
                        <input type="text" id="edit_elder_physician" name="physician_name">
                    </div>
                    <div class="ad-form-group">
                        <label for="edit_elder_physician_phone">Physician Phone</label>
                        <input type="text" id="edit_elder_physician_phone" name="physician_phone">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="ad-form-group">
                        <label for="edit_elder_emergency_name">Emergency Contact *</label>
                        <input type="text" id="edit_elder_emergency_name" name="emergency_contact_name" required>
                    </div>
                    <div class="ad-form-group">
                        <label for="edit_elder_emergency_phone">Emergency Phone *</label>
                        <input type="text" id="edit_elder_emergency_phone" name="emergency_contact_phone" required>
                    </div>
                </div>
            </div>
            <div class="ad-modal-foot">
                <button type="button" class="btn-action btn-edit" style="padding:8px 14px;" onclick="closeModal('modalEditElder')">Cancel</button>
                <button type="submit" class="btn-action" style="background:#0f172a; color:#fff; padding:8px 16px;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchAdTab(tabId, btn) {
    document.querySelectorAll('.ad-tab-pane').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.ad-tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).style.display = 'block';
    btn.classList.add('active');
}

function openEditUserModal(id, name, email, phone, role) {
    const form = document.getElementById('formEditUser');
    form.action = '/admin/users/' + id;
    document.getElementById('edit_user_name').value = name;
    document.getElementById('edit_user_email').value = email;
    document.getElementById('edit_user_phone').value = phone || '';
    document.getElementById('edit_user_role').value = role;
    document.getElementById('modalEditUser').style.display = 'flex';
}

function openEditElderModal(id, name, caregiverId, residence, physicianName, physicianPhone, emergencyName, emergencyPhone) {
    const form = document.getElementById('formEditElder');
    form.action = '/admin/elders/' + id + '/minor';
    document.getElementById('edit_elder_name_display').value = name;
    document.getElementById('edit_elder_caregiver').value = caregiverId || '';
    document.getElementById('edit_elder_residence').value = residence || '';
    document.getElementById('edit_elder_physician').value = physicianName || '';
    document.getElementById('edit_elder_physician_phone').value = physicianPhone || '';
    document.getElementById('edit_elder_emergency_name').value = emergencyName;
    document.getElementById('edit_elder_emergency_phone').value = emergencyPhone;
    document.getElementById('modalEditElder').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

window.onclick = function(e) {
    if (e.target.classList.contains('ad-modal-backdrop')) {
        e.target.style.display = 'none';
    }
}
</script>
@endsection
