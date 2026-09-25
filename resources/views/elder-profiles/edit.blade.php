<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Elder Profile | ElderCare</title>

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>

<div class="auth-page">

    <div class="auth-brand">
        <div class="brand-mark">E</div>
        <h1>ElderCare</h1>
        <p>Care records. Better connected.</p>
    </div>

    <div class="auth-card">

        <div class="auth-header">
            <h2>Edit Elder Profile</h2>
            <p>Update the elder's care information.</p>
        </div>

        @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('elder.profile.update', $elder->id) }}">

            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Full name</label>

                <input
                    type="text"
                    name="full_name"
                    value="{{ old('full_name', $elder->full_name) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label>Date of birth</label>

                <input
                    type="date"
                    name="date_of_birth"
                    value="{{ old('date_of_birth', $elder->date_of_birth) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label>Medical conditions</label>

                <textarea name="medical_conditions">{{ old('medical_conditions', $elder->medical_conditions) }}</textarea>
            </div>

            <div class="form-group">
                <label>Medications</label>

                <textarea name="medications">{{ old('medications', $elder->medications) }}</textarea>
            </div>

            <div class="form-group">
                <label>Allergies</label>

                <textarea name="allergies">{{ old('allergies', $elder->allergies) }}</textarea>
            </div>

            <div class="form-group">
                <label>Mobility status</label>

                <input
                    type="text"
                    name="mobility_status"
                    value="{{ old('mobility_status', $elder->mobility_status) }}"
                >
            </div>

            <div class="form-group">
                <label>Care preferences</label>

                <textarea name="care_preferences">{{ old('care_preferences', $elder->care_preferences) }}</textarea>
            </div>

            <div class="form-group">
                <label>Emergency contact name</label>

                <input
                    type="text"
                    name="emergency_contact_name"
                    value="{{ old('emergency_contact_name', $elder->emergency_contact_name) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label>Emergency contact phone</label>

                <input
                    type="text"
                    name="emergency_contact_phone"
                    value="{{ old('emergency_contact_phone', $elder->emergency_contact_phone) }}"
                    required
                >
            </div>

            <button type="submit" class="primary-btn">
                Update Profile
            </button>

        </form>

        <div class="auth-footer">
            <a href="{{ route('dashboard') }}">
                Back to Dashboard
            </a>
        </div>

    </div>

</div>

</body>
</html>