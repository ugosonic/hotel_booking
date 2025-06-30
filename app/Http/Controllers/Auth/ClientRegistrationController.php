<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller; // Import the base Controller
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;       // <-- Import Mail facade
use App\Mail\RegistrationWelcome;          // <-- Import your Mailable
use App\Models\User;   

class ClientRegistrationController extends Controller
{
    // Display the client registration form
    public function showClientRegistrationForm()
    {
        return view('register_client'); // Return the client registration view
    }

    // Handle client registration
    protected function registerClient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'client', // Assign role as 'client'
        ]);
    
        
        return redirect()->route('login')->with('success', 'Registered successfully! Please login.');
    }
    
}
