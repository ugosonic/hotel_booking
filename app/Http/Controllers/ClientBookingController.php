<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Models\AccountBalance;

class ClientBookingController extends Controller
{
    // Display the "Cancel Bookings" page with filters
    public function cancel(Request $request)
    {
        $query = Booking::where('client_id', Auth::id());

        // (A) Filtering by date range => created_at
        if ($request->filled('start_date')) {
            $query->where('created_at','>=',$request->start_date.' 00:00:00');
        }
        if ($request->filled('end_date')) {
            $query->where('created_at','<=',$request->end_date.' 23:59:59');
        }

        // (B) Search by booking ID or guest_name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id','like',"%$search%")
                  ->orWhere('guest_name','like',"%$search%");
            });
        }

        // (C) Filter by status
        if ($request->filled('status')) {
            $query->where('status',$request->status);
        }

        // (D) Sorting
        $sort      = $request->get('sort','id'); // default
        $direction = $request->get('direction','desc');
        $query->orderBy($sort, $direction);

        // (E) Paginate
        $bookings = $query->paginate(10)->appends($request->all());

        return view('bookings.client_cancel', compact('bookings'));
    }

    // POST route to actually cancel a booking
    public function cancelBooking($bookingId)
    {
        $booking = Booking::where('id', $bookingId)
                          ->where('client_id', Auth::id())
                          ->firstOrFail();

        if ($booking->status === 'canceled') {
            return back()->with('info','Booking already canceled.');
        }

        // Optional partial refund if booking is "successful"
        if ($booking->status === 'successful') {
            $settings = Setting::first();
            $refundPerc = $settings ? $settings->refund_percentage : 0;

            $amount = $booking->total_amount * ($refundPerc / 100);

            // Update client’s account balance
            $balance = AccountBalance::firstOrCreate(['user_id' => Auth::id()]);
            $balance->balance += $amount;
            $balance->save();
        }

        // Mark as canceled
        $booking->status = 'canceled';
        $booking->save();

        return back()->with('success','Booking canceled successfully.');
    }

    // Display the change booking form for a client
    public function change($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Ensure that the booking belongs to the authenticated client
        if ($booking->client_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        return view('bookings.client_change', compact('booking'));
    }

    // Process the update for a booking (client)
    public function update(Request $request, $bookingId)
    {
        $booking = Booking::where('id', $bookingId)
                          ->where('client_id', Auth::id())
                          ->firstOrFail();
        
        // Validate the input
        $validated = $request->validate([
            'start_date'  => 'required|date|after_or_equal:today',
            'end_date'    => 'required|date|after:start_date',
            'guest_phone' => 'nullable|string|max:50',
        ]);
        
        // Calculate nights
        $start = \Carbon\Carbon::parse($validated['start_date']);
        $end   = \Carbon\Carbon::parse($validated['end_date']);
        $nights = $start->diffInDays($end);
        if ($nights < 1) {
            $nights = 1;
        }
        
        // Recalc total_amount if needed
        $price         = $booking->price; // stored price from DB
        $total_amount  = $price * $nights;

        $booking->update([
            'start_date'   => $validated['start_date'],
            'end_date'     => $validated['end_date'],
            'nights'       => $nights,
            'guest_phone'  => $validated['guest_phone'],
            'total_amount' => $total_amount,
        ]);
        
        return redirect()->route('bookings.show', $booking->id)
                         ->with('success', 'Your booking has been updated successfully.');
    }
}
