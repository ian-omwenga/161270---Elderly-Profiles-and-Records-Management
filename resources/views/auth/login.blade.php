<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In | ElderCare</title>
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
                <h2>Welcome back</h2>
                <p>Sign in to access your dedicated care records and dashboard.</p>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="auth-alert-box auth-alert-error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="auth-alert-box auth-alert-info" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('info'))
                <div class="auth-alert-box auth-alert-info" role="status">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Sign In Form (No Role Dropdown needed) -->
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        autocomplete="email"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="auth-options-row">
                    <label class="remember-label" for="remember">
                        <input id="remember" type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="auth-submit-btn">
                    <span>Sign In to ElderCare</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>

            <div class="auth-switch-footer">
                New to ElderCare? <a href="{{ route('register') }}">Create an account</a>
            </div>

        </div>
    </main>

    <!-- Right Visual Panel (Elder & Caregiver image) -->
    <div class="auth-visual-column" aria-hidden="true"></div>

</div>
</body>
</html>
