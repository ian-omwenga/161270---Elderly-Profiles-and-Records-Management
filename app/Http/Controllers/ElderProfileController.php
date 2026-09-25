<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ElderProfile;

class ElderProfileController extends Controller
{
    /**
     * Show the elder profile creation form.
     */
    public function create()
    {
        return view('elder-profiles.create');
    }

    /**
     * Store a newly created elder profile.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'medical_conditions' => ['nullable', 'string'],
            'medications' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'mobility_status' => ['nullable', 'string', 'max:100'],
            'care_preferences' => ['nullable', 'string'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
        ]);

        $validated['created_by'] = Auth::id();

        ElderProfile::create($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Elder profile created successfully.');
    }

    /**
     * Show the elder profile edit form.
     */
    public function edit(ElderProfile $elder)
    {
        // Make sure the logged-in family user owns this profile.
        if ($elder->created_by !== Auth::id()) {
            abort(403);
        }

        return view('elder-profiles.edit', compact('elder'));
    }

    /**
     * Update an existing elder profile.
     */
    public function update(Request $request, ElderProfile $elder)
    {
        // Make sure the logged-in family user owns this profile.
        if ($elder->created_by !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'medical_conditions' => ['nullable', 'string'],
            'medications' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'mobility_status' => ['nullable', 'string', 'max:100'],
            'care_preferences' => ['nullable', 'string'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
        ]);

        $elder->update($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Elder profile updated successfully.');
    }
}