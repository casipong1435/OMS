<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Cache;


 class SMSNotificationServices
{

    protected $twilio;
    protected $phone;

    public function __construct(){
        $sid = getenv("TWILIO_SID");
        $token = getenv("TWILIO_TOKEN");
        $this->phone = getenv("TWILIO_PHONE");
        $this->twilio = new Client($sid, $token);
    }

    public function sendOTP($recepient){
        //Cache::forget("otp_{$recepient}");
        $otp = random_int(100000, 999999); // Generate a 6-digit OTP
        Cache::put("otp_{$recepient}", $otp, now()->addMinutes(5));
        $message = $this->twilio->messages->create(
            '+63'.$recepient, // To
            [
                "body" =>
                    "Your OTP is: $otp. It will expire in 5 minutes.",
                "from" => $this->phone,
            ]
        );
    }

    public function verifyOTP($otp, $recepient)
    {
        // Retrieve OTP from cache
        $cachedOtp = Cache::get("otp_{$recepient}");
        //dd($cachedOtp);
        if ($cachedOtp == $otp) {
            // OTP is valid
            Cache::forget("otp_{$recepient}"); // Remove OTP after successful verification
            return true;
        }else{
            return false;
        }
    }

    public function SendPaymentReminder($response){
        $message = $this->twilio->messages->create(
            '+63'.$response['recepient'], // To
            [
                "body" =>
                    "Payment Reminder from CEEDO! Please pay your payment for ".$response['due_date']." amounting to ₱".$response['amount']." to avoid penalty or business closure. Check your account for more info!",
                "from" => $this->phone,
            ]
        );
    }

    public function SendPaymentPaid($response){
        $message = $this->twilio->messages->create(
            '+63'.$response['recepient'], // To
            [
                "body" =>
                    "Payment Successful! You paid your payment for ".$response['due_date']." amounting to ₱".$response['amount'].", paid at ".$response['paid_at'].".",
                "from" => $this->phone,
            ]
        );
    }

    public function SendPermitUpdateNotice($recepients){
        foreach($recepients as $recepient){
            $message = $this->twilio->messages->create(
                '+63'.$recepient, // To
                [
                    "body" =>
                        "Your business permit needs to be renewed. Renew your payment in order to avoid penalty",
                    "from" => $this->phone,
                ]
            );
        }
    }

    public function SendWarning($recepient){
        $message = $this->twilio->messages->create(
            '+63'.$recepient, // To
            [
                "body" =>
                    "This is a warning from CEEDO! Compliance to requirements is necessary to continue operate your business. Check your account for compliance and comply on time to avoid penalties.",
                "from" => $this->phone,
            ]
        );
    }

    public function SendApplicationResponse($response){
        if($response['status'] == 1){
            $message = $this->twilio->messages->create(
                '+63'.$response['recepient'], // To
                [
                    "body" =>
                        "Congratulations! Your Application for a stall in ".$response['area']." has been approved. You can now start your business operation in that area.",
                    "from" => $this->phone,
                ]
            );
        }else{
            $message = $this->twilio->messages->create(
                '+63'.$response['recepient'], // To
                [
                    "body" =>
                        "Thank you for your application for a stall in ".$response['area']. ". Unfortunately, your application for a stall has been declined due to ".$response['remarks'].". We appreciate your interest and encourage you to apply again in the future.",
                    "from" => $this->phone,
                ]
            );
        }
    }

    public function SendStaffVerdict($response){
        if($response['status'] == 1){
            $message = $this->twilio->messages->create(
                '+63'.$response['recepient'], // To
                [
                    "body" =>
                        "Congratulations! Your business is now reopen. You can now continue your business operation. Please comply the requirements or payments on time to avoid penalty.",
                    "from" => $this->phone,
                ]
            );
        }else{
            $message = $this->twilio->messages->create(
                '+63'.$response['recepient'], // To
                [
                    "body" =>
                        "Sorry! The stall you rented and your business has been closed due to ".$response['remarks'].". You can go to the Tangub City Economic Enterprises and Development Office for more information.",
                    "from" => $this->phone,
                ]
            );
        }
    }
}
