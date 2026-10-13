<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account | ElderCare</title>
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
            <p>Set up your family account to keep your loved one's care information organised.</p>
        </div>

        @if ($errors->any())
            <div class="error-box" role="alert">
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
                <label for="name">Full name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Enter your full name" autocomplete="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone number <span style="color:#8b97a9;font-weight:500">(optional)</span></label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="07XXXXXXXX" autocomplete="tel">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="At least 8 characters" autocomplete="new-password" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Enter the password again" autocomplete="new-password" required>
            </div>
            <button type="submit" class="primary-btn">Create family account</button>
        </form>

        <div class="auth-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
    </main>
</div>
</body>
</html>
