<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ElderProfile;


class ElderProfileController extends Controller
{
    public function create()
    {
        return view('elder-profiles.create');
    }

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

        return redirect()->route('dashboard')
            ->with('success', 'Elder profile created successfully.');
    }
}