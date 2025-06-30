<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\TopUp;
use App\Models\Booking;

class StaffPaymentsController extends Controller
{
    public function dailyPayments(Request $request)
    {
        if (!auth()->user()->isStaff()) abort(403);

        // 1) Grab date from request or default to today
        $dateStr = $request->input('date', Carbon::now()->format('Y-m-d'));
        $start   = Carbon::parse($dateStr)->startOfDay();
        $end     = Carbon::parse($dateStr)->endOfDay();

        // 2) Filter Payment records that happened that day
        $paymentQuery = Payment::whereBetween('created_at',[$start,$end]);

        // If user picks a method to filter (like ?method=cash)
        if ($request->filled('method')) {
            $paymentQuery->where('payment_method', $request->method);
        }
        // If sorting
        $sortColumn = $request->get('sort','id'); 
        $sortDir    = $request->get('direction','desc');
        $paymentQuery->orderBy($sortColumn, $sortDir);

        $payments = $paymentQuery->get();
        $paymentTotal = $payments->sum('amount');  // Summation of amounts 
        // If you store negative amounts for refunds, that sum already includes it

        // 3) We can also check “approved” top-ups from this day
        $topupQuery = TopUp::where('status','approved')
                           ->whereBetween('updated_at',[$start,$end]);
        if ($request->filled('topup_method')) {
            $topupQuery->where('method',$request->topup_method);
        }
        $topups = $topupQuery->get();
        $topupTotal = $topups->sum('amount');

        // Possibly the “system gain” from bookings is Payment sum minus refunds. 
        // If you are storing the refund as Payment with negative amount, you can 
        // see that “$paymentTotal” is already net. If you want it separated:
        $refunds   = $payments->where('payment_method','refund')->sum('amount'); // likely negative
        $nonRefund = $payments->where('payment_method','!=','refund')->sum('amount');
        // So the system gain from booking is $nonRefund + $refunds, or just $paymentTotal.

        // 4) Return it to a daily_payments view
        return view('staff.finance.daily_payments', [
            'payments'     => $payments,
            'paymentTotal' => $paymentTotal,
            'topups'       => $topups,
            'topupTotal'   => $topupTotal,
            'refundsTotal' => $refunds,
            'dateStr'      => $dateStr,
        ]);
    }
}
