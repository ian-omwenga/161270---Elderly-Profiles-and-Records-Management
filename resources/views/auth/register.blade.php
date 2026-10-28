<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | ElderCare</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
<div class="auth-page">
    <div class="auth-brand">
        <div class="brand-mark">E</div>
        <h1>ElderCare</h1>
        <p>Care records. Better connected.</p>
    </div>

    <main class="auth-card">
        <div class="auth-header">
            <h2>Create your account</h2>
            <p>Join ElderCare to manage elder records or log home-care visits.</p>
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

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div class="form-group">
                <label>Account Role</label>
                <select name="role" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; font-size:13px;">
                    <option value="family" {{ old('role') === 'family' ? 'selected' : '' }}>
                        Family Member (Managing elder profiles & alerts)
                    </option>
                    <option value="caregiver" {{ old('role') === 'caregiver' ? 'selected' : '' }}>
                        Professional Caregiver (Logging visits & observations)
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Full name</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Enter your full name"
                    required
                >
            </div>

            <div class="form-group">
                <label>Email address</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="you@example.com"
                    required
                >
            </div>

            <div class="form-group">
                <label>Phone number</label>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    placeholder="+254 7XX XXX XXX"
                >
            </div>

            <div class="form-group">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Minimum 8 characters"
                    required
                >
            </div>

            <div class="form-group">
                <label>Confirm password</label>
                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Repeat your password"
                    required
                >
            </div>

            <button type="submit" class="primary-btn">
                Create Account
            </button>
        </form>

        <div class="auth-footer">
            Already have an account?
            <a href="{{ route('login') }}">
                Sign in
            </a>
        </div>

    </div>

</div>
</body>
</html>
