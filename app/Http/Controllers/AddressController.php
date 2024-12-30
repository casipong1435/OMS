<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function getProvinces($regionCode)
    {
        return Province::where('regCode', $regionCode)->get();
    }

    public function getCities($provinceCode)
    {
        return City::where('provCode', $provinceCode)->get();
    }

    public function getBarangays($cityCode)
    {
        return Barangay::where('citymunCode', $cityCode)->get();
    }
}
