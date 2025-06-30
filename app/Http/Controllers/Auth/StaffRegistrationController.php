<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationWelcome;

class StaffRegistrationController extends Controller
{
    /**
     * Show the staff registration form (GET /register_staff).
     */
    public function showStaffRegistrationForm()
    {
        return view('register_staff');
    }

    /**
     * Handle the staff registration form (POST /register_staff).
     */
    public function registerStaff(Request $request)
    {
        // Validate form input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create a new staff user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => 'staff',
        ]);

        // Send an email notification (see Mailable below)
        Mail::to($user->email)->send(new RegistrationWelcome($user->name));

        // Redirect to login with a success flash message
        return redirect()->route('login')->with('success', 'Registered successfully! Please login.');
    }
}
