<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\Business;
use App\Models\City;
use App\Models\Profile;
use App\Models\Province;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Hash;

class AdminController extends Controller
{
    // render dashboard
    public function dashboard()
    {
        // Total officials
        $totalOfficials = User::whereNot('role', 0)->count();

        // Total non-official users
        $totalNonOfficials = User::where('role', 0)->count();

        // Total users with businesses
        $totalUsersWithBusiness = Business::where('status', 1)->count();

        // Fetch the growth of users by year and month
        $userGrowthData = User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, count(*) as user_count')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Initialize an empty array to store user growth data for each year
        $formattedGrowthData = [];

        // Fill the data for each year and month
        $currentYear = date('Y');
        $years = range(2024, $currentYear);  // Adjust the start year as needed

        foreach ($years as $year) {
            // Initialize the year in the result array with 12 months (0-indexed)
            $formattedGrowthData[$year] = array_fill(0, 12, 0);  // Fill months (0-11) with 0s

            // Loop through the query result and map user growth data
            foreach ($userGrowthData as $data) {
                if ($data->year == $year) {
                    // Adjust for 0-indexed months (1 => 0, 2 => 1, ..., 12 => 11)
                    $formattedGrowthData[$year][$data->month - 1] = $data->user_count;
                }
            }
        }

        // Pass the data to the view or API response
        return inertia('Admin/Dashboard/AccountManagement/Summary', [
            'totalOfficials' => $totalOfficials,
            'totalNonOfficials' => $totalNonOfficials,
            'totalUsersWithBusiness' => $totalUsersWithBusiness,
            'userGrowthData' => $formattedGrowthData,  // Send formatted growth data
        ]);
    }

    // render officials
    public function officials(Request $request)
    {
        $searchInput = $request->input('searchInput');
        $filter_role = $request->input('filter_role');
        $officials = User::whereNot('role', 0)
            ->when($searchInput, function ($query, $search) {
                $query->where('username', 'LIKE', '%' . $search . '%')
                    ->orWhere('role', 'LIKE', '%' . $search . '%');
            })
            ->when($filter_role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->get();

        return Inertia::render('Admin/Dashboard/AccountManagement/Officials', ['officials' => $officials]);
    }

    // render users
    public function users(Request $request)
    {
        $searchInput = $request->input('searchInput');
        $filter_barangay = $request->input('filter_barangay');
        $regions = Region::get(['regCode', 'regDesc']);
        $users = User::with(['profile', 'profile.region', 'profile.province', 'profile.city', 'profile.barangay'])
            ->where('role', 0)
            ->when($searchInput, function ($query, $search) {
                $query->whereHas('profile', function ($query) use ($search) {
                    $query->when($search, function ($q, $search) {
                        $q
                            ->where('first_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('middle_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $search . '%');
                    });
                });
            })
            ->when($filter_barangay, function ($query, $barangay) {
                $query->whereHas('profile', function ($profileQuery) use ($barangay) {
                    $profileQuery->where('barangay', $barangay);
                });
            })
            ->get();

        // dd($users);

        return Inertia::render('Admin/Dashboard/AccountManagement/Users', ['users' => $users, 'regions' => $regions]);
    }

    public function addOfficial(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:4|unique:users,username',
            'role' => 'required|in:1,2,3,4',
            'password' => 'required|min:5'
        ]);

        try {
            User::create([
                'username' => $request->username,
                'role' => $request->role,
                'password' => Hash::make($request->role)
            ]);

            return redirect()->route('admin.officials')->with('success', 'User Added!');
        } catch (\Exception $e) {
            return redirect()->route('admin.officials')->with('error', 'Something Went Wrong!');
        }
    }

    public function updateOfficial(Request $request, $id)
    {
        if ($request->password) {
            $request->validate([
                'password' => 'required|min:5'
            ]);

            try {
                // dd($request->password);
                User::where('id', $id)->update(['password' => Hash::make($request->password)]);
                return redirect()->route('admin.officials')->with('success', 'Password Changed!');
            } catch (\Exception $e) {
                return redirect()->route('admin.officials')->with('error', 'Something went wrong!');
            }
        } else {
            $request->validate([
                'role' => 'required|in:1,2,3,4'
            ]);
            try {
                // dd($request->role);
                User::where('id', $id)->update(['role' => $request->role]);
                return redirect()->route('admin.officials')->with('success', 'Role Changed!');
            } catch (\Exception $e) {
                return redirect()->route('admin.officials')->with('error', 'Something went wrong!');
            }
        }
    }

    public function deleteOfficial($id)
    {
        try {
            User::where('id', $id)->delete();
            return redirect()->route('admin.officials')->with('success', 'User Deleted!');
        } catch (\Exception $e) {
            return redirect()->route('admin.officials')->with('error', 'Something Went Wrong!');
        }
    }

    public function editUser(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required',
            'sex' => 'required|in:Male,Female',
            'mobile_number' => 'required|string|max:12|unique:' . User::class . ',mobile_number,' . $id
        ]);

        if ($request->region != null || $request->province != null || $request->city != null || $request->barangay != null || $request->purok != null) {
            $request->validate([
                'province' => 'required',
                'city' => 'required',
                'barangay' => 'required',
                'purok' => 'required',
                'region' => 'required'
            ]);
        }

        try {
            User::where('id', $id)->update([
                'mobile_number' => $request->mobile_number,
            ]);

            Profile::where('user_id', $id)->update([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'sex' => $request->sex,
                'province' => $request->province,
                'city' => $request->city,
                'barangay' => $request->barangay,
                'purok' => $request->purok,
                'region' => $request->region,
            ]);

            return redirect()->route('admin.users')->with('success', 'User Information Updated!');
        } catch (\Excepption $e) {
            return redirect()->route('admin.users')->with('error', 'Something Went Wrong!');
        }
    }

    public function restrictUser($id, $status)
    {
        try {
            User::where('id', $id)->update(['status' => $status]);
            $message = $status == 0 ? 'User Unrestriction Successfull!' : 'User Restricted!';
            return redirect()->route('admin.users')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.users')->with('error', 'Something Went Wrong!');
        }
    }
}
