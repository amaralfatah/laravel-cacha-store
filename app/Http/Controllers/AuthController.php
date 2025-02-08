<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Get remember me value from the request
        $remember = $request->has('remember') ? true : false;

        if (Auth::attempt($credentials, $remember)) {  // Added remember parameter here
            $request->session()->regenerate();

            // Redirect based on user role with success message
            $user = Auth::user();
            switch ($user->role) {
                case 'admin':
                    return redirect()->intended('/dashboard')->with('success', 'Welcome back, Admin! You have successfully logged in.');
                case 'cashier':
                    return redirect()->intended('/dashboard')->with('success', 'Welcome back, Cashier! You have successfully logged in.');
                default:
                    return redirect()->intended('/dashboard')->with('success', 'Welcome back! You have successfully logged in.');
            }
        }

        return back()
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])
            ->with('error', 'Login failed. Please check your credentials and try again.')
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('info', 'You have been successfully logged out. Thank you for using our application!');
    }
}
