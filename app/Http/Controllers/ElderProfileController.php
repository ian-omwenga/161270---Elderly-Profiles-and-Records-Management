<?php

namespace App\Http\Controllers;

use App\Models\Allergy;
use App\Models\ElderProfile;
use App\Models\Medication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ElderProfileController extends Controller
{
    /**
     * Display a dedicated elder profile interface (Figure 4.9).
     */
    public function show(ElderProfile $elder)
    {
        $user = Auth::user();

        // Access check: Family who created, assigned caregiver, or admin
        if ($user->role === 'family' && (int) $elder->created_by !== (int) $user->id) {
            abort(403, 'Unauthorized access to elder profile.');
        }

        if ($user->role === 'caregiver' && (int) $elder->assigned_caregiver_id !== (int) $user->id) {
            abort(403, 'Unauthorized access. You are not assigned to this elder profile.');
        }

        $elder->load([
            'creator',
            'assignedCaregiver',
            'medicationList' => fn($q) => $q->where('is_active', true),
            'allergyList',
            'careVisits' => fn($q) => $q->with('caregiver')->orderByDesc('visit_date')->limit(5),
        ]);

        return view('elder-profiles.show', compact('elder'));
    }

    /**
     * Serve the elder profile photo reliably across all ports and environments.
     */
    public function photo(ElderProfile $elder)
    {
        if (!empty($elder->photo_path)) {
            $path = str_replace('\\', '/', $elder->photo_path);

            // Locate file across public disk, storage path, or public storage
            $fullPath = null;
            if (Storage::disk('public')->exists($path)) {
                $fullPath = Storage::disk('public')->path($path);
            } elseif (file_exists(storage_path('app/public/' . $path))) {
                $fullPath = storage_path('app/public/' . $path);
            } elseif (file_exists(public_path('storage/' . $path))) {
                $fullPath = public_path('storage/' . $path);
            }

            if ($fullPath && file_exists($fullPath)) {
                $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                $mimeTypes = [
                    'jpg'  => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png'  => 'image/png',
                    'gif'  => 'image/gif',
                    'webp' => 'image/webp',
                    'svg'  => 'image/svg+xml',
                ];
                $contentType = $mimeTypes[$extension] ?? (function_exists('mime_content_type') ? @mime_content_type($fullPath) : null) ?: 'image/jpeg';
                $content = file_get_contents($fullPath);

                return response($content, 200, [
                    'Content-Type' => $contentType,
                    'Content-Length' => strlen($content),
                    'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        }

        abort(404);
    }

    /**
     * Show the elder profile creation form.
     */
    public function create()
    {
        $caregivers = User::where('role', 'caregiver')->get();
        return view('elder-profiles.create', compact('caregivers'));
    }

    /**
     * Store a newly created elder profile.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['nullable', 'string', 'max:20'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'residence' => ['nullable', 'string', 'max:255'],
            'physician_name' => ['nullable', 'string', 'max:255'],
            'physician_phone' => ['nullable', 'string', 'max:30'],
            'mobility_status' => ['nullable', 'string', 'max:100'],
            'independence_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'medical_conditions' => ['nullable', 'string'],
            'care_preferences' => ['nullable', 'string'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:30'],
            'assigned_caregiver_id' => ['nullable', 'exists:users,id'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            // Dynamic medications
            'med_name' => ['nullable', 'array'],
            'med_name.*' => ['nullable', 'string', 'max:100'],
            'med_dosage' => ['nullable', 'array'],
            'med_dosage.*' => ['nullable', 'string', 'max:50'],
            'med_schedule' => ['nullable', 'array'],
            'med_schedule.*' => ['nullable', 'string', 'max:100'],
            'med_category' => ['nullable', 'array'],
            'med_category.*' => ['nullable', 'string', 'max:100'],
            // Dynamic allergies
            'allergy_item' => ['nullable', 'array'],
            'allergy_item.*' => ['nullable', 'string', 'max:100'],
            'allergy_reaction' => ['nullable', 'array'],
            'allergy_reaction.*' => ['nullable', 'string', 'max:100'],
            'allergy_severity' => ['nullable', 'array'],
            'allergy_severity.*' => ['nullable', 'string', 'in:High,Medium,Low'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('elder_photos', 'public');
            $publicTarget = public_path('storage/' . $photoPath);
            $storageSource = Storage::disk('public')->path($photoPath);
            if (!file_exists($publicTarget) && file_exists($storageSource)) {
                @mkdir(dirname($publicTarget), 0755, true);
                @copy($storageSource, $publicTarget);
            }
        }

        $elderData = [
            'created_by' => Auth::id(),
            'assigned_caregiver_id' => $validated['assigned_caregiver_id'] ?? null,
            'full_name' => $validated['full_name'],
            'photo_path' => $photoPath,
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'] ?? 'Not Specified',
            'blood_type' => $validated['blood_type'] ?? 'Unknown',
            'residence' => $validated['residence'] ?? null,
            'physician_name' => $validated['physician_name'] ?? null,
            'physician_phone' => $validated['physician_phone'] ?? null,
            'mobility_status' => $validated['mobility_status'] ?? 'Independent',
            'independence_score' => $validated['independence_score'] ?? null,
            'medical_conditions' => $validated['medical_conditions'] ?? null,
            'care_preferences' => $validated['care_preferences'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'],
            'emergency_contact_phone' => $validated['emergency_contact_phone'],
        ];

        $elder = ElderProfile::create($elderData);

        // Store structured medications
        if (!empty($validated['med_name'])) {
            foreach ($validated['med_name'] as $idx => $name) {
                if (filled($name)) {
                    Medication::create([
                        'elder_profile_id' => $elder->id,
                        'name' => $name,
                        'dosage' => $validated['med_dosage'][$idx] ?? null,
                        'schedule' => $validated['med_schedule'][$idx] ?? null,
                        'category' => $validated['med_category'][$idx] ?? null,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // Store structured allergies
        if (!empty($validated['allergy_item'])) {
            foreach ($validated['allergy_item'] as $idx => $allergen) {
                if (filled($allergen)) {
                    Allergy::create([
                        'elder_profile_id' => $elder->id,
                        'allergen' => $allergen,
                        'reaction' => $validated['allergy_reaction'][$idx] ?? null,
                        'severity' => $validated['allergy_severity'][$idx] ?? 'Medium',
                    ]);
                }
            }
        }

        return redirect()
            ->route('elder.show', $elder->id)
            ->with('status', 'Elder care profile created successfully.');
    }

    /**
     * Show the elder profile edit form.
     */
    public function edit(ElderProfile $elder)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->role !== 'family' || (int) $elder->created_by !== (int) $user->id) {
            abort(403, 'Only the family member who created this profile can edit it.');
        }

        $elder->load(['medicationList', 'allergyList']);
        $caregivers = User::where('role', 'caregiver')->get();

        return view('elder-profiles.edit', compact('elder', 'caregivers'));
    }

    /**
     * Update an existing elder profile.
     */
    public function update(Request $request, ElderProfile $elder)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->role !== 'family' || (int) $elder->created_by !== (int) $user->id) {
            abort(403, 'Only the family member who created this profile can update it.');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['nullable', 'string', 'max:20'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'residence' => ['nullable', 'string', 'max:255'],
            'physician_name' => ['nullable', 'string', 'max:255'],
            'physician_phone' => ['nullable', 'string', 'max:30'],
            'mobility_status' => ['nullable', 'string', 'max:100'],
            'independence_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'medical_conditions' => ['nullable', 'string'],
            'care_preferences' => ['nullable', 'string'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:30'],
            'assigned_caregiver_id' => ['nullable', 'exists:users,id'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            // Dynamic medications
            'med_name' => ['nullable', 'array'],
            'med_name.*' => ['nullable', 'string', 'max:100'],
            'med_dosage' => ['nullable', 'array'],
            'med_dosage.*' => ['nullable', 'string', 'max:50'],
            'med_schedule' => ['nullable', 'array'],
            'med_schedule.*' => ['nullable', 'string', 'max:100'],
            'med_category' => ['nullable', 'array'],
            'med_category.*' => ['nullable', 'string', 'max:100'],
            // Dynamic allergies
            'allergy_item' => ['nullable', 'array'],
            'allergy_item.*' => ['nullable', 'string', 'max:100'],
            'allergy_reaction' => ['nullable', 'array'],
            'allergy_reaction.*' => ['nullable', 'string', 'max:100'],
            'allergy_severity' => ['nullable', 'array'],
            'allergy_severity.*' => ['nullable', 'string', 'in:High,Medium,Low'],
        ]);

        if ($request->hasFile('photo')) {
            if ($elder->photo_path && Storage::disk('public')->exists($elder->photo_path)) {
                Storage::disk('public')->delete($elder->photo_path);
            }
            if ($elder->photo_path && file_exists(public_path('storage/' . $elder->photo_path))) {
                @unlink(public_path('storage/' . $elder->photo_path));
            }

            $validated['photo_path'] = $request->file('photo')->store('elder_photos', 'public');
            $publicTarget = public_path('storage/' . $validated['photo_path']);
            $storageSource = Storage::disk('public')->path($validated['photo_path']);
            if (!file_exists($publicTarget) && file_exists($storageSource)) {
                @mkdir(dirname($publicTarget), 0755, true);
                @copy($storageSource, $publicTarget);
            }
        }

        $elder->update([
            'assigned_caregiver_id' => $validated['assigned_caregiver_id'] ?? $elder->assigned_caregiver_id,
            'full_name' => $validated['full_name'],
            'photo_path' => $validated['photo_path'] ?? $elder->photo_path,
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'] ?? $elder->gender,
            'blood_type' => $validated['blood_type'] ?? $elder->blood_type,
            'residence' => $validated['residence'] ?? $elder->residence,
            'physician_name' => $validated['physician_name'] ?? $elder->physician_name,
            'physician_phone' => $validated['physician_phone'] ?? $elder->physician_phone,
            'mobility_status' => $validated['mobility_status'] ?? $elder->mobility_status,
            'independence_score' => $validated['independence_score'] ?? $elder->independence_score,
            'medical_conditions' => $validated['medical_conditions'] ?? $elder->medical_conditions,
            'care_preferences' => $validated['care_preferences'] ?? $elder->care_preferences,
            'emergency_contact_name' => $validated['emergency_contact_name'],
            'emergency_contact_phone' => $validated['emergency_contact_phone'],
        ]);

        // Sync medications: clear existing and re-insert if submitted
        if ($request->has('med_name')) {
            $elder->medicationList()->delete();
            foreach ($request->input('med_name') as $idx => $name) {
                if (filled($name)) {
                    Medication::create([
                        'elder_profile_id' => $elder->id,
                        'name' => $name,
                        'dosage' => $request->input("med_dosage.$idx"),
                        'schedule' => $request->input("med_schedule.$idx"),
                        'category' => $request->input("med_category.$idx"),
                        'is_active' => true,
                    ]);
                }
            }
        }

        // Sync allergies: clear existing and re-insert if submitted
        if ($request->has('allergy_item')) {
            $elder->allergyList()->delete();
            foreach ($request->input('allergy_item') as $idx => $allergen) {
                if (filled($allergen)) {
                    Allergy::create([
                        'elder_profile_id' => $elder->id,
                        'allergen' => $allergen,
                        'reaction' => $request->input("allergy_reaction.$idx"),
                        'severity' => $request->input("allergy_severity.$idx", 'Medium'),
                    ]);
                }
            }
        }

        return redirect()
            ->route('elder.show', $elder->id)
            ->with('status', 'Elder care record updated successfully.');
    }
}
