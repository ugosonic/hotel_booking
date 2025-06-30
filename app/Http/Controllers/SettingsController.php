<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class SettingsController extends Controller
{
    /**
     * General settings page (GET /settings).
     */
    public function index()
    {
        return view('dashboard.settings');
    }

    /**
     * Site settings page (GET /site-settings) - staff only
     */
    public function siteSettings()
    {
        // Load all settings from the table (if multiple rows, adapt accordingly)
        $settings = Setting::all();
        return view('dashboard.site_settings', compact('settings'));
    }

    /**
     * Account settings for the user (GET /account/settings).
     */
    
    /**
     * Staff settings page (GET /staff/settings).
     */
    public function staffSettings()
    {
        // Only allow staff
        if (!auth()->user()->isStaff()) {
            abort(403, 'Unauthorized');
        }

        // Attempt to get the first row or create it
        $settings = Setting::first();
        if (!$settings) {
            $settings = Setting::create([
                'refund_percentage' => 0,
            ]);
        }

        // Return a view that allows staff to edit the refund percentage
        return view('staff.settings.refund', compact('settings'));
    }

    /**
     * Store or update site settings (POST /site-settings).
     * If you keep multiple keys, adapt this to your structure.
     */
    public function storeSiteSettings(Request $request)
    {
        // Save or update settings in a key-value manner
        foreach ($request->all() as $key => $value) {
            if ($key === '_token') {
                continue; // skip the CSRF token
            }
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return back()->with('success','Settings updated!');
    }

    /**
     * Update refund percentage (POST /staff/settings/refund).
     */
    public function updateRefundPercentage(Request $request)
    {
        $request->validate([
            'refund_percentage' => 'required|numeric|min:0|max:100'
        ]);

        if (!Auth::user()->isStaff()) {
            abort(403, "Unauthorized");
        }

        $settings = Setting::first();
        if (!$settings) {
            $settings = new Setting();
        }

        $settings->refund_percentage = $request->refund_percentage;
        $settings->save();

        return redirect()->back()->with('success','Refund percentage updated successfully.');
    }
}
