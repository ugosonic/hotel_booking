<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\UserActivity;

class AccountManagementController extends Controller
{
    public function index(Request $request)
    {
        // Staff only
        if (!Auth::user()->isStaff()) {
            abort(403,'Unauthorized');
        }

        // We show two tabs: “Staff” and “Clients.” Or we can show them in one list with a filter
        $role = $request->input('role','client'); // default to client
        $query = User::where('role',$role);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search){
                $q->where('name','LIKE',"%$search%")
                  ->orWhere('email','LIKE',"%$search%");
            });
        }

        $users = $query->orderBy('id','desc')->paginate(10);

        return view('staff.account_management', compact('users','role'));
    }

    public function showProfile($userId)
    {
        // staff only, for example
        $user = User::findOrFail($userId);
    
        // use paginate(10), so we can call ->links() in Blade
        $activities = \App\Models\UserActivity::where('user_id', $userId)
            ->latest()
            ->paginate(10);
    
        return view('staff.show_profile', compact('user','activities'));
    }
    
    public function suspend($userId)
    {
        // staff only
        $user = User::findOrFail($userId);
        $user->status = 'suspended';
        UserActivity::create([
            'user_id' => auth()->id(),
            'type'    => 'suspend',
            'description' => "Suspended user #{$user->id} ({$user->name})"
          ]);
          
        $user->save();

        return redirect()->back()->with('success','User suspended successfully.');
    }

    public function unsuspend($userId)
    {
        $user = User::findOrFail($userId);
        $user->status = 'active';
        UserActivity::create([
            'user_id' => auth()->id(),
            'type'    => 'unsuspend',
            'description' => "Unsuspended user #{$user->id} ({$user->name})"
          ]);
          
        $user->save();

        return redirect()->back()->with('success','User unsuspended successfully.');
    }

    // "editProfile" => staff can change user’s name or email
    public function editProfile($userId)
    {
        if (!Auth::user()->isStaff()) abort(403);
        $user = User::findOrFail($userId);
        return view('staff.edit_user', compact('user'));
    }

    public function updateProfile(Request $request, $userId)
    {
        if(!Auth::user()->isStaff()) abort(403);
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string',
            'email'=> 'required|email',
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        UserActivity::create([
            'user_id' => auth()->id(),
            'type'    => 'profile_edit',
            'description' => "Updated profile of user #{$user->id} to name={$user->name}, email={$user->email}"
          ]);
          
        $user->save();

        // If we want to send an email link for password reset:
        if ($request->has('send_password_link')) {
            // We can store a token or something, or just do typical password reset email
            // This is up to your existing "ForgotPassword" logic
            // ...
        }

        return redirect()->route('staff.account_management',['role'=>$user->role])
            ->with('success','User updated successfully.');
    }
}
