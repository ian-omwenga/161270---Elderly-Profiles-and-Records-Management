@extends('layouts.app')

@section('title', 'Visit Entry Record - ' . $elder->full_name)
@section('page-heading', 'Caregiver Visit Entry')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/caregiver-visit.css') }}">
@endpush

@section('content')
<div class="ve-container">

    <div class="ve-header">
        <div class="ve-header-title">
            <h1>
                <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Visit Entry Record
            </h1>
            <p>Log clinical activities, observations, and patient sentiment for clinical analysis.</p>
        </div>

        <div class="ve-chips">
            <div class="ve-chip">
                <div class="ve-chip-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="ve-chip-info">
                    <small>Caregiver</small>
                    <strong>{{ auth()->user()->name }}</strong>
                </div>
            </div>

            <div class="ve-chip">
                <div class="ve-chip-avatar" style="background:#e0f2fe; color:#0284c7; overflow:hidden;">
                    @if($elder->photo_url)
                        <img src="{{ $elder->photo_url }}" alt="{{ $elder->full_name }}" style="width:100%; height:100%; object-fit:cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';">
                        <span style="display:none; width:100%; height:100%; place-items:center;">{{ strtoupper(substr($elder->full_name, 0, 1)) }}</span>
                    @else
                        {{ strtoupper(substr($elder->full_name, 0, 1)) }}
                    @endif
                </div>
                <div class="ve-chip-info">
                    <small>Patient / Elder</small>
                    <strong>{{ $elder->full_name }}</strong>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="ec-flash ec-flash-error" style="background:#fee2e2; color:#991b1b; margin-bottom:20px; border-radius:12px; padding:16px;">
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('visits.store', $elder->id) }}">
        @csrf

        <div class="ve-grid">
            <!-- Left Primary Column -->
            <div class="ve-left">

                <!-- Visit Particulars -->
                <div class="ve-card">
                    <div class="ve-section-title">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Visit Particulars</span>
                    </div>
                    <p class="ve-section-desc">Standard scheduling and duration tracking for administrative compliance.</p>

                    <div class="ve-row">
                        <div class="ve-field">
                            <label for="visit_date">Date of Visit</label>
                            <input type="date" id="visit_date" name="visit_date" value="{{ old('visit_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="ve-field">
                            <label for="clock_in">Clock In</label>
                            <input type="time" id="clock_in" name="clock_in" value="{{ old('clock_in', '08:30') }}">
                        </div>
                        <div class="ve-field">
                            <label for="clock_out">Clock Out</label>
                            <input type="time" id="clock_out" name="clock_out" value="{{ old('clock_out', '14:00') }}">
                        </div>
                    </div>
                </div>

                <!-- Care & Support Activities -->
                <div class="ve-card">
                    <div class="ve-section-title">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                        <span>Care & Support Activities</span>
                    </div>
                    <p class="ve-section-desc">Select all clinical and daily living procedures performed during this session.</p>

                    <div class="ve-tasks-grid">
                        <label class="ve-task-item">
                            <input type="checkbox" name="tasks[]" value="Medication Administration" checked>
                            <div class="ve-task-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/></svg>
                                <span>Medication</span>
                            </div>
                        </label>

                        <label class="ve-task-item">
                            <input type="checkbox" name="tasks[]" value="Meal Preparation" checked>
                            <div class="ve-task-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                                <span>Meal Prep</span>
                            </div>
                        </label>

                        <label class="ve-task-item">
                            <input type="checkbox" name="tasks[]" value="Hygiene & Grooming">
                            <div class="ve-task-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12h.01M15 12h.01M10 16c.5.5 1.5.5 2 0"/><path d="M19 6.3a9 9 0 0 1 1.8 3.9 2 2 0 0 1 0 3.6 9 9 0 0 1-17.6 0 2 2 0 0 1 0-3.6A9 9 0 0 1 12 3c2 0 3.9.6 5.5 1.7"/></svg>
                                <span>Hygiene Care</span>
                            </div>
                        </label>

                        <label class="ve-task-item">
                            <input type="checkbox" name="tasks[]" value="Vitals & Biometrics Check" checked>
                            <div class="ve-task-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                <span>Vitals Check</span>
                            </div>
                        </label>

                        <label class="ve-task-item">
                            <input type="checkbox" name="tasks[]" value="Socialization & Companionship">
                            <div class="ve-task-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                <span>Socialization</span>
                            </div>
                        </label>

                        <label class="ve-task-item">
                            <input type="checkbox" name="tasks[]" value="Mobility & Physio Exercises">
                            <div class="ve-task-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m13 2-2 2.5V8l2 3.5-3 5.5 2 3"/><circle cx="12" cy="2" r="1"/></svg>
                                <span>Mobility</span>
                            </div>
                        </label>

                        <label class="ve-task-item">
                            <input type="checkbox" name="tasks[]" value="Recreation & Cognitive Games">
                            <div class="ve-task-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>
                                <span>Recreation</span>
                            </div>
                        </label>

                        <label class="ve-task-item">
                            <input type="checkbox" name="tasks[]" value="Housekeeping & Sanitization">
                            <div class="ve-task-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                <span>Housekeeping</span>
                            </div>
                        </label>

                        <label class="ve-task-item">
                            <input type="checkbox" name="tasks[]" value="Escort & Appointments">
                            <div class="ve-task-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 16 16 12 12 8"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                <span>Escort</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Clinical Observations (VADER Analyzed) -->
                <div class="ve-card">
                    <div class="ve-section-title">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        <span>Clinical Observations & Sentiment Input</span>
                    </div>
                    <p class="ve-section-desc">Record detailed physical and cognitive shifts. Notes are analyzed in real-time by the VADER NLP model.</p>

                    <div class="ve-field" style="margin-bottom:18px;">
                        <label for="visit_note">Physical Health Status & Observation Note *</label>
                        <textarea
                            id="visit_note"
                            name="visit_note"
                            placeholder="Enter details about mobility, pain levels, appetite, or behavioral observations (e.g. 'Patient was in good spirits, walked with assistance and took lunch well.')..."
                            required
                        >{{ old('visit_note') }}</textarea>
                    </div>

                    <div class="ve-field">
                        <label for="cognitive_notes">Cognitive & Emotional Notes (Optional)</label>
                        <textarea
                            id="cognitive_notes"
                            name="cognitive_notes"
                            placeholder="Note any confusion, memory lapses, distress, or significant psychological variations..."
                        >{{ old('cognitive_notes') }}</textarea>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="ve-right">

                <!-- Patient Sentiment & Health Indicators -->
                <div class="ve-card">
                    <span class="ve-ai-badge">VADER Evaluated Metric</span>

                    <div class="ve-section-title">Patient Mood & Responsiveness</div>
                    <p class="ve-section-desc">Assess the overall emotional state observed today.</p>

                    <div class="ve-mood-selector">
                        <label class="ve-mood-btn" title="Distressed (1/5)">
                            <input type="radio" name="mood_score" value="1" {{ old('mood_score') == 1 ? 'checked' : '' }}>
                            <span class="ve-mood-icon">😞</span>
                            <span>Distressed</span>
                        </label>
                        <label class="ve-mood-btn" title="Uneasy (2/5)">
                            <input type="radio" name="mood_score" value="2" {{ old('mood_score') == 2 ? 'checked' : '' }}>
                            <span class="ve-mood-icon">😟</span>
                            <span>Uneasy</span>
                        </label>
                        <label class="ve-mood-btn" title="Neutral (3/5)">
                            <input type="radio" name="mood_score" value="3" {{ old('mood_score', 3) == 3 ? 'checked' : '' }}>
                            <span class="ve-mood-icon">😐</span>
                            <span>Neutral</span>
                        </label>
                        <label class="ve-mood-btn" title="Content (4/5)">
                            <input type="radio" name="mood_score" value="4" {{ old('mood_score') == 4 ? 'checked' : '' }}>
                            <span class="ve-mood-icon">🙂</span>
                            <span>Content</span>
                        </label>
                        <label class="ve-mood-btn" title="Joyful (5/5)">
                            <input type="radio" name="mood_score" value="5" {{ old('mood_score') == 5 ? 'checked' : '' }}>
                            <span class="ve-mood-icon">😊</span>
                            <span>Joyful</span>
                        </label>
                    </div>

                    <div class="ve-row" style="margin-top:16px;">
                        <div class="ve-field">
                            <label for="appetite_score">Appetite (1-5)</label>
                            <select id="appetite_score" name="appetite_score">
                                <option value="1" {{ old('appetite_score') == 1 ? 'selected' : '' }}>1 - Poor (Refused meals)</option>
                                <option value="2" {{ old('appetite_score') == 2 ? 'selected' : '' }}>2 - Fair (Small portion)</option>
                                <option value="3" {{ old('appetite_score', 3) == 3 ? 'selected' : '' }}>3 - Normal / Average</option>
                                <option value="4" {{ old('appetite_score') == 4 ? 'selected' : '' }}>4 - Good appetite</option>
                                <option value="5" {{ old('appetite_score') == 5 ? 'selected' : '' }}>5 - Hearty / Finished all</option>
                            </select>
                        </div>

                        <div class="ve-field">
                            <label for="pain_level">Reported Pain (1-5)</label>
                            <select id="pain_level" name="pain_level">
                                <option value="1" {{ old('pain_level', 1) == 1 ? 'selected' : '' }}>1 - None / Comfortable</option>
                                <option value="2" {{ old('pain_level') == 2 ? 'selected' : '' }}>2 - Mild discomfort</option>
                                <option value="3" {{ old('pain_level') == 3 ? 'selected' : '' }}>3 - Moderate pain</option>
                                <option value="4" {{ old('pain_level') == 4 ? 'selected' : '' }}>4 - Severe pain</option>
                                <option value="5" {{ old('pain_level') == 5 ? 'selected' : '' }}>5 - Very severe pain</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-top:16px; padding:12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px;">
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:12px; font-weight:700; color:#334155;">
                            <input type="checkbox" name="medication_taken" value="1" style="width:18px; height:18px; accent-color:#2563eb;" checked>
                            <span>Scheduled Medication Administered / Taken</span>
                        </label>
                    </div>

                    <!-- Urgent Safety Concern Checkbox -->
                    <div class="ve-urgent-box">
                        <input type="checkbox" id="urgent_concern" name="urgent_concern" value="1">
                        <div>
                            <strong>URGENT CONCERN DETECTED?</strong>
                            <p>Check if any vital signs were outside of normal parameters, a fall occurred, or emergency contact is required.</p>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="ve-actions">
                        <button type="submit" class="ve-submit-btn">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Complete & Log Visit
                        </button>

                        <a href="{{ route('caregiver.dashboard') }}" class="ve-cancel-btn">
                            Cancel Entry
                        </a>
                    </div>
                </div>

                <!-- Previous Visit Summary -->
                @php
                    $lastVisit = $elder->careVisits()->latest('visit_date')->first();
                @endphp
                @if($lastVisit)
                    <div class="ve-card ve-prev-card">
                        <small>PREVIOUS VISIT SUMMARY</small>
                        <strong style="display:block; font-size:12px; color:#1e293b; margin-bottom:4px;">
                            {{ $lastVisit->visit_date ? $lastVisit->visit_date->format('M d, Y') : 'Recent Visit' }} · {{ $lastVisit->caregiver->name ?? 'Caregiver' }}
                        </strong>
                        <p>"{{ \Illuminate\Support\Str::limit($lastVisit->visit_note ?? 'Routine care completed smoothly.', 130) }}"</p>
                        <a href="{{ route('visits.index', $elder->id) }}" style="display:inline-block; margin-top:8px; font-size:11px; color:#2563eb; font-weight:700; text-decoration:none;">
                            View full care history →
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </form>

</div>
@endsection
