<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// The RegistersUsers trait includes basic registration logic (optional in newer Laravel versions)
// If you want to rely on your own logic, you can skip this trait
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    // Pull in default methods (create, validator, etc.)
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // This works only if Controller extends BaseController from Laravel
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     */
    public function showRegistrationForm()
    {
        // Make sure you have a "register.blade.php" in your resources/views directory
        return view('register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        // Validate user input
        $this->validator($request->all())->validate();

        // Create user in database
        $user = $this->create($request->all());

        // (Optional) Log them in directly
        // auth()->login($user);

        // Redirect them somewhere (e.g., a dashboard)
        return redirect('/dashboard');
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
