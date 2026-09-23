<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | ElderCare</title>

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
            <h2>Welcome back</h2>
            <p>Sign in to access your ElderCare account.</p>
        </div>

        @if ($errors->any())
            <div class="error-box">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <div class="form-group">
                <label>Email address</label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Enter your email"
                    required
                >
            </div>

            <div class="form-group">
                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >
            </div>

            <div class="remember-row">

                <label>
                    <input type="checkbox" name="remember">
                    Remember me
                </label>

            </div>

            <button type="submit" class="primary-btn">
                Sign In
            </button>

        </form>

        <div class="auth-footer">
            Don't have an account?
            <a href="{{ route('register') }}">Create account</a>
        </div>

    </div>

</div>

</body>
</html>