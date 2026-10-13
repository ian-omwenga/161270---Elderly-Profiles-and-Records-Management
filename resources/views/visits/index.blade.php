<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Care visits | ElderCare</title><link rel="stylesheet" href="{{ asset('css/visits.css') }}">
</head><body><div class="shell">
<header class="topbar"><a class="brand" href="{{ route('dashboard') }}"><span class="mark">E</span>ElderCare</a>
<nav><a href="{{ route('dashboard') }}">Dashboard</a><span>Care visits</span></nav>
<div class="account"><span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}</span>{{ auth()->user()->name ?? 'Account' }}
<form method="POST" action="{{ route('logout') }}">@csrf<button class="link-button">Log out</button></form></div></header>
<main class="main">
<div class="heading"><div><p class="eyebrow">CARE RECORDS</p><h1>Visit history</h1><p class="muted">Care records for {{ $elder->full_name }}.</p></div>
<a class="button primary" href="{{ route('visits.create',$elder) }}">Record a visit <span>→</span></a></div>
@if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
<section class="profile"><div class="person-avatar">{{ strtoupper(substr($elder->full_name,0,1)) }}</div><div><p class="eyebrow">ELDER PROFILE</p><h2>{{ $elder->full_name }}</h2><p class="muted">{{ $visits->count() }} {{ \Illuminate\Support\Str::plural('visit',$visits->count()) }} recorded</p></div>
<div class="latest"><small>LATEST VISIT</small><strong>{{ $visits->first()?->visit_date?->format('d M Y') ?? 'No visits yet' }}</strong></div></section>
<div class="section-title"><div><h2>All visits</h2><p class="muted">Saved visit notes and observations.</p></div><span class="pill">{{ $visits->count() }} total</span></div>
@forelse($visits as $visit)
<article class="visit"><div class="date-box"><span>{{ $visit->visit_date?->format('M') }}</span><strong>{{ $visit->visit_date?->format('d') }}</strong><small>{{ $visit->visit_date?->format('Y') }}</small></div>
<div class="visit-body"><div class="visit-head"><div><h3>Care visit</h3><p class="muted">Recorded by {{ $visit->caregiver?->name ?? 'Caregiver not available' }}</p></div>
<span class="pill {{ $visit->medication_taken ? 'green' : '' }}">{{ $visit->medication_taken ? 'Medication taken' : 'Medication not confirmed' }}</span></div>
@if($visit->tasks_completed)<p>{{ $visit->tasks_completed }}</p>@endif
@if($visit->visit_note)<p class="muted note">{{ $visit->visit_note }}</p>@endif
<div class="scores"><span>Mood <b>{{ $visit->mood_score }}/5</b></span><span>Appetite <b>{{ $visit->appetite_score }}/5</b></span><span>Pain <b>{{ $visit->pain_level }}/5</b></span></div></div></article>
@empty
<section class="empty"><div class="empty-mark">+</div><h3>No visits recorded yet</h3><p class="muted">Submitted caregiver visits will appear here.</p><a class="button primary" href="{{ route('visits.create',$elder) }}">Record the first visit</a></section>
@endforelse
</main></div></body></html>