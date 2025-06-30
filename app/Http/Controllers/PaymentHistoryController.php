<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class PaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        // Must be a client or staff? We assume this is for the logged‐in client.
        // If staff wants to see a client’s payment history, you can add logic. 
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }
        // If user is client => show only that user’s payments
        // If user is staff => maybe show all or do something else
        // For simplicity, we assume only clients see their own payment history:

        $user = Auth::user();
        if ($user->isStaff()) {
            // staff sees all or you could do another method
            // We'll show them everything. Or do something else
            $query = Payment::query();
        } else {
            // client => only their bookings => or user_id on Payment if you store it
            // But your Payment table only has "booking_id." So we join:
            $query = Payment::whereHas('booking', function($q) use ($user) {
                $q->where('client_id', $user->id);
            });
        }

        // optional filters, e.g. date range
        if ($request->filled('start_date')) {
            $query->where('created_at','>=',$request->start_date . ' 00:00:00');
        }
        if ($request->filled('end_date')) {
            $query->where('created_at','<=',$request->end_date . ' 23:59:59');
        }

        // pagination
        $payments = $query->orderBy('id','desc')->paginate(10);

        return view('payments.history', compact('payments'));
    }
}
