<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account | ElderCare</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
<div class="auth-viewport">

    <!-- Left Form Panel (in the warm cream/white space) -->
    <main class="auth-form-column">
        <div class="auth-card-inner">

            <!-- Brand Header -->
            <div class="auth-brand-row">
                <div class="brand-icon-box">E</div>
                <div class="brand-text">
                    <h1>ElderCare</h1>
                    <p>Care records. Better connected.</p>
                </div>
            </div>

            <!-- Heading -->
            <div class="auth-heading-wrap">
                <h2>Create an account</h2>
                <p>Select your role to access your dedicated care portal.</p>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="auth-alert-box auth-alert-error" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}">
                @csrf

                <!-- Role Selection Dropdown -->
                <div class="form-group">
                    <label for="role">Account Role *</label>
                    <select id="role" name="role" required>
                        <option value="family" {{ old('role', 'family') === 'family' ? 'selected' : '' }}>
                            Family Member (Oversee care & view alerts)
                        </option>
                        <option value="caregiver" {{ old('role') === 'caregiver' ? 'selected' : '' }}>
                            Professional Caregiver (Record visits & notes)
                        </option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                            System Administrator (Manage platform & profiles)
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="e.g. John Kamau"
                        autocomplete="name"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                    <input
                        id="phone"
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="+254 7XX XXX XXX"
                        autocomplete="tel"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="At least 8 characters"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password *</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        placeholder="Re-enter password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button type="submit" class="auth-submit-btn" style="margin-top: 10px;">
                    <span>Create Account</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>

            <div class="auth-switch-footer">
                Already have an account? <a href="{{ route('login') }}">Sign in</a>
            </div>

        </div>
    </main>

    <!-- Right Visual Panel (Elder & Caregiver image) -->
    <div class="auth-visual-column" aria-hidden="true"></div>

</div>
</body>
</html>
