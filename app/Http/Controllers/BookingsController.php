<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

// Carbon import to help with date/time differences
use Carbon\Carbon;

use App\Models\Booking;
use App\Models\BookingCancellation;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\AccountBalance;
use App\Models\UserActivity;
use App\Models\SystemGain; // for system credits/debits

class BookingsController extends Controller
{
    public function index(Request $request)
    {
        // 1) Delete all pending bookings that have expired
        $this->cleanUpOldPending();

        // 2) Build the query
        $query = Booking::query();

        // Distinguish staff vs. client
        if (auth()->user()->isStaff()) {
            // Staff sees all
            if ($request->filled('client_id')) {
                $query->where('client_id', $request->client_id);
            }
            if ($request->filled('guest_only') && $request->guest_only == '1') {
                $query->whereNull('client_id');
            }
        } else {
            // A normal client sees only their own
            $query->where('client_id', auth()->id());
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date.' 00:00:00');
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date.' 23:59:59');
        }

        // Search by name or ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('guest_name','LIKE',"%$search%")
                  ->orWhere('client_name','LIKE',"%$search%")
                  ->orWhere('id','LIKE',"%$search%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sort      = $request->get('sort','created_at');
        $direction = $request->get('direction','desc');
        $query->orderBy($sort, $direction);

        // 3) Paginate
        $bookings = $query->paginate(10)->appends($request->all());

        // 4) For each booking => compute countdown if pending_expires_at is set
        foreach ($bookings as $b) {
            if ($b->status === 'pending' && $b->pending_expires_at) {
                // Compare now() to the pending_expires_at
                $expiresAt = Carbon::parse($b->pending_expires_at);
                // Let’s compute the difference in seconds from now to expiresAt
                $diffSec = Carbon::now()->diffInSeconds($expiresAt, false);

                // If it’s negative, that means it’s already expired
                if ($diffSec < 0) {
                    $b->countdownSeconds = 0;
                } else {
                    $b->countdownSeconds = $diffSec;
                }
            } else {
                $b->countdownSeconds = 0;
            }
        }

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Auto-delete all pending bookings whose pending_expires_at <= now().
     */
    private function cleanUpOldPending()
    {
        Booking::where('status','pending')
            ->where('pending_expires_at','<=', now())
            // (Optional) Exclude bookings that have partial payments, etc.
            ->whereDoesntHave('payments', function($q) {
                // Example: skip if they have an 'account_balance (partial)' method
                $q->where('payment_method', 'account_balance (partial)');
            })
            ->delete();
    }

    /**
     * Staff can extend the wait time for a pending booking.
     */
    public function extendWaitTime(Request $request, $bookingId)
    {
        if (!Auth::user()->isStaff()) {
            abort(403, 'Unauthorized');
        }

        $booking = Booking::findOrFail($bookingId);

        // Must be "pending" to extend
        if ($booking->status !== 'pending') {
            return redirect()->back()
                ->with('error','Cannot extend a non-pending booking.');
        }

        $hours = (int) $request->input('hours', 2); // default 2 hours

        // If pending_expires_at is null, set from now
        if (empty($booking->pending_expires_at)) {
            $booking->pending_expires_at = now()->addHours($hours);
        } else {
            $dt = Carbon::parse($booking->pending_expires_at);
            // If already past, reset from now; otherwise add hours
            if ($dt->isPast()) {
                $booking->pending_expires_at = now()->addHours($hours);
            } else {
                $booking->pending_expires_at = $dt->addHours($hours);
            }
        }

        $booking->save();

        return redirect()->back()->with('success',
            "Booking #{$booking->id} extended by {$hours} hour(s)."
        );
    }

    public function show(Booking $booking)
    {
        // A client can see only their own booking; staff can see any
        if (!Auth::user()->isStaff() && $booking->client_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('bookings.show', compact('booking'));
    }

    public function change($bookingId)
    {
        // Make sure this booking belongs to the current client
        $booking = Booking::where('id', $bookingId)
                          ->where('client_id', Auth::id())
                          ->firstOrFail();

        return view('bookings.client_change', compact('booking'));
    }

    public function showClientCancelPage()
    {
        $bookings = Booking::where('client_id', auth()->id())->get();
        return view('bookings.client_cancel', compact('bookings'));
    }

    public function showStaffCancelPage()
    {
        if (!auth()->user()->isStaff()) {
            abort(403, "Unauthorized");
        }
        $bookings = Booking::orderBy('id','desc')->get();
        return view('bookings.staff_cancel', compact('bookings'));
    }

    public function cancel()
    {
        // If you want to show a list of user’s bookings that they can cancel:
        $bookings = Booking::where('client_id', Auth::id())->get();
        return view('bookings.client_cancel', compact('bookings'));
    }

    public function showByUuid($uuid)
{
    $booking = Booking::where('uuid', $uuid)->firstOrFail();
    // Same logic as the regular show() method:
    // staff can see all; client sees only their own, etc.
    if (!auth()->user()->isStaff() && $booking->client_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }
    return view('bookings.show', compact('booking'));
}




public function cancelBooking(Request $request, $bookingId)
{
    $booking = Booking::findOrFail($bookingId);

    // Staff can cancel any booking; client can only cancel their own
    if (!Auth::user()->isStaff() && $booking->client_id !== Auth::id()) {
        abort(403, "Unauthorized to cancel this booking.");
    }

    // If already canceled, do nothing
    if ($booking->status === 'canceled') {
        return redirect()->back()->with('info', 'Booking already canceled.');
    }

    // Process refund and system gain regardless of current booking status
    $settings = Setting::first();
    $refundPercentage = $settings ? $settings->refund_percentage : 0;
    $refundAmount = $booking->total_amount * ($refundPercentage / 100);

    // 1) Credit the refund amount to the client's account (if applicable)
    if ($booking->client_id && $refundAmount > 0) {
        $balance = AccountBalance::firstOrCreate(['user_id' => $booking->client_id]);
        $balance->balance += $refundAmount;
        $balance->save();
    }

    // 2) Create a negative Payment record for the refund (if refund amount is positive)
    if ($refundAmount > 0) {
        Payment::create([
            'booking_id'     => $booking->id,
            'amount'         => -$refundAmount,
            'payment_method' => 'refund',
            'collected_by'   => Auth::user()->name,
            'status'         => 'successful',
            'reference'      => 'REFUND-' . Str::upper(Str::random(6)),
        ]);
    }

    // 3) Calculate system gain (the portion the system retains)
    $systemPortion = $booking->total_amount - $refundAmount;
    if ($systemPortion < 0) {
        $systemPortion = 0;
    }
    if ($systemPortion > 0) {
        SystemGain::create([
            'booking_id' => $booking->id,
            'user_id'    => Auth::id(),
            'type'       => 'refund',
            'amount'     => $systemPortion,
        ]);
    }

    // 4) Record the cancellation details
    BookingCancellation::create([
        'booking_id'      => $booking->id,
        'user_id'         => $booking->client_id, // Cancellation "belongs" to the client
        'canceled_by'     => Auth::id(),          // Staff or client who triggered the cancellation
        'canceled_amount' => $booking->total_amount,
        'refunded_amount' => $refundAmount,
        'canceled_at'     => now(),
    ]);

    // Mark booking as canceled and save
    $booking->status = 'canceled';
    $booking->save();

    // Log user activity
    UserActivity::create([
        'user_id'     => Auth::id(),
        'type'        => 'cancel',
        'description' => "Canceled booking #{$booking->id} (client #{$booking->client_id})",
    ]);

    return redirect()->back()->with('success', 'Booking was canceled successfully.');
}



}