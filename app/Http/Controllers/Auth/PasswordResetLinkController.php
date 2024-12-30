<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Services\SMSNotificationServices;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Cache;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */

     protected $smsNotification;

    public function __construct()
    {
        $this->smsNotification = new SMSNotificationServices();
    }

    public function create(): Response
    {

        $canReset = Cache::has("can_reset");
        $mobile_number = Cache::get("mobile_number");

        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
            'canReset' => $canReset,
            'mobile_number' => $mobile_number
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'mobile_number' => 'required',
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    public function sendOTP(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required'
        ]);

        try {
            $this->smsNotification->sendOTP($request->mobile_number);
            return redirect()->back()->with('success', 'OTP Sent!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send OTP: ' . $e->getMessage());
        }
    }

    public function verifyOTP(Request $request)
    {
        $otp = $request->otp;
        try {
            $isVerified = $this->smsNotification->verifyOTP($otp, $request->mobile_number);
           
            if ($isVerified) {
                Cache::put("can_reset", true, now()->addMinutes(5));
                Cache::put("mobile_number", $request->mobile_number, now()->addMinutes(5));
                return redirect()->back();
            } else {
                return redirect()->back()->with('errorOTP', 'OTP Invalid!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
