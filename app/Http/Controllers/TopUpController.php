<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TopUp;
use App\Models\BankDetail;
use App\Models\User;
use App\Models\Setting;
use App\Models\UserActivity;
use App\Models\AccountBalance;  // If you have an AccountBalance model

class TopUpController extends Controller
{
    /**
     * Show form for top up (client or staff).
     */
    public function showForm()
{
    $bankDetails = BankDetail::all();
    // Pull the key from .env
    $paystackPubKey = env('PAYSTACK_PUBLIC_KEY','');

    return view('topup.form', compact('bankDetails','paystackPubKey'));
}
    /**
     * Submit a top-up request (bank_transfer, card, or staff cash).
     */
    public function submitTopUp(Request $request)
    {
        $request->validate([
            'method'       => 'required|in:bank_transfer,card,cash',
            'amount'       => 'required|numeric|min:1',
            'paystack_ref' => 'nullable|string', // only relevant if method=card
        ]);
    
        // Usually the top‐up is for the logged‐in user
        $targetUserId = Auth::id();
    
        // If staff is topping up on behalf of another user
        if (Auth::user()->isStaff() && $request->filled('user_id')) {
            $targetUserId = $request->input('user_id');
        }
    
        $theUser = User::findOrFail($targetUserId);
    
        // (A) Bank Transfer
        if ($request->method === 'bank_transfer') {
            $request->validate([
                'bank_detail_id' => 'required|integer|exists:bank_details,id',
                'screenshot'     => 'required|file|mimes:jpg,png,jpeg|max:2048',
            ]);
    
            $path = $request->file('screenshot')->store('topups', 'public');
            
            $topup = TopUp::create([
                'user_id'        => $theUser->id,
                'method'         => 'bank_transfer',
                'amount'         => $request->amount,
                'status'         => 'pending', 
                'bank_detail_id' => $request->bank_detail_id,
                'screenshot_path'=> $path,
            ]);
    
            UserActivity::create([
                'user_id'     => $theUser->id,
                'type'        => 'topup',
                'description' => "Requested bank transfer top-up #{$topup->id} (₦{$request->amount})",
            ]);
    
            // Possibly notify staff
            $setting = \App\Models\Setting::first();
            if (!$setting || $setting->topup_email_notification) {
                $staffList = User::where('role','staff')
                                 ->where('status','active')
                                 ->pluck('email')->toArray();
                \Mail::to($staffList)->send(new \App\Mail\PendingTopUpMail($topup));
            }
    
            return redirect()->route('topup.form')
                             ->with('success','Your bank transfer request is pending. Staff will approve soon.');
        }
    
        // (B) Staff Cash
        elseif ($request->method === 'cash') {
            if (!Auth::user()->isStaff()) {
                return back()->with('error','Only staff can do a cash top-up');
            }
    
            // Staff physically handles cash => auto-approve
            $topup = TopUp::create([
                'user_id' => $theUser->id,
                'method'  => 'cash',
                'amount'  => $request->amount,
                'status'  => 'approved',
            ]);
    
            // Credit user’s account
            $acct = $theUser->accountBalance;
            if (!$acct) {
                $acct = \App\Models\AccountBalance::create([
                    'user_id' => $theUser->id,
                    'balance' => 0,
                ]);
            }
            $acct->balance += $request->amount;
            $acct->save();
    
            UserActivity::create([
                'user_id'     => Auth::id(),
                'type'        => 'topup',
                'description' => "Cash top-up #{$topup->id} (₦{$request->amount}) for user #{$theUser->id}",
            ]);
    
            return redirect()->route('topup.form')
                             ->with('success','Cash top-up added successfully!');
        }
    
        // (C) Card (Paystack)
        elseif ($request->method === 'card') {
            // 1) Retrieve reference from the form
            $paystackRef = $request->input('paystack_ref','');
    
            // 2) Verify with Paystack
            if (!$this->verifyPaystackTransaction($paystackRef)) {
                return redirect()->route('topup.form')
                                 ->with('error','Paystack verification failed or no reference provided.');
            }
    
            // 3) If verified => create topup as “approved” & credit user
            $topup = TopUp::create([
                'user_id' => $theUser->id,
                'method'  => 'card',
                'amount'  => $request->amount,
                'status'  => 'approved',
                'reference'=> $paystackRef,
            ]);
    
            // Update user’s account balance
            $acct = $theUser->accountBalance;
            if (!$acct) {
                $acct = \App\Models\AccountBalance::create([
                    'user_id' => $theUser->id,
                    'balance' => 0,
                ]);
            }
            $acct->balance += $request->amount;
            $acct->save();
    
            UserActivity::create([
                'user_id'     => $theUser->id,
                'type'        => 'topup',
                'description' => "Paystack card top-up #{$topup->id} (₦{$request->amount})",
            ]);
    
            return redirect()->route('topup.form')
                             ->with('success','Top-up successful via Paystack! Your balance has been updated.');
        }
    
        // else
        return back()->with('error','Invalid method selected.');
    }
    
    /**
     * Example Paystack verification function
     */
    private function verifyPaystackTransaction($reference)
    {
        if (!$reference) return false;
    
        $secretKey = env('PAYSTACK_SECRET_KEY','');
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
            $res = json_decode($response, true);
            if (isset($res['data']) && isset($res['data']['status']) 
                && $res['data']['status'] === 'success') {
                // Optionally check amount, currency, etc.
                return true;
            }
        }
        return false;
    }
    

    /**
     * (A) The main staff top-ups page:
     *     - pending top-ups, plus accepted/rejected "historyTopups"
     */
    public function index()
    {
        if (!auth()->user()->isStaff()) {
            abort(403,'Unauthorized');
        }

        // Pending
        $topups = TopUp::where('status','pending')
                       ->orderBy('id','desc')
                       ->paginate(10);

        // Accepted/Rejected
        $history = TopUp::whereIn('status',['approved','rejected'])
                        ->orderBy('updated_at','desc')
                        ->paginate(5);

        $setting = Setting::first();

        return view('staff.topups.index', [
            'topups'        => $topups,
            'setting'       => $setting,
            'historyTopups' => $history,
        ]);
    }

    /**
     * (B) If you have a separate route /staff/indexStaff,
     *     we do EXACTLY the same logic as index() so $historyTopups is also provided.
     */
    public function indexStaff()
    {
        if (!Auth::user()->isStaff()) {
            abort(403);
        }

        // Pending
        $topups = TopUp::where('status','pending')
                       ->orderBy('id','desc')
                       ->paginate(10);

        // Accepted/Rejected
        $history = TopUp::whereIn('status',['approved','rejected'])
                        ->orderBy('updated_at','desc')
                        ->paginate(5);

        $setting = Setting::first();

        // Return the same blade as index does
        return view('staff.topups.index', [
            'topups'        => $topups,
            'setting'       => $setting,
            'historyTopups' => $history,
        ]);
    }

    /**
     * Staff approves
     */
    public function approve(Request $request, TopUp $topUp)
    {
        if(!Auth::user()->isStaff()) abort(403);

        $topUp->status = 'approved';
        $topUp->approved_by = Auth::id();
        $topUp->save();

        // credit user’s account
        $acct = $topUp->user->accountBalance;
        if(!$acct){
            $acct = AccountBalance::create([
                'user_id' => $topUp->user_id,
                'balance' => 0
            ]);
        }
        $acct->balance += $topUp->amount;
        $acct->save();

        UserActivity::create([
            'user_id'     => Auth::id(),
            'type'        => 'staff_action',
            'description' => "Approved top-up #{$topUp->id} for user #{$topUp->user_id} (₦{$topUp->amount})"
        ]);

        return back()->with('success','Top-up approved and user’s balance updated.');
    }

    /**
     * Staff rejects
     */
    public function reject(Request $request, TopUp $topUp)
    {
        if(!Auth::user()->isStaff()) abort(403);

        $topUp->status = 'rejected';
        $topUp->approved_by = Auth::id();
        $topUp->save();

        UserActivity::create([
            'user_id'     => Auth::id(),
            'type'        => 'staff_action',
            'description' => "Rejected top-up #{$topUp->id} for user #{$topUp->user_id}",
        ]);

        return back()->with('info','Top-up request rejected.');
    }
}
