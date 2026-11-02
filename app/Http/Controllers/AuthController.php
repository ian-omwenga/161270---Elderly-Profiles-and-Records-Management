<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'name' => $validated['name'],
            'email' => $validated['email'],
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
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['nullable', 'in:family,caregiver,admin'],
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $selectedRole = $request->input('role');

            // If a specific role was chosen and doesn't match the account role (unless user is an admin)
            if ($selectedRole && $user->role !== $selectedRole && !$user->isAdmin()) {
                $accountRole = $user->role;
                Auth::logout();
                return back()->withErrors([
                    'role' => 'This account is registered as a ' . ucfirst($accountRole) . ', not ' . ucfirst($selectedRole) . '.',
                ])->onlyInput('email');
            }

            $effectiveRole = ($user->isAdmin() && $selectedRole) ? $selectedRole : $user->role;

            if ($effectiveRole === 'caregiver') {
                return redirect()->route('caregiver.dashboard');
            }

            if ($effectiveRole === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'The email or password is incorrect.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
