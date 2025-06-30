<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TopUp;
use App\Models\Booking;
use App\Models\AccountBalance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function clientIndex()
    {
        // 1) Load or create the user’s balance
        $balanceModel = AccountBalance::firstOrCreate(['user_id' => Auth::id()]);
        $balance = $balanceModel->balance;

        // 2) Attempt to find a "current" successful booking
        $booking = Booking::with(['subCategory.images', 'payments'])
            ->where('client_id', Auth::id())
            ->where('status', 'successful')
            ->whereDate('end_date', '>=', now()->format('Y-m-d'))
            ->orderBy('start_date', 'asc')
            ->first();

        $currentBooking = null;
        if ($booking) {
            // If you only want to call it "current" if it's before 12pm on end_date
            $endDateNoon = Carbon::parse($booking->end_date)->setTime(12, 0, 0);
            if (now()->lt($endDateNoon)) {
                $currentBooking = $booking;
            }
        }

        // 3) Find a "next pending" booking
        $nextPendingBooking = Booking::with(['subCategory.images', 'payments'])
            ->where('client_id', Auth::id())
            ->where('status', 'pending')
            ->orderBy('start_date', 'asc')
            ->first();

        return view('dashboard.client_dashboard', [
            'balance' => $balance,
            'currentBooking' => $currentBooking,
            'nextPendingBooking' => $nextPendingBooking,
        ]);
    }

    /**
     * (Optional) If you had a staff dashboard:
     */
    public function staffIndex()
    {
        // e.g. count pending top-ups
        $pendingCount = TopUp::where('status','pending')->count();
        return view('dashboard.staff_dashboard', compact('pendingCount'));
    }
}
