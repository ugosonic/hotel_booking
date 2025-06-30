<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginNotification;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Handle the login submission.
     */
    public function submitLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email','password');
        
        // Attempt login:
        if (Auth::attempt($credentials)) {
            // The user is now logged in, so Auth::id() is not null
            $user = Auth::user();

            // 1) Send the login notification email
            Mail::to($user->email)->send(new LoginNotification($user));

            // 2) Log “user logged in successfully”
            UserActivity::create([
                'user_id'     => $user->id,
                'type'        => 'login',
                'description' => 'User logged in successfully.',
            ]);

            // 3) Redirect staff vs client
            if ($user->isStaff()) {
                return redirect()->route('staff.dashboard')
                                 ->with('success', 'You are logged in as staff!');
            } else {
                return redirect()->route('client.dashboard')
                                 ->with('success', 'You are logged in as a client!');
            }
        }

        // If login fails, show error
        return back()->withErrors(['email'=>'Invalid credentials.']);
    }

    /**
     * Handle logging out (if you want to do it here rather than a closure).
     */
    public function logout(Request $request)
    {
        // Grab the current user ID *before* logging out
        $userId = Auth::id();

        // Log the user out
        Auth::logout();

        // Only if the user was actually logged in, record the logout
        if ($userId) {
            UserActivity::create([
                'user_id'     => $userId,
                'type'        => 'logout',
                'description' => 'User logged out successfully.',
            ]);
        }

        return redirect('/login')->with('info','You have been logged out.');
    }
}
