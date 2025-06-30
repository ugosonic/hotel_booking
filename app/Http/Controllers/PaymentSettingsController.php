<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\TopUp;

class PaymentSettingsController extends Controller
{
    
    public function toggleEmail()
    {
        if (!auth()->user()->isStaff()) {
            abort(403,'Unauthorized');
        }

        $setting = Setting::first();
        if (!$setting) {
            $setting = Setting::create([
                'refund_percentage' => 0,  // or some default
                'topup_email_notification' => false,
            ]);
        }

        $setting->topup_email_notification = 
               ! $setting->topup_email_notification;
        $setting->save();

        return back()->with('success','Email notification toggled!');
    }

    public function history(Request $request)
{
    if (!auth()->user()->isStaff()) {
        abort(403, 'Unauthorized');
    }

    // Suppose you want to see all topups that are not pending => i.e. only approved or rejected
    $query = \App\Models\TopUp::query();

    // If you only want topups that have status=approved or rejected:
    $query->whereIn('status',['approved','rejected']);

    // If you want to filter by date or user, or sort
    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }
    if ($request->filled('status')) {
        $query->where('status',$request->status);
    }
    if ($request->filled('start_date')) {
        $query->where('created_at','>=',$request->start_date.' 00:00:00');
    }
    if ($request->filled('end_date')) {
        $query->where('created_at','<=',$request->end_date.' 23:59:59');
    }

    // Sort or default
    $query->orderBy('id','desc');

    $topups = $query->paginate(10)->appends($request->all());

    return view('staff.topups.history', compact('topups'));
}

}
