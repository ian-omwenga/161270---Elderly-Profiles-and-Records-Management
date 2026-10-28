<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ElderCare') | ElderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app-layout.css') }}">
    @stack('styles')
</head>
<body class="ec-shell-body">
    @php
        $currentUser = auth()->user();
        $userRole = $currentUser->role ?? 'family';
        $brandSpace = match($userRole) {
            'caregiver' => 'CAREGIVER PORTAL',
            'admin' => 'ADMIN CONSOLE',
            default => 'FAMILY CARE SPACE',
        };
        $firstElder = \App\Models\ElderProfile::where('created_by', $currentUser->id ?? 0)->first() 
            ?? \App\Models\ElderProfile::first();
    @endphp

    <div class="ec-shell">
        <aside class="ec-sidebar" id="ecSidebar">
            <a class="ec-brand" href="{{ $userRole === 'caregiver' ? route('caregiver.dashboard') : ($userRole === 'admin' ? route('admin.dashboard') : route('dashboard')) }}" aria-label="ElderCare home">
                <span class="ec-brand-mark"><span></span><span></span><span></span></span>
                <span class="ec-brand-copy"><strong>ElderCare</strong><small>{{ $brandSpace }}</small></span>
            </a>

            <div class="ec-nav-caption">WORKSPACE</div>
            <nav class="ec-nav" aria-label="Main navigation">
                @if($userRole === 'family')
                    <a href="{{ route('dashboard') }}" class="ec-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="2"/><rect x="13.5" y="3.5" width="7" height="7" rx="2"/><rect x="3.5" y="13.5" width="7" height="7" rx="2"/><rect x="13.5" y="13.5" width="7" height="7" rx="2"/></svg>
                        <span>Family Dashboard</span>
                    </a>

                    @if($firstElder)
                        <a href="{{ route('elder.show', $firstElder->id) }}" class="ec-nav-link {{ request()->routeIs('elder.show') || request()->routeIs('elder.profile.edit') ? 'is-active' : '' }}">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a6.5 6.5 0 0 1 13 0"/></svg>
                            <span>Elder Profile</span>
                        </a>
                        <a href="{{ route('visits.index', $firstElder->id) }}" class="ec-nav-link {{ request()->routeIs('visits.index') ? 'is-active' : '' }}">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="3"/><path d="M8 3v4M16 3v4M4 10h16M8 14h3"/></svg>
                            <span>Visit History</span>
                        </a>
                    @else
                        <a href="{{ route('elder.create') }}" class="ec-nav-link {{ request()->routeIs('elder.create') ? 'is-active' : '' }}">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a6.5 6.5 0 0 1 13 0"/></svg>
                            <span>Add Elder Profile</span>
                        </a>
                    @endif

                    <a href="{{ route('sentiment.index') }}" class="ec-nav-link {{ request()->routeIs('sentiment.*') ? 'is-active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5M4 19h16"/><path d="m7 15 4-4 3 2 5-7"/><circle cx="19" cy="6" r="1.5"/></svg>
                        <span>Sentiment Analysis</span>
                        <span class="ec-nav-pill">AI</span>
                    </a>
                @elseif($userRole === 'caregiver')
                    <a href="{{ route('caregiver.dashboard') }}" class="ec-nav-link {{ request()->routeIs('caregiver.dashboard') ? 'is-active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="2"/><rect x="13.5" y="3.5" width="7" height="7" rx="2"/><rect x="3.5" y="13.5" width="7" height="7" rx="2"/><rect x="13.5" y="13.5" width="7" height="7" rx="2"/></svg>
                        <span>Caregiver Portal</span>
                    </a>

                    @if($firstElder)
                        <a href="{{ route('visits.create', $firstElder->id) }}" class="ec-nav-link {{ request()->routeIs('visits.create') ? 'is-active' : '' }}">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                            <span>Record Care Visit</span>
                        </a>
                    @endif

                    <a href="{{ route('sentiment.index') }}" class="ec-nav-link {{ request()->routeIs('sentiment.*') ? 'is-active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5M4 19h16"/><path d="m7 15 4-4 3 2 5-7"/><circle cx="19" cy="6" r="1.5"/></svg>
                        <span>Sentiment Insights</span>
                        <span class="ec-nav-pill">AI</span>
                    </a>
                @elseif($userRole === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="ec-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="2"/><rect x="13.5" y="3.5" width="7" height="7" rx="2"/><rect x="3.5" y="13.5" width="7" height="7" rx="2"/><rect x="13.5" y="13.5" width="7" height="7" rx="2"/></svg>
                        <span>Admin Console</span>
                    </a>
                    <a href="{{ route('dashboard') }}" class="ec-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a6.5 6.5 0 0 1 13 0"/></svg>
                        <span>Family View</span>
                    </a>
                    <a href="{{ route('caregiver.dashboard') }}" class="ec-nav-link {{ request()->routeIs('caregiver.dashboard') ? 'is-active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="3"/><path d="M8 3v4M16 3v4M4 10h16M8 14h3"/></svg>
                        <span>Caregiver View</span>
                    </a>
                    <a href="{{ route('sentiment.index') }}" class="ec-nav-link {{ request()->routeIs('sentiment.*') ? 'is-active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5M4 19h16"/><path d="m7 15 4-4 3 2 5-7"/><circle cx="19" cy="6" r="1.5"/></svg>
                        <span>Sentiment Analytics</span>
                        <span class="ec-nav-pill">AI</span>
                    </a>
                @endif
            </nav>

            <div class="ec-sidebar-bottom">
                <div class="ec-help-card">
                    <span class="ec-help-orb"></span>
                    <strong>VADER AI Monitoring</strong>
                    <p>Clinical notes are evaluated using lexicon-based sentiment analysis for early warning signals.</p>
                    <a href="{{ route('sentiment.index') }}">Review insights <span aria-hidden="true">↗</span></a>
                </div>
                <div class="ec-account">
                    <div class="ec-account-avatar">{{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}</div>
                    <div class="ec-account-info">
                        <strong>{{ $currentUser->name ?? 'User' }}</strong>
                        <small>{{ ucfirst($userRole) }} Account</small>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="ec-logout" type="submit" title="Log out" aria-label="Log out">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4H5.5A1.5 1.5 0 0 0 4 5.5v13A1.5 1.5 0 0 0 5.5 20H10"/><path d="M14 16l4-4-4-4M18 12H9"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="ec-main-column">
            <header class="ec-topbar">
                <button class="ec-menu-toggle" type="button" id="ecMenuToggle" aria-label="Toggle navigation" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
                <div class="ec-topbar-context">
                    <span class="ec-topbar-kicker">ELDERCARE / {{ $brandSpace }}</span>
                    <strong>@yield('page-heading', 'Home-Based Health Monitoring')</strong>
                </div>
                <div class="ec-topbar-right">
                    <span class="ec-status-dot"></span>
                    <span class="ec-status-text">{{ ucfirst($userRole) }} Active</span>
                    <div class="ec-topbar-avatar">{{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}</div>
                </div>
            </header>

            @if(session('status'))
                <div class="ec-flash ec-flash-success" role="status">{{ session('status') }}</div>
            @endif
            @if(session('info'))
                <div class="ec-flash" role="status">{{ session('info') }}</div>
            @endif
            @if(session('error'))
                <div class="ec-flash ec-flash-error" style="background:#fee2e2; color:#991b1b; border-color:#fca5a5;" role="alert">{{ session('error') }}</div>
            @endif

            <main class="ec-page-content" id="ecPageContent">
                @yield('content')
            </main>
            <footer class="ec-footer">
                <span>ElderCare · Strathmore University Capstone</span>
                <span>Lexicon-Based Sentiment Analysis & Home-Based Health Monitoring</span>
            </footer>
        </div>
    </div>
    <script src="{{ asset('js/app-layout.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
