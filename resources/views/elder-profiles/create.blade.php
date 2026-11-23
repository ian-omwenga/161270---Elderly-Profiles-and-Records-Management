@extends('layouts.app')

@section('title', 'Create Elder Profile | ElderCare')
@section('page-heading', 'Create Elder Profile')

@section('content')
<style>
.ep-form-container {
    max-width: 960px;
    margin: 0 auto;
    padding: 24px clamp(16px, 3vw, 40px) 48px;
    color: #1e293b;
}

.ep-form-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: clamp(24px, 3.5vw, 36px);
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
}

.ep-form-head {
    margin-bottom: 28px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 20px;
}

.ep-form-head h1 {
    font: 800 26px/1.2 'Manrope', sans-serif;
    color: #0f172a;
    margin: 0 0 6px;
    letter-spacing: -0.5px;
}

.ep-form-head p {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

/* Photo Uploader Card */
.ep-photo-uploader-card {
    display: flex;
    align-items: center;
    gap: 24px;
    padding: 20px;
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    border-radius: 16px;
    margin-bottom: 28px;
    transition: border-color 0.2s ease, background 0.2s ease;
}

.ep-photo-uploader-card:hover {
    border-color: #3b82f6;
    background: #f0f7ff;
}

.ep-photo-avatar-box {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    background: #e2e8f0;
    display: grid;
    place-items: center;
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
    border: 3px solid #ffffff;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
}

.ep-photo-avatar-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
}

.ep-photo-avatar-placeholder {
    color: #64748b;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
}

.ep-photo-upload-meta {
    flex: 1;
}

.ep-photo-upload-meta h4 {
    margin: 0 0 4px;
    font-family: 'Manrope', sans-serif;
    font-weight: 700;
    font-size: 15px;
    color: #0f172a;
}

.ep-photo-upload-meta p {
    margin: 0 0 12px;
    font-size: 12.5px;
    color: #64748b;
}

.ep-photo-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.ep-btn-upload-trigger {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #2563eb;
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    border-radius: 10px;
    cursor: pointer;
    border: none;
    transition: all 0.15s ease;
}

.ep-btn-upload-trigger:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}

.ep-btn-remove-photo {
    padding: 8px 14px;
    background: #fee2e2;
    color: #b91c1c;
    font-size: 12px;
    font-weight: 700;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    display: none;
}

.ep-section-divider {
    font: 800 15px 'Manrope', sans-serif;
    color: #0f172a;
    margin: 32px 0 16px;
    padding-bottom: 10px;
    border-bottom: 1.5px dashed #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ep-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 18px;
    margin-bottom: 16px;
}

.ep-field {
    display: flex;
    flex-direction: column;
    gap: 7px;
    margin-bottom: 16px;
}

.ep-field label {
    font-size: 12px;
    font-weight: 700;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ep-field input,
.ep-field select,
.ep-field textarea {
    padding: 11px 15px;
    border: 1.5px solid #cbd5e1;
    border-radius: 11px;
    background: #f8fafc;
    font-size: 13.5px;
    color: #0f172a;
    font-family: inherit;
    outline: none;
    transition: all 0.18s ease;
}

.ep-field input:focus,
.ep-field select:focus,
.ep-field textarea:focus {
    border-color: #2563eb;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
}

.ep-field textarea {
    min-height: 85px;
    resize: vertical;
    line-height: 1.55;
}

.ep-repeat-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1.5fr 1fr 40px;
    gap: 10px;
    align-items: center;
    margin-bottom: 10px;
}

.ep-repeat-btn {
    padding: 7px 14px;
    background: #eff6ff;
    color: #2563eb;
    border: 1.5px dashed #93c5fd;
    border-radius: 9px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
}

.ep-repeat-btn:hover {
    background: #dbeafe;
    border-color: #3b82f6;
}

.ep-del-btn {
    background: #fee2e2;
    color: #b91c1c;
    border: none;
    border-radius: 9px;
    width: 38px;
    height: 38px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    display: grid;
    place-items: center;
    transition: background 0.15s ease;
}

.ep-del-btn:hover {
    background: #fecaca;
}

.ep-submit-row {
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 14px;
}

.ep-btn-primary {
    padding: 13px 28px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #ffffff;
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 700;
    border-radius: 11px;
    border: none;
    cursor: pointer;
    box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
    transition: all 0.18s ease;
}

.ep-btn-primary:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
    transform: translateY(-1px);
    box-shadow: 0 10px 22px rgba(37, 99, 235, 0.28);
}

.ep-btn-secondary {
    padding: 13px 22px;
    background: #f1f5f9;
    color: #475569;
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 700;
    border-radius: 11px;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: background 0.15s ease;
}

.ep-btn-secondary:hover {
    background: #e2e8f0;
    color: #1e293b;
    text-decoration: none;
}
</style>

<div class="ep-form-container">

    @if ($errors->any())
        <div style="background:#fee2e2; border:1px solid #fecaca; color:#991b1b; margin-bottom:24px; border-radius:14px; padding:16px 20px;">
            <div style="font-weight:700; margin-bottom:6px;">Please correct the following errors:</div>
            <ul style="margin:0; padding-left:20px; font-size:13px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ep-form-card">
        <div class="ep-form-head">
            <h1>Create Digital Elder Profile</h1>
            <p>Establish a centralized medical and personal care profile for home-based monitoring.</p>
        </div>

        <form method="POST" action="{{ route('elder.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Prominent Profile Photo Uploader Avenue -->
            <div class="ep-photo-uploader-card">
                <div class="ep-photo-avatar-box">
                    <img id="photo-preview-img" src="" alt="Elder Photo Preview">
                    <div id="photo-preview-placeholder" class="ep-photo-avatar-placeholder">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>No Photo</span>
                    </div>
                </div>

                <div class="ep-photo-upload-meta">
                    <h4>Elder Profile Photo</h4>
                    <p>Upload a clear photo of the elder to display across family, caregiver, and administrator interfaces.</p>
                    <div class="ep-photo-actions">
                        <label for="photo-input" class="ep-btn-upload-trigger">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span>Choose Profile Photo</span>
                        </label>
                        <input type="file" id="photo-input" name="photo" accept="image/png, image/jpeg, image/jpg, image/webp" style="display:none;" onchange="previewSelectedPhoto(this)">
                        <button type="button" id="photo-remove-btn" class="ep-btn-remove-photo" onclick="removeSelectedPhoto()">Remove</button>
                    </div>
                </div>
            </div>

            <!-- Demographics Information -->
            <div class="ep-section-divider">
                <span>Personal & Demographic Information</span>
            </div>

            <div class="ep-row">
                <div class="ep-field">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" placeholder="e.g. Mzee James Maarifa" required autofocus>
                </div>
                <div class="ep-field">
                    <label for="date_of_birth">Date of Birth *</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                </div>
            </div>

            <div class="ep-row">
                <div class="ep-field">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="ep-field">
                    <label for="blood_type">Blood Type</label>
                    <select id="blood_type" name="blood_type">
                        @foreach(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'] as $bt)
                            <option value="{{ $bt }}" {{ old('blood_type', 'O+') === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ep-field">
                    <label for="residence">Residence / Location</label>
                    <input type="text" id="residence" name="residence" value="{{ old('residence') }}" placeholder="e.g. Karen, Nairobi">
                </div>
            </div>

            <!-- Mobility & Assigned Caregiver -->
            <div class="ep-section-divider">
                <span>Mobility Status & Caregiver Assignment</span>
            </div>

            <div class="ep-row">
                <div class="ep-field">
                    <label for="mobility_status">Mobility Status</label>
                    <select id="mobility_status" name="mobility_status">
                        <option value="Independent" {{ old('mobility_status') === 'Independent' ? 'selected' : '' }}>Independent</option>
                        <option value="Assisted" {{ old('mobility_status', 'Assisted') === 'Assisted' ? 'selected' : '' }}>Needs Assistance / Assisted</option>
                        <option value="Limited Mobility" {{ old('mobility_status') === 'Limited Mobility' ? 'selected' : '' }}>Limited Mobility</option>
                        <option value="Bedridden" {{ old('mobility_status') === 'Bedridden' ? 'selected' : '' }}>Bedridden</option>
                    </select>
                </div>
                <div class="ep-field">
                    <label for="assigned_caregiver_id">Assigned Primary Caregiver</label>
                    <select id="assigned_caregiver_id" name="assigned_caregiver_id">
                        <option value="">-- Unassigned / Assign Later --</option>
                        @foreach($caregivers as $cg)
                            <option value="{{ $cg->id }}" {{ old('assigned_caregiver_id') == $cg->id ? 'selected' : '' }}>
                                {{ $cg->name }} ({{ $cg->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="ep-row">
                <div class="ep-field">
                    <label for="physician_name">Primary Physician</label>
                    <input type="text" id="physician_name" name="physician_name" value="{{ old('physician_name') }}" placeholder="e.g. Dr. David Kimani">
                </div>
                <div class="ep-field">
                    <label for="physician_phone">Physician Phone</label>
                    <input type="text" id="physician_phone" name="physician_phone" value="{{ old('physician_phone') }}" placeholder="+254 7XX XXX XXX">
                </div>
            </div>

            <!-- Active Prescriptions & Medications -->
            <div class="ep-section-divider">
                <span>Active Prescriptions & Medications</span>
                <button type="button" class="ep-repeat-btn" onclick="addMedicationRow()">+ Add Medication</button>
            </div>

            <div id="medications-container">
                <div class="ep-repeat-row">
                    <input type="text" name="med_name[]" placeholder="Medication Name (e.g. Lisinopril)" value="{{ old('med_name.0') }}">
                    <input type="text" name="med_dosage[]" placeholder="Dosage (10mg)" value="{{ old('med_dosage.0') }}">
                    <input type="text" name="med_schedule[]" placeholder="Schedule (Daily Morning)" value="{{ old('med_schedule.0') }}">
                    <input type="text" name="med_category[]" placeholder="Category (Hypertension)" value="{{ old('med_category.0') }}">
                    <button type="button" class="ep-del-btn" onclick="this.parentElement.remove()">×</button>
                </div>
            </div>

            <!-- Known Allergies -->
            <div class="ep-section-divider">
                <span>Known Allergies & Sensitivities</span>
                <button type="button" class="ep-repeat-btn" onclick="addAllergyRow()">+ Add Allergy</button>
            </div>

            <div id="allergies-container">
                <div class="ep-repeat-row" style="grid-template-columns: 2fr 2fr 1.5fr 40px;">
                    <input type="text" name="allergy_item[]" placeholder="Allergen (e.g. Penicillin, Peanuts)" value="{{ old('allergy_item.0') }}">
                    <input type="text" name="allergy_reaction[]" placeholder="Reaction (e.g. Anaphylaxis, Rash)" value="{{ old('allergy_reaction.0') }}">
                    <select name="allergy_severity[]">
                        <option value="High">High Severity</option>
                        <option value="Medium" selected>Medium Severity</option>
                        <option value="Low">Low Severity</option>
                    </select>
                    <button type="button" class="ep-del-btn" onclick="this.parentElement.remove()">×</button>
                </div>
            </div>

            <!-- Medical Conditions & Care Preferences -->
            <div class="ep-section-divider">
                <span>Clinical Notes & Preferences</span>
            </div>

            <div class="ep-field">
                <label for="medical_conditions">Diagnosed Medical Conditions</label>
                <textarea id="medical_conditions" name="medical_conditions" placeholder="List existing chronic conditions (e.g. Type 2 Diabetes, Mild Cognitive Impairment, Hypertension)...">{{ old('medical_conditions') }}</textarea>
            </div>

            <div class="ep-field">
                <label for="care_preferences">Care & Dietary Preferences</label>
                <textarea id="care_preferences" name="care_preferences" placeholder="Dietary restrictions, routine preferences, physiotherapy instructions...">{{ old('care_preferences') }}</textarea>
            </div>

            <!-- Emergency Contact -->
            <div class="ep-section-divider">
                <span>Primary Emergency Contact</span>
            </div>

            <div class="ep-row">
                <div class="ep-field">
                    <label for="emergency_contact_name">Contact Person Full Name *</label>
                    <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="e.g. Sarah Furaha (Daughter)" required>
                </div>
                <div class="ep-field">
                    <label for="emergency_contact_phone">Contact Phone Number *</label>
                    <input type="text" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="+254 712 345 678" required>
                </div>
            </div>

            <div class="ep-submit-row">
                <button type="submit" class="ep-btn-primary">
                    <span>Save & Create Elder Profile</span>
                </button>
                <a href="{{ route('dashboard') }}" class="ep-btn-secondary">
                    <span>Cancel</span>
                </a>
            </div>

        </form>
    </div>

</div>

<script>
function previewSelectedPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('photo-preview-img');
            const placeholder = document.getElementById('photo-preview-placeholder');
            const removeBtn = document.getElementById('photo-remove-btn');
            
            img.src = e.target.result;
            img.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
            if (removeBtn) removeBtn.style.display = 'inline-block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeSelectedPhoto() {
    const input = document.getElementById('photo-input');
    const img = document.getElementById('photo-preview-img');
    const placeholder = document.getElementById('photo-preview-placeholder');
    const removeBtn = document.getElementById('photo-remove-btn');

    input.value = '';
    img.src = '';
    img.style.display = 'none';
    if (placeholder) placeholder.style.display = 'flex';
    if (removeBtn) removeBtn.style.display = 'none';
}

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
