<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\UserActivity;
use App\Mail\PasswordChangedNotification;

class AccountSettingsController extends Controller
{
    /**
     * Show the account settings form.
     */
    public function index()
    {
        return view('staff.settings.account', ['user' => Auth::user()]);
    }

    /**
     * Update account details.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            
            // Send email notification
            Mail::to($user->email)->send(new PasswordChangedNotification($user));
            
            // Log user activity
            UserActivity::create([
                'user_id' => $user->id,
                'type' => 'password_change',
                'description' => 'User changed their password.',
            ]);
        }
        
        $user->save();

        // Log general account update
        UserActivity::create([
            'user_id' => $user->id,
            'type' => 'account_update',
            'description' => 'User updated their account settings.',
        ]);

        return back()->with('success', 'Account updated successfully!');
    }
}

?>


