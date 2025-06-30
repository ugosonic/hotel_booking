<?php

namespace App\Http\Controllers;

use App\Models\BankDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserActivity;

class BankDetailsController extends Controller
{
    public function index()
    {
        // staff only
        if(!Auth::user()->isStaff()){
            abort(403,'Unauthorized');
        }
        $details = BankDetail::orderBy('id','desc')->get();
        return view('staff.bank_details.index',compact('details'));
    }

    public function create()
    {
        if(!Auth::user()->isStaff()){
            abort(403,'Unauthorized');
        }
        return view('staff.bank_details.create');
    }

    public function store(Request $request)
    {
        if(!Auth::user()->isStaff()) abort(403,'Unauthorized');
        $validated = $request->validate([
            'bank_name'    => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number'=>'required|string|max:50',
        ]);

        $bd = BankDetail::create($validated);

        // Log user activity
        \App\Models\UserActivity::create([
            'user_id'     => Auth::id(),
            'type'        => 'staff_action',
            'description' => "Added a new bank detail #{$bd->id}"
        ]);

        return redirect()->route('staff.bank_details.index')
                         ->with('success','Bank detail added!');
    }

    public function destroy(BankDetail $bankDetail)
    {
        if(!Auth::user()->isStaff()) abort(403,'Unauthorized');
        $bankDetail->delete();

        // Log user activity
        \App\Models\UserActivity::create([
            'user_id'     => Auth::id(),
            'type'        => 'staff_action',
            'description' => "Removed bank detail #{$bankDetail->id}"
        ]);

        return redirect()->route('staff.bank_details.index')
                         ->with('success','Bank detail deleted!');
    }
}
