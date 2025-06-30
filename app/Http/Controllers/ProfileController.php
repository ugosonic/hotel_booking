<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserActivity;

class ProfileController extends Controller
{
    public function showProfile($userId)
    {
        // If staff wants to view any user’s profile, or client can only see their own
        // For example, a staff route that loads a user by ID:
        $user = User::findOrFail($userId);

        // Retrieve the user’s activity logs, newest first
        $activities = UserActivity::where('user_id',$user->id)
            ->orderBy('id','desc')
            ->paginate(10);

        return view('show_profile', compact('user','activities'));
    }
}
