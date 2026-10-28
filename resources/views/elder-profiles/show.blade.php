@extends('layouts.app')

@section('title', 'Elder Profile - ' . $elder->full_name)
@section('page-heading', 'Elder Profile Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/elder-profile.css') }}">
@endpush

@section('content')
<div class="ep-container">

    <div class="ep-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('dashboard') }}">Family Overview</a>
        <span>/</span>
        <strong>Elder Care Profile</strong>
    </div>

    <div class="ep-grid">

        <!-- Left Profile Card (Figure 4.9) -->
        <div class="ep-sidebar-card">
            <div class="ep-photo-container">
                @if($elder->photo_url)
                    <img src="{{ $elder->photo_url }}" alt="{{ $elder->full_name }}" class="ep-photo">
                @else
                    <div class="ep-photo-fallback">
                        {{ strtoupper(substr($elder->full_name, 0, 1)) }}
                    </div>
                @endif
                <div class="ep-status-pill">
                    <span class="ep-status-dot"></span>
                    <span>Stable Status</span>
                </div>
            </div>

            <div class="ep-profile-details">
                <div class="ep-profile-header">
                    <div>
                        <h1 class="ep-name">{{ $elder->full_name }}</h1>
                        <p class="ep-meta-line">
                            {{ $elder->age ? $elder->age . ' Years Old' : 'Elder Patient' }}
                            @if($elder->residence)
                                · {{ $elder->residence }}
                            @endif
                        </p>
                    </div>

                    <a href="{{ route('elder.profile.edit', $elder->id) }}" class="ep-edit-icon-btn" title="Edit Elder Profile">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </a>
                </div>

                <div class="ep-demographics">
                    <div class="ep-demo-item">
                        <small>Date of Birth</small>
                        <strong>{{ $elder->date_of_birth ? $elder->date_of_birth->format('M d, Y') : 'Not specified' }}</strong>
                    </div>
                    <div class="ep-demo-item">
                        <small>Blood Type</small>
                        <strong>{{ $elder->blood_type ?? 'O+' }}</strong>
                    </div>
                    <div class="ep-demo-item">
                        <small>Physician</small>
                        <strong>{{ $elder->physician_name ?? 'Dr. Obonyo' }}</strong>
                    </div>
                    <div class="ep-demo-item">
                        <small>Gender</small>
                        <strong>{{ $elder->gender ?? 'Not Specified' }}</strong>
                    </div>
                </div>

                <div class="ep-mobility-section">
                    <div class="ep-mobility-header">
                        <span>Mobility Status</span>
                        <span class="ep-mobility-badge">{{ $elder->mobility_status ?? 'Assisted' }}</span>
                    </div>
                    <div class="ep-scale-bar">
                        <div class="ep-scale-fill" style="width: {{ $elder->independence_score ?? 65 }}%;"></div>
                    </div>
                    <div class="ep-scale-label">
                        <span>Independence Scale</span>
                        <span>{{ $elder->independence_score ?? 65 }}%</span>
                    </div>
                </div>

                @if($elder->assignedCaregiver)
                    <div style="margin-top: 18px; padding: 12px; background: #f8fafc; border-radius: 12px; border: 1px solid #eef2f7;">
                        <small style="display:block; font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase;">Assigned Caregiver</small>
                        <strong style="font-size:13px; color:#1e293b;">{{ $elder->assignedCaregiver->name }}</strong>
                        <span style="display:block; font-size:11px; color:#64748b;">{{ $elder->assignedCaregiver->phone ?? $elder->assignedCaregiver->email }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Content Panels (Figure 4.9) -->
        <div class="ep-content-area">

            <!-- Tab Headers -->
            <div class="ep-tabs">
                <button type="button" class="ep-tab-btn active" onclick="switchEpTab('tab-overview', this)">Overview</button>
                <button type="button" class="ep-tab-btn" onclick="switchEpTab('tab-history', this)">Medical History</button>
                <button type="button" class="ep-tab-btn" onclick="switchEpTab('tab-careplan', this)">Care Plan & Notes</button>
            </div>

            <!-- Tab 1: Overview -->
            <div id="tab-overview" class="ep-tab-pane">

                <!-- Active Medications -->
                <div class="ep-section-card">
                    <div class="ep-section-head">
                        <h2 class="ep-section-title">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/></svg>
                            Active Medications
                        </h2>
                        <a href="{{ route('elder.profile.edit', $elder->id) }}" class="ep-action-link">Manage Prescriptions →</a>
                    </div>

                    @if($elder->medicationList->isNotEmpty())
                        <div class="ep-med-list">
                            @foreach($elder->medicationList as $med)
                                <div class="ep-med-card">
                                    <div>
                                        <h3 class="ep-med-name">{{ $med->name }}</h3>
                                        <p class="ep-med-dosage">{{ $med->dosage }} — {{ $med->schedule }}</p>
                                    </div>
                                    @if($med->category)
                                        <span class="ep-med-pill">{{ $med->category }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif($elder->medications)
                        <div style="padding:16px; background:#f8fafc; border-radius:12px; font-size:13px; line-height:1.6; color:#334155; white-space:pre-line;">
                            {{ $elder->medications }}
                        </div>
                    @else
                        <p style="color:#94a3b8; font-size:13px; margin:0;">No active medications currently listed.</p>
                    @endif
                </div>

                <!-- Critical Allergies -->
                <div class="ep-section-card">
                    <div class="ep-section-head">
                        <h2 class="ep-section-title">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Critical Allergies & Sensitivities
                        </h2>
                        <a href="{{ route('elder.profile.edit', $elder->id) }}" class="ep-action-link">Update Allergies →</a>
                    </div>

                    @if($elder->allergyList->isNotEmpty())
                        <div class="ep-allergy-list">
                            @foreach($elder->allergyList as $allergy)
                                <div class="ep-allergy-card severity-{{ $allergy->severity }}">
                                    <div>
                                        <h3 class="ep-allergy-name">{{ $allergy->allergen }}</h3>
                                        <p class="ep-allergy-reaction">Reaction: {{ $allergy->reaction ?? 'Adverse reaction' }}</p>
                                    </div>
                                    <span class="ep-badge-{{ strtolower($allergy->severity) }}">
                                        {{ strtoupper($allergy->severity) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @elseif($elder->allergies)
                        <div style="padding:16px; background:#f8fafc; border-radius:12px; font-size:13px; line-height:1.6; color:#334155; white-space:pre-line;">
                            {{ $elder->allergies }}
                        </div>
                    @else
                        <p style="color:#94a3b8; font-size:13px; margin:0;">No known allergies recorded.</p>
                    @endif
                </div>

                <!-- Emergency Contacts -->
                <div class="ep-section-card">
                    <div class="ep-section-head">
                        <h2 class="ep-section-title">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            Emergency Contacts
                        </h2>
                        <span style="font-size:11px; color:#94a3b8; font-weight:700;">VERIFIED FAMILY & CLINICIANS</span>
                    </div>

                    <div class="ep-contacts-grid">
                        <!-- Primary Family Member -->
                        <div class="ep-contact-card primary-contact">
                            <div class="ep-contact-info">
                                <div class="ep-contact-avatar">
                                    {{ strtoupper(substr($elder->emergency_contact_name, 0, 1)) }}
                                </div>
                                <div class="ep-contact-meta">
                                    <small>Primary Contact</small>
                                    <strong>{{ $elder->emergency_contact_name }}</strong>
                                    <p>{{ $elder->emergency_contact_phone }}</p>
                                </div>
                            </div>
                            <a href="tel:{{ $elder->emergency_contact_phone }}" class="ep-call-btn" title="Call Contact">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </a>
                        </div>

                        <!-- Physician -->
                        @if($elder->physician_name)
                            <div class="ep-contact-card">
                                <div class="ep-contact-info">
                                    <div class="ep-contact-avatar" style="background:#e0f2fe; color:#0369a1;">
                                        Dr
                                    </div>
                                    <div class="ep-contact-meta">
                                        <small>Primary Physician</small>
                                        <strong>{{ $elder->physician_name }}</strong>
                                        <p>{{ $elder->physician_phone ?? 'Clinic reachable' }}</p>
                                    </div>
                                </div>
                                @if($elder->physician_phone)
                                    <a href="tel:{{ $elder->physician_phone }}" class="ep-call-btn" title="Call Physician">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Tab 2: Medical History -->
            <div id="tab-history" class="ep-tab-pane" style="display:none;">
                <div class="ep-section-card">
                    <h2 class="ep-section-title" style="margin-bottom:14px;">Diagnosed Conditions & Chronic History</h2>
                    <div style="font-size:13px; line-height:1.7; color:#334155; white-space:pre-line;">
                        {{ $elder->medical_conditions ?? 'No specific chronic conditions cataloged yet.' }}
                    </div>
                </div>
            </div>

            <!-- Tab 3: Care Plan & Preferences -->
            <div id="tab-careplan" class="ep-tab-pane" style="display:none;">
                <div class="ep-section-card">
                    <h2 class="ep-section-title" style="margin-bottom:14px;">Daily Living & Dietary Preferences</h2>
                    <div style="font-size:13px; line-height:1.7; color:#334155; white-space:pre-line;">
                        {{ $elder->care_preferences ?? 'Standard home-based daily care routine.' }}
                    </div>
                </div>
            </div>

            <!-- Bottom Action Bar (Figure 4.9) -->
            <div class="ep-footer-bar">
                <span>Care Profile Last Synchronized: Today, {{ now()->format('h:i A') }}</span>

                <div class="ep-footer-btns">
                    <button type="button" onclick="window.print()" class="ep-btn-secondary">
                        Export Medical Summary
                    </button>

                    <a href="{{ route('elder.profile.edit', $elder->id) }}" class="ep-btn-secondary">
                        Update Care Record
                    </a>

                    <a href="{{ route('visits.create', $elder->id) }}" class="ep-btn-primary">
                        + Record Care Visit
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
function switchEpTab(tabId, btn) {
    document.querySelectorAll('.ep-tab-pane').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.ep-tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).style.display = 'block';
    btn.classList.add('active');
}
</script>
@endsection
