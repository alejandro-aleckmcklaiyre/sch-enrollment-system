<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->intended($this->redirectPath(Auth::user()));
        }
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:student,faculty,admin',
        ]);

        // Attempt login with role check
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'role' => $credentials['role']])) {
            $request->session()->regenerate();
            $redirectPath = $this->redirectPath(Auth::user());
            return redirect()->intended($redirectPath);
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or role provided.',
        ])->onlyInput('email');
    }

    /**
     * Determine redirect path based on role
     */
    protected function redirectPath($user)
    {
        return match($user->role) {
            'admin' => '/admin/dashboard',
            'faculty' => '/faculty/dashboard',
            'student' => '/student/dashboard',
            default => '/'
        };
    }

    /**
     * Show register form
     */
    public function showRegister($role = 'student')
    {
        // Always show dropdown for registration
        $roles = ['admin', 'faculty', 'student'];
        if (!in_array($role, $roles)) {
            $role = 'student';
        }
        return view('auth.register', ['role' => $role, 'roles' => $roles]);
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student,faculty,admin',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        Auth::login($user);
        return redirect()->intended($this->redirectPath($user));
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully');
    }
}
