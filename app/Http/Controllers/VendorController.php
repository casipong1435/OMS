<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\Business;
use App\Models\City;
use App\Models\EstablishmentUnit;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Province;
use App\Models\Region;
use App\Models\RequirementImage;
use App\Models\User;
use App\Services\SMSNotificationServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;

class VendorController extends Controller
{
    protected $smsNotification;

    public function __construct()
    {
        $this->smsNotification = new SMSNotificationServices();
    }

    public function dashboard()
    {
        try {
            // Get the business associated with the authenticated user's profile
            $business = Business::where('profile_id', auth()->user()->profile->id)->whereIn('status', [1, 3])->first();

            // Handle case when no business is found
            if (!$business) {
                return Inertia::render('Vendor/Page/Dashboard', [
                    'paymentHistory' => [],
                    'totalAmount' => 0,
                    'totalMonths' => 0,
                    'isPermitNotExpired' => null,
                    'nextPayment' => null,  // or false depending on your use case
                    'nextPayment' => null,
                    'overduePayments' => [],
                    'paymentHistory' => []
                ]);
            }

            // Get payment history for the business
            $paymentHistory = Payment::where('business_id', $business->id)->orderBy('due_date', 'desc')->get();

            // Calculate the total amount of payments
            $totalAmount = $paymentHistory->sum('amount');

            $month_interval = 0;

            //dd($business->payment_cycle);
            switch($business->payment_cycle){
                case 0:
                    $month_interval = 1;
                    break;  // Monthly
                case 1: 
                    $month_interval = 3;  // Quarterly
                    break;
                case 2:
                    $month_interval = 6;  // Bi-annual
                    break;
                case 3: 
                    $month_interval = 12;  // Annual
                    break;
            }

            $monthCount = $paymentHistory->map(function ($payment) {
                return Carbon::parse($payment->due_date);
            })->sort()->pipe(function ($dates) use ($business) {
                $firstDate = $dates->first(); // Get the earliest due_date
                $referenceDate = Carbon::parse($business->date_approved); // Reference date
                return $referenceDate->diffInMonths($firstDate);
            });

            $totalMonths = $monthCount + $month_interval;

            // Check permit expiration
            if (!empty($business->permit_expiration_date)) {
                $permitExpirationDate = Carbon::parse($business->permit_expiration_date);
                $isPermitNotExpired = Carbon::now('Asia/Manila')->lt($permitExpirationDate);
            } else {
                $isPermitNotExpired = false;  // Default to expired if no expiration date
            }

            // Define the current date
            $currentDate = now();

            // Get the closure date (nullable)
            $closureDate = $business->status == 3 ? Carbon::parse($business->date_closed) : null;

            // Determine the boundary date: the earlier of closureDate or currentDate (with advance payment consideration)
            $boundaryDate = $closureDate ?? $currentDate->copy()->addMonths(1);  // Allow at least one advance payment

            $dateApproved = Carbon::parse($business->date_approved);
            $paymentCycle = $business->payment_cycle;  // Monthly, quarterly, bi-annual, annual

            // Determine the cycle interval in months
            $cycleInterval = match ($paymentCycle) {
                0 => 1,  // Monthly
                1 => 3,  // Quarterly
                2 => 6,  // Bi-annual
                3 => 12,  // Annual
                default => 1,
            };

            // Get the last paid payment for this business (if any)
            $lastPaidPayment = Payment::where('business_id', $business->id)
                ->orderBy('due_date', 'desc')
                ->first();

            // Starting point for payment cycles
            $startDate = $lastPaidPayment
                ? Carbon::parse($lastPaidPayment->due_date)
                : $dateApproved->copy();

            $payments = [];  // Array to hold generated payments

            // Generate payments for all cycles that are overdue, due, or advance
            while ($startDate->lessThan($boundaryDate)) {
                // Generate a payment for this cycle
                $dueDate = $startDate->copy()->addMonths($cycleInterval);

                // Adjust the due date if it exceeds the closure date
                if ($closureDate && $dueDate->greaterThan($closureDate)) {
                    $dueDate = $closureDate;  // Limit to the closure date
                }

                // Determine the status based on the due date
                $status = match (true) {
                    $dueDate->lessThan($currentDate) => 'Overdue',
                    $dueDate->equalTo($currentDate) => 'Due',
                    default => 'Not Yet'
                };

                // Calculate the number of days and the amount
                $days = $startDate->diffInDays($dueDate);  // Adjusted for partial cycles
                $amount = $business->establishment_unit->establishment->rate * $days;

                $payments[] = [
                    'due_date' => $dueDate,
                    'amount' => number_format($amount, 2),
                    'rate' => $business->establishment_unit->establishment->rate,
                    'days' => $days,
                    'status' => $status,
                ];

                // Break the loop if the business is closed after this cycle
                if ($closureDate && $dueDate->equalTo($closureDate)) {
                    break;
                }

                $startDate = $dueDate;  // Move to the next cycle
            }

            // Return data to the dashboard
            return Inertia::render('Vendor/Page/Dashboard', [
                'paymentHistory' => $paymentHistory,
                'totalAmount' => $totalAmount,
                'totalMonths' => $totalMonths,
                'isPermitNotExpired' => $isPermitNotExpired,
                'payments' => $payments,
                'business_status' => $business->status
            ]);
        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Error loading dashboard: ' . $e->getMessage());

            // Optionally return a user-friendly error page or message
            return Inertia::render('ErrorPage', [
                'message' => 'An error occurred while loading the dashboard. Please try again later.',
            ]);
        }
    }

    public function business()
    {
        $profile = Profile::with(['user', 'business.establishment_unit.establishment.area', 'business.requirement_image', 'region', 'province', 'city', 'barangay'])->where('id', auth()->user()->profile->id)->first();
        return Inertia::render('Vendor/Page/Business', compact('profile'));
    }

    public function payment(Request $request)
    {
        $business = Business::with(['profile', 'establishment_unit.establishment'])
            ->where('profile_id', auth()->user()->profile->id)
            ->first();

        if ($business) {
            // Define the current date
            $currentDate = now();

            // Get the closure date (nullable)
            $closureDate = $business->status == 3 ? Carbon::parse($business->date_closed) : null;

            // Determine the boundary date: the earlier of closureDate or currentDate (with advance payment consideration)
            $boundaryDate = $closureDate ?? $currentDate->copy()->addMonths(1);  // Allow at least one advance payment

            $dateApproved = Carbon::parse($business->date_approved);
            $paymentCycle = $business->payment_cycle;  // Monthly, quarterly, bi-annual, annual

            // Determine the cycle interval in months
            $cycleInterval = match ($paymentCycle) {
                0 => 1,  // Monthly
                1 => 3,  // Quarterly
                2 => 6,  // Bi-annual
                3 => 12,  // Annual
                default => 1,
            };

            // Get the last paid payment for this business (if any)
            $lastPaidPayment = Payment::where('business_id', $business->id)
                ->orderBy('due_date', 'desc')
                ->first();

            // Starting point for payment cycles
            $startDate = $lastPaidPayment
                ? Carbon::parse($lastPaidPayment->due_date)
                : $dateApproved->copy();

            $payments = [];  // Array to hold generated payments

            // Generate payments for all cycles that are overdue, due, or advance
            while ($startDate->lessThan($boundaryDate)) {
                // Generate a payment for this cycle
                $dueDate = $startDate->copy()->addMonths($cycleInterval);

                // Adjust the due date if it exceeds the closure date
                if ($closureDate && $dueDate->greaterThan($closureDate)) {
                    $dueDate = $closureDate;  // Limit to the closure date
                }

                // Determine the status based on the due date
                $status = match (true) {
                    $dueDate->lessThan($currentDate) => 'Overdue',
                    $dueDate->equalTo($currentDate) => 'Due',
                    default => 'Not Yet'
                };

                // Calculate the number of days and the amount
                $days = $startDate->diffInDays($dueDate);  // Adjusted for partial cycles
                $amount = $business->establishment_unit->establishment->rate * $days;

                $payments[] = [
                    'due_date' => $dueDate,
                    'amount' => number_format($amount, 2),
                    'rate' => $business->establishment_unit->establishment->rate,
                    'days' => $days,
                    'status' => $status,
                ];

                // Break the loop if the business is closed after this cycle
                if ($closureDate && $dueDate->equalTo($closureDate)) {
                    break;
                }

                $startDate = $dueDate;  // Move to the next cycle
            }

            // Payment history (from the database)
            $paymentHistory = Payment::where('business_id', $business->id)->orderBy('due_date', 'desc')->get();

            // dd($business);

            return Inertia::render('Vendor/Page/Payment', [
                'payments' => $payments,
                'paymentHistory' => $paymentHistory,
                'business' => $business,
            ]);
        } else {
            return Inertia::render('Vendor/Page/Payment', [
                'nextPayment' => null, 'overduePayments' => [], 'paymentHistory' => []
            ]);
        }
    }

    public function profile()
    {
        $profile = Profile::with(['business.requirement_image', 'user'])->where('id', auth()->user()->profile->id)->first();
        $regions = Region::get(['regCode', 'regDesc']);

        return Inertia::render('Vendor/Page/Profile', compact('profile', 'regions'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'purok' => 'required',
            'barangay' => 'required',
            'city' => 'required',
            'province' => 'required',
            'region' => 'required'
        ]);

        try {
            $id = auth()->user()->id;
            Profile::where('user_id', $id)->update($request->all());
            return redirect()->back()->with('success', 'Profile Updated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function changeProfile(Request $request)
    {
        $id = auth()->user()->id;

        try {
            $old_image = Profile::where('user_id', $id)->first('image')->image;
            $filename = '';

            $file = $request->imageData['image'];
            // dd($file);

            $filename = time() . '-' . Str::random(5) . '.' . $file->getClientOriginalExtension();

            $path = $file->move('images/clients/', $filename);

            $oldFilePath = public_path('images/clients/' . $old_image);

            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }

            Profile::where('user_id', $id)->update(['image' => $filename]);
            return redirect()->back()->with('success', 'Profile Image Changed!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function changePhoneNumber(Request $request) {}

    public function sendOTP(Request $request)
    {
        $id = auth()->user()->id;
        $request->validate([
            'new_mobile_number' => 'required|unique:users,mobile_number'
        ]);

        // dd($request->all());

        try {
            $this->smsNotification->sendOTP($request->new_mobile_number);
            return redirect()->back()->with('success', 'OTP Sent!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send OTP: ' . $e->getMessage());
        }
    }

    public function verifyOTP(Request $request)
    {
        $id = auth()->user()->id;
        $otp = $request->otp;
        // dd($request->all());
        try {
            $isVerified = $this->smsNotification->verifyOTP($otp, $request->new_mobile_number);
            if ($isVerified) {
                User::where('id', $id)->update(['mobile_number' => $request->new_mobile_number]);
                return redirect()->back()->with('success', 'Mobile Number Updated!');
            } else {
                return redirect()->back()->with('errorOTP', 'OTP Invalid!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function applyStall(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'kind_of_business' => 'required',
            'name' => 'required',
            'plate' => 'required',
            'permit_number' => 'required',
            'permit_expiration_date' => 'required',
            'dti_reg_number' => 'required',
            'cedula' => 'required',
            'cedula_cert' => 'required',
            'brgy_clearance' => 'required',
            'pmo_ceedo_clearance' => 'required',
            'dti_cert' => 'required',
            'medical_cert' => 'required',
            'business_permit' => 'required',
            'payment_cycle' => 'required|in:0,1,2,3'
        ]);

        try {
            $area = EstablishmentUnit::with('establishment')->where('id', $request->establishment_unit_id)->first();

            $business_values = [
                'profile_id' => auth()->user()->profile->id,
                'establishment_unit_id' => $request->establishment_unit_id,
                'kind_of_business' => $request->kind_of_business,
                'name' => $request->name,
                'plate' => $request->plate,
                'permit_number' => $request->permit_number,
                'permit_expiration_date' => $request->permit_expiration_date,
                'dti_reg_number' => $request->dti_reg_number,
                'payment_cycle' => $request->payment_cycle,
                'cedula' => $request->cedula,
                'area_id' => $area->establishment->area_id
            ];

            $business = Business::create($business_values);

            $filename_cedula_cert = time() . '-' . Str::random(5) . '.' . $request->cedula_cert->getClientOriginalExtension();
            $request->cedula_cert->move('images/business/', $filename_cedula_cert);

            $filename_brgy_clearance = time() . '-' . Str::random(5) . '.' . $request->brgy_clearance->getClientOriginalExtension();
            $request->brgy_clearance->move('images/business/', $filename_brgy_clearance);

            $filename_pmo_ceedo_clearance = time() . '-' . Str::random(5) . '.' . $request->pmo_ceedo_clearance->getClientOriginalExtension();
            $request->pmo_ceedo_clearance->move('images/business/', $filename_pmo_ceedo_clearance);

            $filename_dti_cert = time() . '-' . Str::random(5) . '.' . $request->dti_cert->getClientOriginalExtension();
            $request->dti_cert->move('images/business/', $filename_dti_cert);

            $filename_medical_cert = time() . '-' . Str::random(5) . '.' . $request->medical_cert->getClientOriginalExtension();
            $request->medical_cert->move('images/business/', $filename_medical_cert);

            $filename_business_permit = time() . '-' . Str::random(5) . '.' . $request->business_permit->getClientOriginalExtension();
            $request->business_permit->move('images/business/', $filename_business_permit);

            $requirement_image_values = [
                'business_id' => $business->id,
                'cedula' => $filename_cedula_cert,
                'brgy_clearance' => $filename_brgy_clearance,
                'pmo_ceedo_clearance' => $filename_pmo_ceedo_clearance,
                'dti_cert' => $filename_dti_cert,
                'medical_cert' => $filename_medical_cert,
                'business_permit' => $filename_business_permit
            ];

            RequirementImage::create($requirement_image_values);
            EstablishmentUnit::where('id', $request->establishment_unit_id)->update(['status' => 2]);

            return redirect()->back()->with('success', 'Application Submitted!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updatePermit(Request $request)
    {
        try {
            $request->validate([
                'new_permit_number' => 'required',
                'new_expiration_date' => 'required',
                'new_permit_image' => 'required'
            ]);

            Business::where('id', $request->business_id)->update(['permit_number' => $request->new_permit_number, 'permit_expiration_date' => $request->new_expiration_date]);

            $old_permit_image = RequirementImage::where('business_id', $request->business_id)->first('business_permit')->business_permit;

            if ($request->new_permit_image) {
                $filename = time() . '.' . $request->new_permit_image->getClientOriginalExtension();

                $image_data = public_path('images/business/' . $old_permit_image);
                if (!empty($old_permit_image) && file_exists($image_data)) {
                    unlink($image_data);
                }

                $request->new_permit_image->move('images/business/', $filename);
            } else {
                $filename = $old_permit_image;
            }

            RequirementImage::where('business_id', $request->business_id)->update(['business_permit' => $filename]);

            return redirect()->back()->with('success', 'Business Permit Updated!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function markasReadNotification($id){
        try{
            $notification = auth()->user()->notifications->find($id);
            $notification->markAsRead();
            return redirect()->back();
        }catch(Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function markasReadAllNotification(){
        try{
            $user = auth()->user();
            $user->unreadNotifications->markAsRead();
            return redirect()->back();
        }catch(Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
