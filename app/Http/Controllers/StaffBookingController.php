<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Models\AccountBalance;

class StaffBookingController extends Controller
{
    // Show all bookings with filters => staff cancel page
    public function cancel(Request $request)
    {
        if (!Auth::user()->isStaff()) {
            abort(403, 'Unauthorized');
        }

        // Staff sees all bookings by default:
        $query = Booking::query();

        // 1) Filter by created_at date range
        if ($request->filled('start_date')) {
            $query->where('created_at','>=',$request->start_date.' 00:00:00');
        }
        if ($request->filled('end_date')) {
            $query->where('created_at','<=',$request->end_date.' 23:59:59');
        }

        // 2) Search (ID, guest_name, OR client_name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id','like',"%$search%")
                  ->orWhere('guest_name','like',"%$search%")
                  ->orWhere('client_name','like',"%$search%");
            });
        }

        // 3) Filter by status
        if ($request->filled('status')) {
            $query->where('status',$request->status);
        }

        // 4) Sort
        $sort      = $request->get('sort','id');
        $direction = $request->get('direction','desc');
        $query->orderBy($sort, $direction);

        // 5) Paginate
        $bookings = $query->paginate(10)->appends($request->all());

        return view('bookings.staff_cancel', compact('bookings'));
    }

    // Staff actually cancels the booking
    public function doCancel($bookingId)
    {
        if (!Auth::user()->isStaff()) {
            abort(403, 'Unauthorized');
        }

        $booking = Booking::findOrFail($bookingId);

        if ($booking->status === 'canceled') {
            return back()->with('info','Booking is already canceled.');
        }

        // If booking was "successful", partial refund
        if ($booking->status === 'successful') {
            $settings = Setting::first();
            $refundPerc = $settings ? $settings->refund_percentage : 0;

            $refundAmount = $booking->total_amount * ($refundPerc / 100);

            // credit the client’s balance if booking->client_id
            if ($booking->client_id) {
                $balance = AccountBalance::firstOrCreate(['user_id' => $booking->client_id]);
                $balance->balance += $refundAmount;
                $balance->save();
            }
        }

        // Mark canceled
        $booking->status = 'canceled';
        $booking->save();

        return back()->with('success','Booking canceled successfully.');
    }
}
