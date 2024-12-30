<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Area;
use Carbon\Carbon;
use PDF;


class ReportController extends Controller
{
    public function financialReport($from , $to, $category){

        $date_from = Carbon::parse($from)->format('F j, Y');
        $date_to = Carbon::parse($to)->format('F j, Y');
        $category = json_decode($category);

        $areas = Area::whereIn('id', $category)->get();

        $vendors = Business::with([
            'profile.ownBarangay',
            'establishment_unit.establishment.area',
            'payment' // Ensure you include the payments relationship
        ])
        ->whereIn('status', [1, 3])
        ->orderBy('created_at', 'desc')
        ->when($category, function($query, $category) {
            $query->whereIn('area_id', $category);
        })
        ->get()
        ->map(function ($vendor) use ($date_from, $date_to) {
            // Ensure payments is always a collection, even if no payments exist
            $payment = $vendor->payment ?: collect(); // Default to empty collection if no payments

            // Filter payments by the date range and calculate the total for each business
            $filteredPayment = $payment->filter(function ($payment) use ($date_from, $date_to) {
                return $payment->due_date >= $date_from && $payment->due_date <= $date_to;
            });

            // Calculate total payments for the vendor
            $totalPayment = $filteredPayment->sum('amount'); // Assuming 'amount' is the payment amount column
            
            // Attach total payment to the vendor object
            $vendor->total_payment = $totalPayment;

            return $vendor;
        });

        $pdf = PDF::loadView('Reports.financialReport', ['date_from' => $date_from, 'date_to' => $date_to, 'vendors' => $vendors, 'areas' => $areas])->setPaper('folio', 'landscape');

        return $pdf->stream('Reports.financialReport');
    }

    public function operationalReport($category){

        $category = json_decode($category);

        $areas = Area::whereIn('id', $category)->get();
        
        $dateNow = Carbon::now('Asia/Manila')->format('F j, Y');
        $vendors = Business::with([
            'profile',
            'profile.barangay',
            'profile.user',
            'establishment_unit.establishment.area'
        ])
        ->orderBy('created_at', 'desc')
        ->when($category, function($query, $category){
            $query->whereIn('area_id', $category);
        })
        ->get();

        $pdf = PDF::loadView('Reports.OperationalReport', ['vendors' => $vendors, 'dateNow' => $dateNow, 'areas' => $areas])->setPaper('folio', 'landscape');

        return $pdf->stream('Reports.OperationalReport');
    }

    public function complianceReport(){
        
        
        $dateNow = Carbon::now('Asia/Manila');
        $businesses = Business::with('profile')->where('status', 1)->where('permit_expiration_date', '<', $dateNow)->get();

        $date_now = $dateNow->format('F j, Y');
        $pdf = PDF::loadView('Reports.ComplianceReport', ['date_now' => $date_now, 'businesses' => $businesses])->setPaper('folio', 'landscape');

        return $pdf->stream('Reports.ComplianceReport');
    }

}
