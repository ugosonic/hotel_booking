<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;    // Import Mail
use Illuminate\Support\Str;
use App\Mail\PasswordResetSuccessMail;  // Import your Mailable (if you have one)

class ResetPasswordController extends Controller
{
    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Handle the reset form submission.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password'        => bcrypt($request->password),
                    'remember_token'  => Str::random(60),
                ])->save();

                // Optionally log them in immediately:
                Auth::login($user);
            }
        );

        // If successful => optionally send an email, then redirect.
        if ($status === Password::PASSWORD_RESET) {
            // e.g. “Your password has been reset” email
            Mail::to($request->email)->send(new PasswordResetSuccessMail());

            return redirect()->route('login')
                ->with('success', 'Password reset successfully!');
        }

        // Otherwise, show errors
        return back()->withErrors([
            'email' => [__($status)],
        ]);
    }
}
