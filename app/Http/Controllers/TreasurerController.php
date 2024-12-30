<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Business;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\User;
use App\Services\SMSNotificationServices;
use App\Services\SystemNotificationServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TreasurerController extends Controller
{
    protected $smsNotification;
    protected $systemNotification;

    public function __construct()
    {
        $this->smsNotification = new SMSNotificationServices();
        $this->systemNotification = new SystemNotificationServices();
    }

    public function dashboard(Request $request)
    {
        $year_selected = $request->input('year_selected', now()->year);

        // Count active businesses
        $activeBusinesses = Business::where('status', 1)->count();

        // Total income for the year
        $totalIncome = Payment::whereYear('due_date', $year_selected)->sum('amount');

        // Monthly income breakdown
        $monthlyIncome = Payment::selectRaw('MONTH(due_date) as month, SUM(amount) as total')
            ->whereYear('due_date', $year_selected)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Fill missing months with 0
        $formattedMonthlyIncome = [];
        for ($i = 1; $i <= 12; $i++) {
            $formattedMonthlyIncome[] = $monthlyIncome->get($i, 0);
        }

        $yearlyIncome = Area::select('id', 'name')
            ->with(['business.payment' => function ($query) use ($year_selected) {
                // If a specific year is selected, filter payments based on due_date
                if ($year_selected) {
                    $query->whereYear('due_date', $year_selected);
                }
            }])
            ->get()
            ->map(function ($area) {
                // Ensure the sum of the payments only for the filtered year
                $totalPayments = $area->business->flatMap->payment->sum('amount');

                return [
                    'name' => $area->name,
                    'total_payments' => $totalPayments,
                ];
            })
            ->sortBy('name');

        return Inertia::render('Treasurer/Dashboard/Summary', [
            'activeBusinesses' => $activeBusinesses,
            'totalIncome' => number_format($totalIncome, 2),
            'monthlyIncome' => $formattedMonthlyIncome,
            'year_selected' => $year_selected,
            'yearlyIncome' => $yearlyIncome
        ]);
    }

    public function payment_due(Request $request)
    {
        // Fetch all businesses with their profiles and establishments
        $businesses = Business::with(['profile', 'establishment_unit.establishment'])
            ->whereIn('status', [1, 3])  // Assuming status 1 and 3 represent active or relevant businesses
            ->get();

        // Define the current date
        $currentDate = now();
        $businessPayments = [];

        foreach ($businesses as $business) {
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

            // Get the closure date (nullable)
            $closureDate = $business->status == 3 ? Carbon::parse($business->date_closed) : null;

            // Determine the boundary date: the earlier of closureDate or currentDate (with advance payment consideration)
            $boundaryDate = $closureDate ?? $currentDate->copy()->addMonths(1);  // Allow at least one advance payment

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
                    default => 'Advance'
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

            // Collect business data with generated payments
            if (count($payments) > 0) {
                $businessPayments[] = [
                    'business_id' => $business->id,
                    'business_name' => $business->name,  // Assuming the profile has a 'name' field
                    'payments' => $payments,
                    'first_name' => $business->profile->first_name,
                    'middle_name' => $business->profile->middle_name,
                    'last_name' => $business->profile->last_name,
                    'profile_id' => $business->profile->id,
                    'plate' => $business->plate,
                    'business_status' => $business->status,
                ];
            }
        }

        return Inertia::render('Treasurer/Dashboard/PaymentDue', [
            'businessPayments' => $businessPayments,
        ]);
    }

    public function payment_portal(Request $request, $id)
    {
        // Fetch the business with its profile and establishments
        $business = Business::with(['profile', 'establishment_unit.establishment'])
            ->where('id', $id)
            ->first();

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

        return Inertia::render('Treasurer/Dashboard/PaymentPortal', ['payments' => $payments, 'business' => $business]);
    }

    public function transaction()
    {
        $paymentHistory = Payment::with('business.profile')->orderBy('due_date', 'desc')->get();
        // dd($paymentHistory);
        return Inertia::render('Treasurer/Dashboard/Transaction', compact('paymentHistory'));
    }

    public function report()
    {
        $areas = Area::get();

        return Inertia::render('Treasurer/Dashboard/Report', compact('areas'));
    }

    public function getVendorsReport(Request $request)
    {
        $category = $request->input('category');
        $category = json_decode($category);
        $startDate = $request->input('start_date');  // Expected in format Y-m-d
        $endDate = $request->input('end_date');  // Expected in format Y-m-d

        // Get vendors with their payments within the date range and filter by area_id
        $vendors = Business::with([
            'profile',
            'profile.barangay',
            'establishment_unit.establishment.area',
            'payment'  // Ensure you include the payments relationship
        ])
            ->whereIn('status', [1, 3])
            ->orderBy('created_at', 'desc')
            ->when($category, function ($query, $category) {
                $query->whereIn('area_id', $category);
            })
            ->get()
            ->map(function ($vendor) use ($startDate, $endDate) {
                // Ensure payments is always a collection, even if no payments exist
                $payment = $vendor->payment ?: collect();  // Default to empty collection if no payments

                // Filter payments by the date range and calculate the total for each business
                $filteredPayment = $payment->filter(function ($payment) use ($startDate, $endDate) {
                    return $payment->due_date >= $startDate && $payment->due_date <= $endDate;
                });

                // Calculate total payments for the vendor
                $totalPayment = $filteredPayment->sum('amount');  // Assuming 'amount' is the payment amount column

                // Attach total payment to the vendor object
                $vendor->total_payment = $totalPayment;

                return $vendor;
            });

        return response()->json(['vendors' => $vendors]);
    }

    public function pay(Request $request)
    {
        // dd($request['selectedPayment']);
        try {
            $business = Business::with('profile.user')->where('id', $request->business_id)->first();

            foreach ($request['selectedPayment'] as $payment) {
                $values = [
                    'business_id' => $request->business_id,
                    'amount' => str_replace(',', '', $payment['amount']),
                    'due_date' => Carbon::parse($payment['due_date']),
                    'paid_at' => Carbon::now('Asia/Manila'),
                    'remark' => $payment['status']
                ];

                Payment::create($values);

                $response = [
                    'recepient' => $business->profile->user->mobile_number,
                    'due_date' => Carbon::parse($payment['due_date'])->format('m/d/Y'),
                    'amount' => $payment['amount'],
                    'paid_at' => Carbon::now('Asia/Manila')
                ];

                $this->smsNotification->SendPaymentPaid($response);

                $user = User::where('id', $business->profile->user_id)->first();
                $ceedo = User::where('role', 2)->get();

                $message_to_user = "Thank You for Paying! You paid for ".Carbon::parse($payment['due_date'])->format('m/d/Y')." amounting to ₱".$payment['amount'].".";

                $message_to_ceedo = $business->profile->first_name." ".$business->profile->last_name." paid for ".Carbon::parse($payment['due_date'])->format('m/d/Y')." amounting to ₱".$payment['amount'].".";


                $this->systemNotification->sendNotification($user, $message_to_user);
                $this->systemNotification->sendNotification($ceedo, $message_to_ceedo);
            }

            return redirect()->back()->with('success', 'Payment Successful');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
