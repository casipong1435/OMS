<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Business;
use App\Models\Establishment;
use App\Models\EstablishmentImages;
use App\Models\EstablishmentUnit;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\User;
use App\Services\SMSNotificationServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MayorController extends Controller
{
    public function dashboard(Request $request)
    {
        $year_selected = $request->input('year_selected', now()->year);
        // Total business operations (active businesses)
        $totalBusinessOperations = Business::where('status', 1)->count();

        // Total vendor applications (submitted applications)
        $totalVendorApplications = Business::where('status', 0)->count();

        // Total areas inside (areas available for rent or use)
        $totalAreas = Area::count();

        // Total establishments inside areas (all establishments, including businesses)
        $totalEstablishments = Establishment::count();

        // Total stalls available (stalls that are not occupied)
        $totalStallsAvailable = EstablishmentUnit::where('status', 1)->count();

        // Total stalls occupied (stalls that are occupied)
        $totalStallsOccupied = EstablishmentUnit::where('status', 0)->count();

        // Total income from rented stalls or areas (assuming Payments table stores the transaction)
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

        return Inertia::render('Mayor/Dashboard/Summary', [
            'totalBusinessOperations' => $totalBusinessOperations,
            'totalVendorApplications' => $totalVendorApplications,
            'totalAreas' => $totalAreas,
            'totalEstablishments' => $totalEstablishments,
            'totalStallsAvailable' => $totalStallsAvailable,
            'totalStallsOccupied' => $totalStallsOccupied,
            'totalIncome' => $totalIncome,
            'monthlyIncome' => $formattedMonthlyIncome,
            'year_selected' => $year_selected,
            'yearlyIncome' => $yearlyIncome
        ]);
    }

    public function business_operation()
    {
        $areas = Area::with(['establishment.establishment_units'])
            ->withCount(['business as active_business_count' => function ($query) {
                $query->where('status', 1);
            }])
            ->get();

        return Inertia::render('Mayor/Dashboard/BusinessOperation', compact('areas'));
    }

    public function sections(Request $request, $id)
    {
        $searchInput = $request->input('searchInput');
        $area_id = $id;
        $name = Area::find($id)->name;
        $vendors = Business::with(['profile', 'establishment_unit.establishment'])
            ->where('area_id', $id)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->whereHas('profile', function ($query) use ($searchInput) {
                $query->when($searchInput, function ($q, $searchInput) {
                    $q
                        ->where('first_name', 'LIKE', '%' . $searchInput . '%')
                        ->orWhere('middle_name', 'LIKE', '%' . $searchInput . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $searchInput . '%')
                        ->orWhere('id', 'LIKE', '%' . $searchInput . '%');
                });
            })
            ->get();

        return Inertia::render('Mayor/Dashboard/Sections', compact('name', 'vendors', 'area_id'));
    }

    public function vendorProfile($id)
    {
        $profile = Profile::with(['user', 'business.establishment_unit.establishment.area', 'business.requirement_image', 'region', 'province', 'city', 'barangay'])->where('id', $id)->first();
        return Inertia::render('Mayor/Dashboard/VendorProfile', compact('profile'));
    }

    public function financial_monitoring(Request $request)
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

            // Collect business data with generated payments
            if (count($payments) > 0) {
                $businessPayments[] = [
                    'business_id' => $business->id,
                    'business_name' => $business->name,  // Assuming the profile has a 'name' field
                    'status' => $business->status,
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

        $paymentHistory = Payment::with('business.profile')->orderBy('due_date', 'desc')->get();

        return Inertia::render('Mayor/Dashboard/FinancialMonitoring', [
            'businessPayments' => $businessPayments, 'paymentHistory' => $paymentHistory
        ]);
    }

    public function compliance()
    {
        $dateNow = Carbon::now('Asia/Manila');
        $businesses = Business::with('profile')->where('status', 1)->where('permit_expiration_date', '<', $dateNow)->get();
        return Inertia::render('Mayor/Dashboard/Compliance', ['businesses' => $businesses]);
    }

    // Render Report Compliance vue
    public function compliance_report()
    {
        $dateNow = Carbon::now('Asia/Manila');
        $businesses = Business::with('profile')->where('status', 1)->where('permit_expiration_date', '<', $dateNow)->get();
        return Inertia::render('Mayor/Dashboard/Reports/Compliance', ['businesses' => $businesses]);
    }

    // Render Report Opeartions vue
    public function operations_report()
    {
        $areas = Area::get();

        return Inertia::render('Mayor/Dashboard/Reports/Operations', ['areas' => $areas]);
    }

    // Render Report Vendors vue
    public function vendors_report(Request $request)
    {
        $areas = Area::get();
        return Inertia::render('Mayor/Dashboard/Reports/Vendors', compact('areas'));
    }

    public function getOperationsReport(Request $request)
    {
        $category = $request->input('category');
        $category = json_decode($category);

        $vendors = Business::with([
            'profile',
            'profile.barangay',
            'establishment_unit.establishment.area'
        ])
            ->whereIn('status', [1, 3])
            ->orderBy('created_at', 'desc')
            ->when($category, function ($query, $category) {
                $query->whereIn('area_id', $category);
            })
            ->get();

        return response()->json(['vendors' => $vendors]);
    }
}
