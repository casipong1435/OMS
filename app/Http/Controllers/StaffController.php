<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Business;
use App\Models\Establishment;
use App\Models\EstablishmentImages;
use App\Models\EstablishmentUnit;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Activity;
use App\Models\User;
use App\Services\SMSNotificationServices;
use App\Services\SystemNotificationServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StaffController extends Controller
{
    protected $smsNotification;
    protected $systemNotification;

    public function __construct()
    {
        $this->smsNotification = new SMSNotificationServices();
        $this->systemNotification = new SystemNotificationServices();
    }

    // Start Render CEEDO UI (GET)

    // Dashboard
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

        return Inertia::render('Ceedo/Dashboard/Summary', [
            'totalBusinessOperations' => $totalBusinessOperations,
            'totalVendorApplications' => $totalVendorApplications,
            'totalAreas' => $totalAreas,
            'totalEstablishments' => $totalEstablishments,
            'totalStallsAvailable' => $totalStallsAvailable,
            'totalStallsOccupied' => $totalStallsOccupied,
            'totalIncome' => $totalIncome,
            'monthlyIncome' => $formattedMonthlyIncome,
            'yearlyIncome' => $yearlyIncome,
            'year_selected' => $year_selected
        ]);
    }

    // Render All Vendors vue
    public function vendors(Request $request)
    {
        $searchInput = $request->input('searchInput');
        $vendors = Business::with([
            'profile',
            'requirement_image',
            'profile.barangay',
            'profile.user',
            'establishment_unit.establishment.area'
        ])
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

        return Inertia::render('Ceedo/Dashboard/Vendors/AllVendors', compact('vendors'));
    }

    // Render Applications vue
    public function applications(Request $request)
    {
        $searchInput = $request->input('searchInput');
        $applicants = Business::with([
            'profile',
            'requirement_image',
            'profile.barangay',
            'profile.user',
            'establishment_unit.establishment.area'
        ])
            ->where('status', 0)
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

        return Inertia::render('Ceedo/Dashboard/Vendors/Application', compact('applicants'));
    }

    public function applicationInfo($id)
    {
        $profile = Profile::with(['user', 'business.establishment_unit.establishment.area', 'business.requirement_image', 'region', 'province', 'city', 'barangay'])->where('id', $id)->first();
        return Inertia::render('Ceedo/Dashboard/Vendors/ApplicationInfo', compact('profile'));
    }

    public function vendorProfile($id)
    {
        $profile = Profile::with(['user', 'business.establishment_unit.establishment.area', 'business.requirement_image', 'region', 'province', 'city', 'barangay'])->where('id', $id)->first();
        return Inertia::render('Ceedo/Dashboard/Vendors/VendorProfile', compact('profile'));
    }

    // Render Compliance vue
    public function compliance()
    {
        return Inertia::render('Ceedo/Dashboard/Vendors/Compliance', ['role' => auth()->user()->role]);
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

        // Return data to the view with unpaid payments
        return Inertia::render('Ceedo/Dashboard/Vendors/PaymentDue', [
            'businessPayments' => $businessPayments,
        ]);
    }

    public function renewal()
    {
        $dateNow = Carbon::now('Asia/Manila');
        $businesses = Business::with('profile.user')->where('status', 1)->where('permit_expiration_date', '<', $dateNow)->get();
        return Inertia::render('Ceedo/Dashboard/Vendors/Renewal', ['businesses' => $businesses]);
    }

    // Render Establishment vue
    public function establishments()
    {
        $areas = $areas = Area::with(['establishment.establishment_units', 'business'])
            ->withCount([
                'establishment as establishment_count',
                'business as businesses_count',
            ])
            ->with(['establishment' => function ($query) {
                $query->select('id', 'area_id');  // Load necessary columns
            }])
            ->addSelect([
                'lowest_rate' => Establishment::selectRaw('MIN(rate)')
                    ->whereColumn('area_id', 'areas.id'),  // Match area_id
                'highest_rate' => Establishment::selectRaw('MAX(rate)')
                    ->whereColumn('area_id', 'areas.id'),  // Match area_id
            ])
            ->get();

        // dd($areas);
        return Inertia::render('Ceedo/Dashboard/Areas/Establishment', ['areas' => $areas]);
    }

    public function stalls($id)
    {
        $name = Area::find($id)->name;
        $establishments = Establishment::with(['establishment_units.business.profile', 'establishment_units.establishment_images'])->where('area_id', $id)->get();
        // dd($establishments);
        return Inertia::render('Ceedo/Dashboard/Areas/Stall', ['establishments' => $establishments, 'name' => $name, 'id' => $id]);
    }

    public function closed_business(Request $request)
    {
        $searchInput = $request->input('searchInput');
        $closed_business = Business::with([
            'profile',
            'requirement_image',
            'profile.barangay',
            'profile.user',
            'establishment_unit.establishment.area'
        ])
            ->where('status', 3)
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

        return Inertia::render('Ceedo/Dashboard/Vendors/ClosedBusiness', compact('closed_business'));
    }

    // Render Report Compliance vue
    public function compliance_report()
    {
        $dateNow = Carbon::now('Asia/Manila');
        $businesses = Business::with('profile')->where('status', 1)->where('permit_expiration_date', '<', $dateNow)->get();
        return Inertia::render('Ceedo/Dashboard/Reports/Compliance', ['businesses' => $businesses]);
    }

    // Render Report Opeartions vue
    public function operations_report()
    {
        $areas = Area::get();

        return Inertia::render('Ceedo/Dashboard/Reports/Operations', ['areas' => $areas]);
    }

    // Render Report Vendors vue
    public function vendors_report(Request $request)
    {
        $areas = Area::get();
        return Inertia::render('Ceedo/Dashboard/Reports/Vendors', compact('areas'));
    }

    // Render All Vendors vue
    public function activities()
    {
        $activities = Activity::where('user', auth()->user()->id)->paginate(20);
        return Inertia::render('Ceedo/Dashboard/History/Activities', ['activities' => $activities]);
    }

    // End Render CEEDO UI (GET)

    // POST METHODS

    public function addArea(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|unique:areas,name',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $filename = time() . '-' . Str::random(5) . '.' . $file->getClientOriginalExtension();

            $path = $file->move('images/Areas/Establishment', $filename);
        } else {
            $filename = '';
        }

        try {
            Area::create(['name' => $request->name, 'description' => $request->description, 'image' => $filename]);


            $activity = [
                'user' => auth()->user()->id,
                'activity' => "Added an area",
                'details' => "You added an area named ".$request->name
            ];

            Activity::create($activity);

            return redirect()->route('ceedo.establishments')->with('success', 'Area Added!');
        } catch (\Exception $e) {
            return redirect()->route('ceedo.establishments')->with('error', 'Something Went Wrong!');
        }
    }

    public function editArea(Request $request, $id)
    {
        // dd($request->all());

        $request->validate([
            'name' => 'required|unique:' . Area::class . ',name,' . $id,
        ]);

        $old_image = Area::where('id', $id)->first('image')->image;

        // dd($old_image, $request->image);
        $filename = '';

        if ($request->image != $old_image) {
            $request->validate([
                'image' => 'required|image|max:2048'
            ]);

            $file = $request->file('image');

            $filename = time() . '-' . Str::random(5) . '.' . $file->getClientOriginalExtension();

            $path = $file->move('images/Areas/Establishment', $filename);
            $oldFilePath = public_path('images/Areas/Establishment/' . $old_image);
            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }
        } else {
            $filename = $old_image;
        }

        try {
            Area::where('id', $id)->update(['name' => $request->name, 'description' => $request->description, 'image' => $filename]);

            $activity = [
                'user' => auth()->user()->id,
                'activity' => "Edited an area",
                'details' => "You edited the area ".$request->name
            ];

            Activity::create($activity);

            return redirect()->route('ceedo.establishments')->with('success', 'Area Updated!');
        } catch (\Exception $e) {
            return redirect()->route('ceedo.establishments')->with('error', 'Something Went Wrong!');
        }
    }

    public function updateAreaCoordinates(Request $request, $id)
    {
        try {
            Area::where('id', $id)->update(['lat' => $request->lat, 'long' => $request->long]);
            return redirect()->route('ceedo.establishments')->with('success', 'Coordinates Updated!');
        } catch (\Exception $e) {
            return redirect()->route('ceedo.establishments')->with('error', 'Something Went Wrong!');
        }
    }

    public function editMapVisibility($id)
    {
        $inMap = Area::where('id', $id)->first(['inMap'])->inMap;
        try {
            $status = $inMap == 0 ? 1 : 0;
            Area::where('id', $id)->update(['inMap' => $status]);
            return redirect()->route('ceedo.establishments')->with('success', 'Area Updated!');
        } catch (\Exception $e) {
            return redirect()->route('ceedo.establishments')->with('error', 'Something Went Wrong!');
        }
    }

    public function addEstablishment(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'rate' => 'required'
        ]);

        // dd($request->all());

        try {
            Establishment::create(['area_id' => $request->area_id, 'name' => $request->name, 'rate' => $request->rate]);

            $area_name = Area::where('id', $request->area_id)->first('name')->name;

            $activity = [
                'user' => auth()->user()->id,
                'activity' => "Add Area Section",
                'details' => "You added ".$request->name." to ".$area_name
            ];

            Activity::create($activity);

            return redirect()->back()->with('success', 'Establishment Added!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function editEstablishment(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'rate' => 'required'
        ]);

        // dd($request->all());

        try {
            Establishment::where('id', $id)->update(['name' => $request->name, 'rate' => $request->rate]);

            $activity = [
                'user' => auth()->user()->id,
                'activity' => "Edited Section",
                'details' => "You edited the section ".$request->name
            ];

            Activity::create($activity);

            return redirect()->back()->with('success', 'Establishment Updated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function addUnit(Request $request)
    {
        // dd($request->all());
        try {
            $i = 0;
            for ($i; $i < $request->quantity; $i++) {
                EstablishmentUnit::create(['establishment_id' => $request->establishment_ID, 'status' => 1]);
            }

            $establishment_name = Establishment::where('id', $request->establishment_ID)->first('name')->name;

            $activity = [
                'user' => auth()->user()->id,
                'activity' => "Added Stalls",
                'details' => "You added new stalls to ".$establishment_name
            ];

            Activity::create($activity);

            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function editEstablishmentUnit(Request $request)
    {
        // dd($request->all());

        try {
            if ($request->images) {
                // dd($request->images);

                foreach ($request->images as $image) {
                    $file = $image;

                    $filename = time() . '-' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                    // dd($filename);
                    $file->move('images/Areas/Establishment', $filename);
                    EstablishmentImages::create(['establishment_unit_id' => $request->establishmentUnitID, 'image' => $filename]);
                }
            }

            if ($request->removedImages) {
                foreach ($request->removedImages as $image) {
                    $data = json_decode($image, true);
                    // dd($data['id']);
                    $oldFilePath = public_path('images/Areas/Establishment/' . $data['image']);
                    if (File::exists($oldFilePath)) {
                        File::delete($oldFilePath);
                    }
                    EstablishmentImages::where('id', $data['id'])->delete();
                }
            }

            EstablishmentUnit::where('id', $request->establishmentUnitID)->update(['status' => $request->status]);

            return redirect()->back()->with('success', 'Unit Updated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('success', 'Unit Updated!');
        }
    }

    public function deleteUnit($id)
    {
        try {
            EstablishmentUnit::where('id', $id)->delete();
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function approveApplication($id)
    {
        try {
            $dateNow = Carbon::now('Asia/Manila');
            $business = Business::with(['profile.user'])->where('id', $id)->first();

            $area_name = Area::where('id', $business->area_id)->first('name')->name;

            Business::where('id', $id)->update(['status' => 1, 'date_approved' => $dateNow]);

            EstablishmentUnit::where('id', $business->establishment_unit_id)->update(['status' => 0]);

            $response = [
                'status' => 1,
                'recepient' => $business->profile->user->mobile_number,
                'area' => $area_name,
                'remarks' => ''
            ];

            $this->smsNotification->SendApplicationResponse($response);

            $activity = [
                'user' => auth()->user()->id,
                'activity' => "Approved Application",
                'details' => "You approved the application of ".$business->profile->first_name." ".$business->profile->last_name
            ];

            Activity::create($activity);
            
            $user = User::where('id', $business->profile->user_id)->first();
            $message = "Congratulations! Your application for stall was approved.";
            $this->systemNotification->sendNotification($user, $message);

            return redirect()->back()->with('success', 'Application Approved!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function declineApplication(Request $request, $id)
    {
        try {
            $dateNow = Carbon::now('Asia/Manila');

            $business = Business::with('profile.user')->where('id', $id)->first();

            $area_name = Area::where('id', $business->area_id)->first('name')->name;
            Business::where('id', $id)->update(['status' => 2, 'date_rejected' => $dateNow, 'remarks' => $request->remarks]);

            if ($request->remarks == '' || $request->remarks == null) {
                $request->remarks = 'No reason';
            }

            $response = [
                'status' => 2,
                'recepient' => $business->profile->user->mobile_number,
                'area' => $area_name,
                'remarks' => $request->remarks
            ];

            $this->smsNotification->SendApplicationResponse($response);

            $activity = [
                'user' => auth()->user()->id,
                'activity' => "Declinded Application",
                'details' => "You declinded the application of ".$business->profile->first_name." ".$business->profile->last_name
            ];

            Activity::create($activity);

            $user = User::where('id', $business->profile->user_id)->first();
            $message = "Sorry! Your application for stall was denied due to ".$request->remarks.".";
            $this->systemNotification->sendNotification($user, $message);

            return redirect()->back()->with('success', 'Application Declined!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function closeBusiness(Request $request, $id)
    {
        try {
            $dateNow = Carbon::now('Asia/Manila');
            $business = Business::with('profile.user')->where('id', $id)->first();
            EstablishmentUnit::where('id', $business->establishment_unit_id)->update(['status' => 1]);
            Business::where('id', $id)->update(['status' => 3, 'date_closed' => $dateNow, 'remarks' => $request->remarks]);

            if ($request->remarks == '' || $request->remarks == null) {
                $request->remarks = 'No reason';
            }

            $response = [
                'status' => 3,
                'recepient' => $business->profile->user->mobile_number,
                'remarks' => $request->remarks
            ];

            $this->smsNotification->SendStaffVerdict($response);

            $activity = [
                'user' => auth()->user()->id,
                'activity' => "Closed Business",
                'details' => "You closed the business owned by ".$business->profile->first_name." ".$business->profile->last_name." due to ".$request->remarks."."
            ];

            Activity::create($activity);

            $user = User::where('id', $business->profile->user_id)->first();
            $message = "Sorry! Your business was closed due to ".$request->remarks.". You can go to CEEDO office to appeal.";
            $this->systemNotification->sendNotification($user, $message);

            return redirect()->back()->with('success', 'Business Closed!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reopenBusiness($id)
    {
        try {
            $dateNow = Carbon::now('Asia/Manila');
            $business = Business::with('profile.user')->where('id', $id)->first();
            EstablishmentUnit::where('id', $business->establishment_unit_id)->update(['status' => 0]);
            Business::where('id', $id)->update(['status' => 1, 'remarks' => null, 'date_closed' => null, 'date_approved' => $dateNow]);

            $response = [
                'status' => 1,
                'recepient' => $business->profile->user->mobile_number,
                'remarks' => ''
            ];

            $this->smsNotification->SendStaffVerdict($response);

            $activity = [
                'user' => auth()->user()->id,
                'activity' => "Closesd Business",
                'details' => "You reopened the business owned by ".$business->profile->first_name." ".$business->profile->last_name."."
            ];

            Activity::create($activity);

            $user = User::where('id', $business->profile->user_id)->first();
            $message = "Congratulations! Your business has been reopened. You can now continue your business operation";
            $this->systemNotification->sendNotification($user, $message);

            return redirect()->back()->with('success', 'Business Reopened!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function SendPermitUpdateNotice(Request $request)
    {
        try {
            $this->smsNotification->SendPermitUpdateNotice($request->mobile_numbers);
            return redirect()->back()->with('success', 'Notification Sent!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function sendWarning(Request $request){
        try{
            $this->smsNotification->SendWarning($request->mobile_number);
            return redirect()->back()->with('success', 'Notification Sent!');
        }catch(Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function SendPaymentReminder(Request $request){
        try{
            $profile = Profile::with('user')->where('id', $request->profile_id)->first();
            $mobile_number = $profile->user->mobile_number;

            $response = [
                'recepient' => $mobile_number,
                'due_date' => Carbon::parse($request->payment['due_date'])->format('m/d/Y'),
                'amount' => $request->payment['amount']
            ];

            $this->smsNotification->SendPaymentReminder($response);

            $user = User::where('id', $profile->user_id)->first();

            $message = "Payment Reminder from CEEDO! Please pay your payment for ".Carbon::parse($request->payment['due_date'])->format('m/d/Y')." amounting to ₱".$request->payment['amount']." to avoid penalty or business closure.";

            $this->systemNotification->sendNotification($user, $message);

            return redirect()->back()->with('success', 'Notification Sent!');
        }catch(Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function deleteActivities(){
        try{
            Activity::query()->delete();
            return redirect()->back()->with('success', 'Activities Cleared!');
        }catch(Exception $e){
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
