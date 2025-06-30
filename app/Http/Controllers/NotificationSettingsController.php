<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationSetting;

class NotificationSettingsController extends Controller
{
    public function index()
    {
        // Current user
        $user = Auth::user();

        // If the user doesn't have a related NotificationSetting, create default
        if (!$user->notificationSetting) {
            $user->notificationSetting()->create([]);
            $user->load('notificationSetting'); // reload user with new settings
        }

        // Pass notification settings to the view
        $settings = $user->notificationSetting;
        return view('notifications.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Current user
        $user = Auth::user();

        // Ensure user settings record exists
        if (!$user->notificationSetting) {
            $user->notificationSetting()->create([]);
        }

        // Update toggles (boolean checkboxes may come as 'on' or null)
        $user->notificationSetting->update([
            'login_notification'               => $request->boolean('login_notification'),
            'password_changed_notification'    => $request->boolean('password_changed_notification'),
            'payment_error_notification'       => $request->boolean('payment_error_notification'),
            'payment_success_notification'     => $request->boolean('payment_success_notification'),
            'pending_topup_notification'       => $request->boolean('pending_topup_notification'),
            'registration_welcome_notification'=> $request->boolean('registration_welcome_notification'),
        ]);

        return redirect()->back()->with('success', 'Notification settings updated.');
    }
}
