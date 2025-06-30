<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Models\SubCategoryAvailability;
use App\Models\SubCategory;
use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;

use App\Mail\PaymentSuccessMail;  // We'll define these mail classes below
use App\Mail\PaymentErrorMail;

class PaymentController extends Controller
{
    /**
     * 1) Display a "pre‐booking" form or (if you already have the data),
     *    accept it from your "book‐apartment" step. 
     * 
     *    But per your request, we won't create the booking yet until we check availability.
     */
    public function preCheckAndCreate(Request $request)
    {
        // Validate form input
        $validated = $request->validate([
            'sub_category_id' => 'required|integer',
            'start_date'      => 'required|date|after_or_equal:today',
            'end_date'        => 'required|date|after:start_date',

            // "Guest vs. client" fields
            'booking_type'    => 'nullable|string', // 'guest' or 'client'
            'guest_name'      => 'nullable|string',
            'guest_email'     => 'nullable|email',
            'guest_phone'     => 'nullable|string',
            'guest_address'   => 'nullable|string',
            'guest_dob'       => 'nullable|date',
            'doc_type'        => 'nullable|string',
            'doc_number'      => 'nullable|string',
            'doc_upload'      => 'nullable|file|max:2048',

            'client_id'       => 'nullable|integer',

            'extra_guests'    => 'nullable',
        ]);

        // (A) Check date range for any day that is blocked or zero slots
        $subCatId = $request->sub_category_id;
        $start    = Carbon::parse($request->start_date);
        $end      = Carbon::parse($request->end_date);

        // We'll treat end_date as exclusive after 12pm. 
        // So the occupant leaves at noon on $end_date
        // i.e., you can do $end->subDay() if you want the calendar to exclude that day.
        // But let's keep it simple: the range is $start -> $end-1 day

        $dateRange = new \DatePeriod(
            new \DateTime($start->format('Y-m-d')),
            new \DateInterval('P1D'),
            (new \DateTime($end->format('Y-m-d'))) // exclusive
        );

        foreach ($dateRange as $dt) {
            $day = $dt->format('Y-m-d');

            $row = SubCategoryAvailability::where('sub_category_id', $subCatId)
                ->where('date', $day)
                ->first();
            // If no row => treat as unavailable or treat as "unlimited"
            if (!$row) {
                return redirect()->route('book.apartment')
                    ->with('error', "Date $day is not available for booking (no availability row).");
            }
            // If row is is_unavailable or slots < 1 => blocked
            if ($row->is_unavailable || $row->slots < 1) {
                return redirect()->route('book.apartment')
                    ->with('error', "Date $day is blocked or has 0 slots. Cannot book.");
            }
        }

        // (B) If we pass the loop, all days are good => create "pending" booking
        $staffId   = (Auth::check() && Auth::user()->isStaff()) ? Auth::id() : null;
        $clientId  = null;
        $guestName = null;

        if ($staffId && $request->booking_type === 'client') {
            // staff booking for existing client
            $clientId = $request->client_id;
        } elseif ($staffId && $request->booking_type === 'guest') {
            // staff booking for a walkin
            $guestName = $request->guest_name;
        } else {
            // normal client user
            $clientId  = Auth::id();
            $guestName = Auth::user()->name;
        }

        // doc upload if any
        $docPath = null;
        if ($request->hasFile('doc_upload')) {
            $docPath = $request->file('doc_upload')->store('documents','public');
        }

        $nights = $start->diffInDays($end);
        if ($nights < 1) {
            $nights = 1;
        }

        $booking = Booking::create([
            'staff_id'       => $staffId,
            'client_id'      => $clientId,
            'guest_name'     => $guestName,
            'guest_email'    => $request->guest_email,
            'guest_address'  => $request->guest_address,
            'guest_phone'    => $request->guest_phone,
            'guest_dob'      => $request->guest_dob,
            'doc_type'       => $request->doc_type,
            'doc_number'     => $request->doc_number,
            'doc_upload'     => $docPath,

            'sub_category_id'=> $subCatId,
            'start_date'     => $start->format('Y-m-d'),
            'end_date'       => $end->format('Y-m-d'),
            'nights'         => $nights,
            'status'         => 'pending',
        ]);

        // (C) Additional guests
        if ($request->extra_guests) {
            $guestsArr = json_decode($request->extra_guests,true) ?: [];
            foreach ($guestsArr as $g) {
                DB::table('booking_guests')->insert([
                    'booking_id' => $booking->id,
                    'name'       => $g['name']    ?? '',
                    'dob'        => $g['dob']     ?? null,
                    'phone'      => $g['phone']   ?? '',
                    'address'    => $g['address'] ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Redirect to the "Payment Confirmation" page
        return redirect()->route('payment.show', $booking->id);
    }

    /**
     * 2) Show Payment Confirmation page 
     *    (where user picks “cash” or “card,” sees total, etc.)
     */
public function confirmPayment($bookingId)
    {
        $booking = Booking::with('subCategory')->findOrFail($bookingId);
        $subCat  = $booking->subCategory;
        if (!$subCat) {
            return redirect()
                ->route('book.apartment')
                ->with('error','Invalid sub-category!');
        }

        $nights   = max($booking->nights, 1);
        $price    = $subCat->price ?? 0;
        $fullCost = $nights * $price;
        $differenceDue = floatval($booking->difference_due);
        $displayAmount = ($differenceDue > 0) ? $differenceDue : $fullCost;

        // Additional guests
        $extraGuests = DB::table('booking_guests')
                         ->where('booking_id', $booking->id)
                         ->get();

        // Pass your Paystack public key (assuming it's in your .env)
        $paystackPubKey = env('PAYSTACK_PUBLIC_KEY','');

        return view('payments.confirm', [
            'booking'            => $booking,
            'subCategory'        => $subCat,
            'totalCost'          => $fullCost,
            'extraGuests'        => $extraGuests,
            'difference_due'     => $differenceDue,
            'displayAmount'      => $displayAmount,
            // We no longer need flwPubKey, we use paystackPubKey
            'paystackPubKey'     => $paystackPubKey, 
        ]);
    }
    /**
     * Process Payment (store Payment record, update booking status, etc.)
     */
        public function processPayment(Request $request, $bookingId)
    {
        // 1) Load booking
        $booking = Booking::with('subCategory')->findOrFail($bookingId);

        if ($booking->status === 'successful' && $booking->difference_due <= 0) {
            return redirect()
                ->route('payment.success', $booking->id)
                ->with('info','This booking is already fully paid.');
        }

        // 2) Validate
        $request->validate([
            'payment_method' => 'required|string', // 'card','cash','account_balance'
        ]);

        // 3) Compute total owed
        $subCat = $booking->subCategory;
        $nights = max($booking->nights,1);
        $price  = $subCat ? $subCat->price : 0;
        $fullDue = $nights * $price;

        // Already paid
        $alreadyPaid = Payment::where('booking_id',$booking->id)
                              ->where('status','successful')
                              ->sum('amount');

        $normalDiff = $fullDue - $alreadyPaid;
        if ($normalDiff < 0) {
            $normalDiff = 0;
        }
        $difference = max($normalDiff, floatval($booking->difference_due));

        if ($difference <= 0) {
            // No payment needed
            if ($booking->status !== 'successful') {
                $booking->status = 'successful';
                $booking->difference_due = 0;
                $booking->save();
            }
            return redirect()
                ->route('payment.success',$booking->id)
                ->with('info','No payment required.');
        }

        // 4) final_payable
        $finalPayable = floatval($request->input('final_payable', $difference));
        if ($finalPayable <= 0) {
            $finalPayable = $difference;
        }

        // 5) Payment method
        $method  = $request->input('payment_method');
        $payment = null;

        // -----------------------------
        // A) Account Balance
        // -----------------------------
        if ($method === 'account_balance') {
            $user = auth()->user();
            if (!$user) {
                return redirect()->route('payment.error',$booking->id)
                                 ->with('error','Must be logged in to use account balance.');
            }
            if ($user->isStaff()) {
                return redirect()->route('payment.error',$booking->id)
                                 ->with('error','Staff cannot pay from account balance.');
            }

            $acct = $user->accountBalance;
            if (!$acct || $acct->balance <= 0) {
                return redirect()->route('payment.error',$booking->id)
                                 ->with('error','Insufficient account balance.');
            }

            $currentBal = $acct->balance;
            if($currentBal >= $finalPayable) {
                // Full coverage
                $acct->balance = $currentBal - $finalPayable;
                $acct->save();

                $payment = Payment::create([
                    'booking_id'     => $booking->id,
                    'amount'         => $finalPayable,
                    'payment_method' => 'account_balance',
                    'collected_by'   => $user->name,
                    'status'         => 'successful',
                    'reference'      => 'BAL-'.strtoupper(Str::random(8)),
                ]);

                if(($alreadyPaid + $finalPayable) >= $fullDue) {
                    $booking->status = 'successful';
                    $booking->difference_due = 0;
                } else {
                    $booking->status = 'pending';
                    $booking->difference_due = $difference - $finalPayable;
                }
                $booking->save();
            } else {
                // Partial coverage
                $leftover = $finalPayable - $currentBal;
                $acct->balance = 0;
                $acct->save();

                $payment = Payment::create([
                    'booking_id'     => $booking->id,
                    'amount'         => $currentBal,
                    'payment_method' => 'account_balance (partial)',
                    'collected_by'   => $user->name,
                    'status'         => 'successful',
                    'reference'      => 'BAL-'.strtoupper(Str::random(8)),
                ]);

                $booking->status = 'pending';
                $booking->difference_due = $difference - $currentBal;
                $booking->save();

                return redirect()
                    ->route('payment.confirm', $booking->id)
                    ->with('info', 
                        "Used ₦{$currentBal} from your balance. You still owe ₦{$leftover}."
                    );
            }
        }

        // -----------------------------
        // B) Cash (staff only)
        // -----------------------------
        elseif ($method === 'cash') {
            if (!auth()->check() || !auth()->user()->isStaff()) {
                return redirect()->route('payment.error',$booking->id)
                                 ->with('error','Only staff can do a cash payment.');
            }

            $payment = Payment::create([
                'booking_id'     => $booking->id,
                'amount'         => $finalPayable,
                'payment_method' => 'cash',
                'collected_by'   => auth()->user()->name,
                'status'         => 'successful',
                'reference'      => strtoupper(Str::random(10)),
            ]);

            if(($alreadyPaid + $finalPayable) >= $fullDue) {
                $booking->status = 'successful';
                $booking->difference_due = 0;
            } else {
                $booking->status = 'pending';
                $booking->difference_due = $difference - $finalPayable;
            }
            $booking->save();
        }

        // -----------------------------
        // C) Card (Paystack)
        // -----------------------------
        elseif ($method === 'card') {
            // Get the paystack ref from the form
            $paystackRef = $request->input('paystack_ref','');

            // Optionally, verify with Paystack:
            $verified = $this->verifyPaystackTransaction($paystackRef);
            if (!$verified) {
                return redirect()
                    ->route('payment.error',$booking->id)
                    ->with('error','Paystack transaction verification failed.');
            }

            // If verified, create a successful Payment record
            $payment = Payment::create([
                'booking_id'     => $booking->id,
                'amount'         => $finalPayable,
                'payment_method' => 'card',
                'collected_by'   => auth()->user()->name ?? 'GUEST',
                'status'         => 'successful',
                'reference'      => $paystackRef ?: ('CARD-'.strtoupper(Str::random(8))),
            ]);

            if(($alreadyPaid + $finalPayable) >= $fullDue) {
                $booking->status = 'successful';
                $booking->difference_due = 0;
            } else {
                $booking->status = 'pending';
                $booking->difference_due = $difference - $finalPayable;
            }
            $booking->save();
        }

        // -----------------------------
        // Invalid or unknown method
        // -----------------------------
        else {
            return redirect()
                ->route('payment.error',$booking->id)
                ->with('error','Invalid payment method selected.');
        }

        // If successful, log activity, send mail
        if ($payment && $payment->status === 'successful') {
            \App\Models\UserActivity::create([
                'user_id' => auth()->id(),
                'type'    => 'payment',
                'description' => "Paid ₦{$payment->amount} for booking #{$booking->id} 
                                  via {$payment->payment_method}",
            ]);
            $this->sendSuccessEmail($booking, $payment);
        }

        // Redirect to success
        return redirect()->route('payment.success', $booking->id);
    }


    /**
     * Payment success page
     */
   

     public function paymentSuccess($bookingId)
     {
         $booking = Booking::with('subCategory')->findOrFail($bookingId);
         if ($booking->status !== 'successful') {
             return redirect()->route('payment.error', $bookingId)
                 ->with('error','Payment not successful or canceled.');
         }
         $payment = Payment::where('booking_id', $booking->id)
                           ->orderBy('id','desc')->first();
 
         return view('payments.success', [
             'booking' => $booking,
             'payment' => $payment,
         ]);
     }
 
     /**
      * 5) Payment error
      */
     public function paymentError($bookingId)
     {
         $booking = Booking::with('subCategory')->findOrFail($bookingId);
         $this->sendErrorEmail($booking, 'Transaction error or canceled by user');
         return view('payments.error', [ 'booking' => $booking ]);
     }
 
     /**
      * Optional: Verify Paystack Transaction
      */
     private function verifyPaystackTransaction($reference)
     {
         $secretKey = env('PAYSTACK_SECRET_KEY','');
         if (!$reference) {
             return false;
         }
 
         $curl = curl_init();
         curl_setopt_array($curl, [
           CURLOPT_URL => "https://api.paystack.co/transaction/verify/{$reference}",
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_HTTPHEADER => [
             "Content-Type: application/json",
             "Authorization: Bearer {$secretKey}"
           ],
         ]);
         $response = curl_exec($curl);
         $err = curl_error($curl);
         curl_close($curl);
 
         if ($err) {
             return false;
         } else {
             $res = json_decode($response,true);
             if (isset($res['data']) && isset($res['data']['status']) 
                 && $res['data']['status'] === 'success') 
             {
                 // Optionally check if the amount matches what you expected
                 return true;
             }
         }
         return false;
     }
 
     /**
      * Helper: Send success email
      */
     private function sendSuccessEmail(Booking $booking, Payment $payment)
     {
         $email = $booking->guest_email;
         if ($booking->client_id && $booking->client) {
             $email = $booking->client->email;
         }
         if (!$email) return;
         Mail::to($email)->send(new PaymentSuccessMail($booking, $payment));
     }
 
     /**
      * Helper: Send error email
      */
     private function sendErrorEmail(Booking $booking, $reason)
     {
         $email = $booking->guest_email;
         if ($booking->client_id && $booking->client) {
             $email = $booking->client->email;
         }
         if (!$email) return;
         Mail::to($email)->send(new PaymentErrorMail($booking, $reason));
     }
 
 
     // The index(), settings(), toggleEmailNotify() etc. are unchanged...
     public function index()
     {
         if (!auth()->user()->isStaff()) {
             abort(403);
         }
         $setting = \App\Models\Setting::first();
         return view('payments.settings', compact('setting'));
     }
 
     public function settings()
     {
         if (!auth()->user()->isStaff()) {
             abort(403);
         }
     
         $setting = \App\Models\Setting::first(); 
         return view('payments.settings', compact('setting'));
     }
     
     public function toggleEmailNotify(Request $request)
     {
         if (!auth()->user()->isStaff()) {
             abort(403);
         }
     
         $setting = \App\Models\Setting::first();
         if (!$setting) {
             $setting = \App\Models\Setting::create([
                 'topup_email_notification' => true,
             ]);
         }
         $setting->topup_email_notification = 
             ! $setting->topup_email_notification;
         $setting->save();
     
         return back()->with('success','Email notification setting updated!');
     }
 }