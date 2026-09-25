<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    /**
     * Mark an alert as read.
     */
    public function markAsRead(Alert $alert)
    {
        // Make sure this alert belongs to the
        // currently logged-in family user.
        if ($alert->family_user_id !== Auth::id()) {
            abort(403);
        }

        $alert->update([
            'is_read' => true,
        ]);

        return back()->with('success', 'Alert marked as read.');
    }
}