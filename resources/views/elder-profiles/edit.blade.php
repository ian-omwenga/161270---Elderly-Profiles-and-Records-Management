@extends('layouts.app')

@section('title', 'Edit Elder Profile - ' . $elder->full_name)
@section('page-heading', 'Edit Elder Profile')

@section('content')
<style>
.ep-form-container { max-width: 950px; margin: 0 auto; padding: 24px clamp(16px, 2.5vw, 36px) 40px; color: #1e293b; }
.ep-form-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 28px; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03); margin-bottom: 24px; }
.ep-form-head { margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; }
.ep-form-head h1 { font: 800 24px 'Manrope', sans-serif; color: #0f172a; margin: 0 0 6px; }
.ep-form-head p { margin: 0; color: #64748b; font-size: 13px; }
.ep-section-divider { font: 800 15px 'Manrope', sans-serif; color: #0f172a; margin: 28px 0 14px; padding-bottom: 8px; border-bottom: 1px dashed #e2e8f0; display: flex; align-items: center; justify-content: space-between; }
.ep-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 16px; }
.ep-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
.ep-field label { font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
.ep-field input, .ep-field select, .ep-field textarea { padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; background: #f8fafc; font-size: 13px; color: #0f172a; font-family: inherit; }
.ep-field input:focus, .ep-field select:focus, .ep-field textarea:focus { outline: none; border-color: #2563eb; background: #fff; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
.ep-field textarea { min-height: 80px; resize: vertical; }
.ep-repeat-row { display: grid; grid-template-columns: 2fr 1fr 1.5fr 1fr 40px; gap: 10px; align-items: center; margin-bottom: 10px; }
.ep-repeat-btn { padding: 6px 12px; background: #eff6ff; color: #2563eb; border: 1px dashed #93c5fd; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; }
.ep-del-btn { background: #fee2e2; color: #b91c1c; border: none; border-radius: 8px; width: 36px; height: 36px; font-size: 16px; font-weight: 700; cursor: pointer; display: grid; place-items: center; }
</style>

<div class="ep-form-container">

    @if ($errors->any())
        <div class="ec-flash ec-flash-error" style="background:#fee2e2; color:#991b1b; margin-bottom:20px; border-radius:12px; padding:16px;">
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ep-form-card">
        <div class="ep-form-head">
            <h1>Update Elder Profile</h1>
            <p>Update vital metrics, prescriptions, allergies, and caregiver assignments for {{ $elder->full_name }}.</p>
        </div>

        <form method="POST" action="{{ route('elder.profile.update', $elder->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Demographics & Photo -->
            <div class="ep-section-divider">
                <span>Personal & Demographic Information</span>
            </div>

            <div class="ep-row">
                <div class="ep-field">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $elder->full_name) }}" required>
                </div>
                <div class="ep-field">
                    <label for="date_of_birth">Date of Birth *</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', optional($elder->date_of_birth)->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="ep-row">
                <div class="ep-field">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="Male" {{ old('gender', $elder->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $elder->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $elder->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="ep-field">
                    <label for="blood_type">Blood Type</label>
                    <select id="blood_type" name="blood_type">
                        @foreach(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'] as $bt)
                            <option value="{{ $bt }}" {{ old('blood_type', $elder->blood_type) === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ep-field">
                    <label for="photo">Change Profile Photo</label>
                    <input type="file" id="photo" name="photo" accept="image/*">
                </div>
            </div>

            @if($elder->photo_url)
                <div style="margin-bottom:14px; display:flex; align-items:center; gap:12px;">
                    <img src="{{ $elder->photo_url }}" alt="Current Photo" style="width:50px; height:50px; border-radius:10px; object-fit:cover;">
                    <span style="font-size:12px; color:#64748b;">Current profile image active. Uploading a new file will replace it.</span>
                </div>
            @endif

            <div class="ep-field">
                <label for="residence">Residence / Home Location</label>
                <input type="text" id="residence" name="residence" value="{{ old('residence', $elder->residence) }}" placeholder="e.g. Lang'ata Homes, Wing B, Nairobi">
            </div>

            <!-- Mobility & Clinical Team -->
            <div class="ep-section-divider">
                <span>Mobility Status & Physician</span>
            </div>

            <div class="ep-row">
                <div class="ep-field">
                    <label for="mobility_status">Mobility Status</label>
                    <select id="mobility_status" name="mobility_status">
                        <option value="Independent" {{ old('mobility_status', $elder->mobility_status) === 'Independent' ? 'selected' : '' }}>Independent</option>
                        <option value="Assisted" {{ old('mobility_status', $elder->mobility_status) === 'Assisted' ? 'selected' : '' }}>Needs Assistance / Assisted</option>
                        <option value="Limited Mobility" {{ old('mobility_status', $elder->mobility_status) === 'Limited Mobility' ? 'selected' : '' }}>Limited Mobility</option>
                        <option value="Bedridden" {{ old('mobility_status', $elder->mobility_status) === 'Bedridden' ? 'selected' : '' }}>Bedridden</option>
                    </select>
                </div>
                <div class="ep-field">
                    <label for="independence_score">Independence Scale (%)</label>
                    <input type="number" id="independence_score" name="independence_score" value="{{ old('independence_score', $elder->independence_score ?? 65) }}" min="0" max="100">
                </div>
                <div class="ep-field">
                    <label for="assigned_caregiver_id">Assigned Primary Caregiver</label>
                    <select id="assigned_caregiver_id" name="assigned_caregiver_id">
                        <option value="">-- Unassigned --</option>
                        @foreach($caregivers as $cg)
                            <option value="{{ $cg->id }}" {{ old('assigned_caregiver_id', $elder->assigned_caregiver_id) == $cg->id ? 'selected' : '' }}>
                                {{ $cg->name }} ({{ $cg->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="ep-row">
                <div class="ep-field">
                    <label for="physician_name">Primary Physician</label>
                    <input type="text" id="physician_name" name="physician_name" value="{{ old('physician_name', $elder->physician_name) }}" placeholder="e.g. Dr. Stephen Obonyo">
                </div>
                <div class="ep-field">
                    <label for="physician_phone">Physician Phone</label>
                    <input type="text" id="physician_phone" name="physician_phone" value="{{ old('physician_phone', $elder->physician_phone) }}" placeholder="+254 700 000 000">
                </div>
            </div>

            <!-- Active Structured Medications -->
            <div class="ep-section-divider">
                <span>Active Prescriptions & Medications</span>
                <button type="button" class="ep-repeat-btn" onclick="addMedicationRow()">+ Add Medication</button>
            </div>

            <div id="medications-container">
                @forelse($elder->medicationList as $med)
                    <div class="ep-repeat-row">
                        <input type="text" name="med_name[]" placeholder="Medication Name" value="{{ $med->name }}" required>
                        <input type="text" name="med_dosage[]" placeholder="Dosage" value="{{ $med->dosage }}">
                        <input type="text" name="med_schedule[]" placeholder="Schedule" value="{{ $med->schedule }}">
                        <input type="text" name="med_category[]" placeholder="Category" value="{{ $med->category }}">
                        <button type="button" class="ep-del-btn" onclick="this.parentElement.remove()">×</button>
                    </div>
                @empty
                    <div class="ep-repeat-row">
                        <input type="text" name="med_name[]" placeholder="Medication Name (e.g. Lisinopril)">
                        <input type="text" name="med_dosage[]" placeholder="Dosage (10mg)">
                        <input type="text" name="med_schedule[]" placeholder="Schedule (Daily Morning)">
                        <input type="text" name="med_category[]" placeholder="Category (Hypertension)">
                        <button type="button" class="ep-del-btn" onclick="this.parentElement.remove()">×</button>
                    </div>
                @endforelse
            </div>

            <!-- Structured Allergies -->
            <div class="ep-section-divider">
                <span>Known Allergies & Sensitivities</span>
                <button type="button" class="ep-repeat-btn" onclick="addAllergyRow()">+ Add Allergy</button>
            </div>

            <div id="allergies-container">
                @forelse($elder->allergyList as $allergy)
                    <div class="ep-repeat-row" style="grid-template-columns: 2fr 2fr 1.5fr 40px;">
                        <input type="text" name="allergy_item[]" placeholder="Allergen" value="{{ $allergy->allergen }}" required>
                        <input type="text" name="allergy_reaction[]" placeholder="Reaction" value="{{ $allergy->reaction }}">
                        <select name="allergy_severity[]">
                            <option value="High" {{ $allergy->severity === 'High' ? 'selected' : '' }}>High Severity</option>
                            <option value="Medium" {{ $allergy->severity === 'Medium' ? 'selected' : '' }}>Medium Severity</option>
                            <option value="Low" {{ $allergy->severity === 'Low' ? 'selected' : '' }}>Low Severity</option>
                        </select>
                        <button type="button" class="ep-del-btn" onclick="this.parentElement.remove()">×</button>
                    </div>
                @empty
                    <div class="ep-repeat-row" style="grid-template-columns: 2fr 2fr 1.5fr 40px;">
                        <input type="text" name="allergy_item[]" placeholder="Allergen (e.g. Penicillin)">
                        <input type="text" name="allergy_reaction[]" placeholder="Reaction (e.g. Anaphylaxis)">
                        <select name="allergy_severity[]">
                            <option value="High">High Severity</option>
                            <option value="Medium" selected>Medium Severity</option>
                            <option value="Low">Low Severity</option>
                        </select>
                        <button type="button" class="ep-del-btn" onclick="this.parentElement.remove()">×</button>
                    </div>
                @endforelse
            </div>

            <!-- Medical Conditions & Care Preferences -->
            <div class="ep-section-divider">
                <span>Clinical Notes & Preferences</span>
            </div>

            <div class="ep-field">
                <label for="medical_conditions">Diagnosed Medical Conditions</label>
                <textarea id="medical_conditions" name="medical_conditions">{{ old('medical_conditions', $elder->medical_conditions) }}</textarea>
            </div>

            <div class="ep-field">
                <label for="care_preferences">Care & Dietary Preferences</label>
                <textarea id="care_preferences" name="care_preferences">{{ old('care_preferences', $elder->care_preferences) }}</textarea>
            </div>

            <!-- Emergency Contact -->
            <div class="ep-section-divider">
                <span>Primary Emergency Contact</span>
            </div>

            <div class="ep-row">
                <div class="ep-field">
                    <label for="emergency_contact_name">Contact Person Full Name *</label>
                    <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $elder->emergency_contact_name) }}" required>
                </div>
                <div class="ep-field">
                    <label for="emergency_contact_phone">Contact Phone Number *</label>
                    <input type="text" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $elder->emergency_contact_phone) }}" required>
                </div>
            </div>

            <div style="margin-top:28px; display:flex; gap:12px;">
                <button type="submit" class="ep-btn-primary" style="padding:12px 24px; font-size:14px;">
                    Save Changes & Update
                </button>
                <a href="{{ route('elder.show', $elder->id) }}" class="ep-btn-secondary" style="padding:12px 20px; font-size:14px; display:inline-flex; align-items:center;">
                    Cancel
                </a>
            </div>

        </form>
    </div>

</div>

<script>
function addMedicationRow() {
    const container = document.getElementById('medications-container');
    const div = document.createElement('div');
    div.className = 'ep-repeat-row';
    div.innerHTML = `
        <input type="text" name="med_name[]" placeholder="Medication Name" required>
        <input type="text" name="med_dosage[]" placeholder="Dosage (e.g. 500mg)">
        <input type="text" name="med_schedule[]" placeholder="Schedule (e.g. Twice Daily)">
        <input type="text" name="med_category[]" placeholder="Category (e.g. Diabetes)">
        <button type="button" class="ep-del-btn" onclick="this.parentElement.remove()">×</button>
    `;
    container.appendChild(div);
}

function addAllergyRow() {
    const container = document.getElementById('allergies-container');
    const div = document.createElement('div');
    div.className = 'ep-repeat-row';
    div.style.gridTemplateColumns = '2fr 2fr 1.5fr 40px';
    div.innerHTML = `
        <input type="text" name="allergy_item[]" placeholder="Allergen" required>
        <input type="text" name="allergy_reaction[]" placeholder="Reaction">
        <select name="allergy_severity[]">
            <option value="High">High Severity</option>
            <option value="Medium" selected>Medium Severity</option>
            <option value="Low">Low Severity</option>
        </select>
        <button type="button" class="ep-del-btn" onclick="this.parentElement.remove()">×</button>
    `;
    container.appendChild(div);
}
</script>
@endsection
