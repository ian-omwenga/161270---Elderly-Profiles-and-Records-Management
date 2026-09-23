<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Create Elder Profile | ElderCare</title>

    <link rel="stylesheet"
          href="{{ asset('css/auth.css') }}">

</head>

<body>

<div class="auth-page">

    <div class="auth-card" style="width: 650px;">

        <div class="auth-header">

            <h2>Create Elder Profile</h2>

            <p>
                Tell us about the person whose care will be
                managed through ElderCare.
            </p>

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
              action="{{ route('elder.store') }}">

            @csrf


            <div class="form-group">

                <label>Full Name</label>

                <input
                    type="text"
                    name="full_name"
                    value="{{ old('full_name') }}"
                    placeholder="Enter elder's full name"
                    required
                >

            </div>


            <div class="form-group">

                <label>Date of Birth</label>

                <input
                    type="date"
                    name="date_of_birth"
                    value="{{ old('date_of_birth') }}"
                    required
                >

            </div>


            <div class="form-group">

                <label>Medical Conditions</label>

                <textarea
                    name="medical_conditions"
                    placeholder="List any known medical conditions..."
                >{{ old('medical_conditions') }}</textarea>

            </div>


            <div class="form-group">

                <label>Current Medications</label>

                <textarea
                    name="medications"
                    placeholder="List current medications..."
                >{{ old('medications') }}</textarea>

            </div>


            <div class="form-group">

                <label>Allergies</label>

                <textarea
                    name="allergies"
                    placeholder="List known allergies..."
                >{{ old('allergies') }}</textarea>

            </div>


            <div class="form-group">

                <label>Mobility Status</label>

                <select name="mobility_status">

                    <option value="">
                        Select mobility status
                    </option>

                    <option value="Independent">
                        Independent
                    </option>

                    <option value="Needs Assistance">
                        Needs Assistance
                    </option>

                    <option value="Limited Mobility">
                        Limited Mobility
                    </option>

                    <option value="Bedridden">
                        Bedridden
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label>Care Preferences</label>

                <textarea
                    name="care_preferences"
                    placeholder="Describe important care preferences..."
                >{{ old('care_preferences') }}</textarea>

            </div>


            <h3 style="margin: 25px 0 15px;">
                Emergency Contact
            </h3>


            <div class="form-group">

                <label>Emergency Contact Name</label>

                <input
                    type="text"
                    name="emergency_contact_name"
                    value="{{ old('emergency_contact_name') }}"
                    required
                >

            </div>


            <div class="form-group">

                <label>Emergency Contact Phone</label>

                <input
                    type="text"
                    name="emergency_contact_phone"
                    value="{{ old('emergency_contact_phone') }}"
                    required
                >

            </div>


            <button class="primary-btn"
                    type="submit">

                Save Elder Profile

            </button>

        </form>

    </div>

</div>

</body>

</html>