<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:family,caregiver,admin'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        Auth::login($user);

        if ($user->role === 'caregiver') {
            return redirect()->route('caregiver.dashboard')->with('status', 'Welcome to the Caregiver Portal.');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('status', 'Welcome to the Administrator Console.');
        }

        return redirect()->route('elder.create')->with('status', 'Welcome! Please set up your first elder profile.');
    }

    public function showLogin()
    {
        // If already logged in, redirect straight to their respective interface
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            if ($user->role === 'caregiver') {
                return redirect()->route('caregiver.dashboard');
            }
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $email = strtolower(trim($request->input('email')));
        $password = $request->input('password');

        // Look up user case-insensitively for PostgreSQL compatibility
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($user->role === 'caregiver') {
                return redirect()->route('caregiver.dashboard')->with('status', 'Welcome back, ' . $user->name);
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('status', 'Welcome to the Administrator Console.');
            }

            return redirect()->route('dashboard')->with('status', 'Welcome back, ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been signed out successfully.');
    }
}
