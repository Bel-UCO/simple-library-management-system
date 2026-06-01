<?php

namespace App\Http\Controllers;

use App\Models\BorrowedLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index');
    }

    public function authenticate(Request $request)
    {
        // Validate the submitted email and password
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the user
        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors([
                    'email' => 'Authentication failed, please check your email and/or password.',
                ])
                ->onlyInput('email');
        }

        // Regenerate session after successful login
        $request->session()->regenerate();

        // Get the authenticated user
        $user = Auth::user();

        // Check if the authenticated user is an admin
        if ($user->is_admin) {
            return redirect()->route('home');
        }

        // Check if the authenticated user's account is active
        if ($user->status !== 'active') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors([
                    'email' => 'Your account is inactive. Please contact support.',
                ])
                ->onlyInput('email');
        }

        // Check if the authenticated user has overdue books
        $hasOverdueBooks = BorrowedLog::where('user_id', $user->id)
            ->whereNull('returned_date')
            ->whereDate('due_date', '<', today())
            ->exists();

        // Suspend the user if they have overdue books
        if ($hasOverdueBooks) {
            $user->update([
                'status' => 'suspended',
            ]);

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login.index')
                ->withErrors([
                    'email' => 'Your account is temporarily suspended due to overdue books. Please return the overdue books to reactivate your account.',
                ])
                ->onlyInput('email');
        }

        // Redirect active member to home page
        return redirect()->route('home');
    }

    public function setting()
    {
        // Show setting page for authenticated user
        return view('login.setting');
    }

    public function updatePassword(Request $request)
    {
        // Validate the submitted password data
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Check if the current password is correct
        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors([
                    'current_password' => 'Current password is incorrect.',
                ]);
        }

        // Update user password with hashed password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Redirect back to setting page with success message
        return redirect()
            ->route('login.setting')
            ->with('success', 'Password changed successfully.');
    }

    public function logout(Request $request)
    {
        // Logout the authenticated user
        Auth::logout();

        // Invalidate current session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Redirect back to login page
        return redirect()->route('login.index');
    }
}