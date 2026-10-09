<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Record visit | ElderCare</title><link rel="stylesheet" href="{{ asset('css/visits.css') }}"></head><body><div class="shell">
<header class="topbar"><a class="brand" href="{{ route('dashboard') }}"><span class="mark">E</span>ElderCare</a>
<nav><a href="{{ route('dashboard') }}">Dashboard</a><a href="{{ route('visits.index',$elder) }}">Care visits</a><span>New entry</span></nav>
<div class="account"><span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}</span>{{ auth()->user()->name ?? 'Account' }}</div></header>
<main class="main"><div class="heading"><div><p class="eyebrow">CAREGIVER WORKSPACE</p><h1>Record a care visit</h1><p class="muted">Save observations to {{ $elder->full_name }}'s care record.</p></div><a class="button secondary" href="{{ route('visits.index',$elder) }}">Back to visits</a></div>
@if($errors->any())<div class="notice error"><strong>Please check these fields:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="entry-grid"><form method="POST" action="{{ route('visits.store',$elder) }}" class="form-stack">@csrf
<section class="panel"><div class="panel-heading"><span class="step">01</span><div><h2>Visit details</h2><p class="muted">Date and care activities.</p></div></div>
<div class="field-grid"><div class="field"><label for="visit_date">Date of visit *</label><input id="visit_date" type="date" name="visit_date" value="{{ old('visit_date',now()->format('Y-m-d')) }}" required></div>
<div class="field"><label>Caregiver</label><input value="{{ auth()->user()->name }}" readonly><small>Your signed-in account is recorded.</small></div></div>
<div class="field"><label for="tasks_completed">Care and support activities</label><textarea id="tasks_completed" name="tasks_completed" rows="3" maxlength="5000" placeholder="What care tasks were completed?">{{ old('tasks_completed') }}</textarea></div></section>
<section class="panel"><div class="panel-heading"><span class="step">02</span><div><h2>Health observations</h2><p class="muted">Select a score from 1 to 5.</p></div></div>
@foreach(['mood_score'=>'Mood','appetite_score'=>'Appetite','pain_level'=>'Pain level'] as $field=>$label)
<div class="score-field"><div><label for="{{ $field }}">{{ $label }} *</label><small>{{ ['mood_score'=>'How did the elder seem emotionally?','appetite_score'=>'How was their appetite?','pain_level'=>'How much pain or discomfort?'][$field] }}</small></div>
<select id="{{ $field }}" name="{{ $field }}" required><option value="">Choose score</option>@foreach([1,2,3,4,5] as $score)<option value="{{ $score }}" @selected((string)old($field)===(string)$score)>{{ $score }} / 5</option>@endforeach</select></div>
@endforeach
<label class="check-card"><input type="checkbox" name="medication_taken" value="1" @checked(old('medication_taken'))><span><b>Medication was taken during this visit</b><small>Tick only if confirmed.</small></span></label></section>
<section class="panel"><div class="panel-heading"><span class="step">03</span><div><h2>Visit notes</h2><p class="muted">Keep observations clear and factual.</p></div></div>
<div class="field"><label for="visit_note">Clinical and wellbeing notes *</label><textarea id="visit_note" name="visit_note" rows="6" maxlength="10000" required placeholder="Record the elder's condition, mood, concerns, changes, and follow-up needed...">{{ old('visit_note') }}</textarea><small>Record what you observed or what the elder reported.</small></div></section>
<div class="actions"><a class="button secondary" href="{{ route('visits.index',$elder) }}">Cancel</a><button class="button primary" type="submit">Save visit record →</button></div></form>
<aside class="side-stack"><section class="panel person-panel"><p class="eyebrow">ELDER PROFILE</p><div class="person-avatar">{{ strtoupper(substr($elder->full_name,0,1)) }}</div><h2>{{ $elder->full_name }}</h2>
@if($elder->date_of_birth)<p class="muted">Born {{ \Illuminate\Support\Carbon::parse($elder->date_of_birth)->format('d M Y') }}</p>@endif
<div class="rule"></div><div class="detail"><small>Mobility</small><b>{{ $elder->mobility_status ?: 'Not recorded' }}</b></div><div class="detail"><small>Allergies</small><b>{{ $elder->allergies ?: 'None recorded' }}</b></div><div class="detail"><small>Emergency contact</small><b>{{ $elder->emergency_contact_name ?: 'Not recorded' }}</b>@if($elder->emergency_contact_phone)<small>{{ $elder->emergency_contact_phone }}</small>@endif</div></section>
<section class="panel guidance"><span class="info">i</span><h3>Before you save</h3><ul><li>Complete all required fields.</li><li>Keep notes specific and factual.</li><li>Record medication only when confirmed.</li><li>Your entry will appear in visit history.</li></ul></section></aside></div></main></div></body></html>