<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in | ElderCare</title>
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
            <h2>Welcome back</h2>
            <p>Sign in to see your loved one's care updates and records.</p>
        </div>

        @if ($errors->any())
            <div class="error-box" role="alert">{{ $errors->first() }}</div>
        @endif

        @if (session('status'))
            <div class="error-box" role="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
            </div>
            <div class="remember-row">
                <label for="remember"><input id="remember" type="checkbox" name="remember"> Remember me</label>
            </div>
            <button type="submit" class="primary-btn">Sign in to ElderCare</button>
        </form>

        <div class="auth-footer">New to ElderCare? <a href="{{ route('register') }}">Create an account</a></div>
    </main>
</div>
</body>
</html>
