<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OTPController extends Controller
{
    
    function generateOTP($userId) { $otp = Str::random(6); // Generate a 6-character OTP 
        $expiresAt = now()->addMinutes(5); // Set expiration time to 5 minutes 
        Cache::put('otp_' . $userId, $otp, $expiresAt); 
        return $otp;
    }

    function validateOTP($userId, $inputOtp) {
         $storedOtp = Cache::get('otp_' . $userId); 
         if (!$storedOtp) { 
            return false; 
            // OTP not found or expired 
        } if ($storedOtp !== $inputOtp) { 
            return false; // OTP does not match 
        } // Optionally remove the OTP after successful use 
            
        Cache::forget('otp_' . $userId); 

            return true;
    }

    public function requestOTP(Request $request) { 
        $user = auth()->user(); 
        $otp = generateOTP($user->id); 
        sendOTPSMS($user->phone_number, $otp); 
        return response()->json(['message' => 'OTP sent successfully']); 
    } 
    
    public function verifyOTP(Request $request) { 
        $user = auth()->user(); $inputOtp = $request->input('otp'); 
        if (validateOTP($user->id, $inputOtp)) {
             return response()->json(['message' => 'OTP verified successfully']); 
        } else {
             return response()->json(['message' => 'Invalid or expired OTP'], 422); 
        }
    }
}
