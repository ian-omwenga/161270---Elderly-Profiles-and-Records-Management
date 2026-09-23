<?php

namespace App\Http\Controllers;

use App\Models\ElderProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FamilyDashboardController extends Controller
{
    public function index()
    {
        //$user = auth()->user();
        $user = Auth::user();

        $elder = ElderProfile::where('created_by', $user->id)
            ->with([
                'careVisits' => function ($query) {
                    $query->latest('visit_date');
                }
            ])
            ->first();

        if (!$elder) {
            return redirect()->route('elder.create');
        }

        $recentVisits = $elder->careVisits()
            ->latest('visit_date')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'elder',
            'recentVisits'
        ));
    }
}