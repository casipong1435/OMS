<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Models\Area;
use App\Models\Establishment;
use App\Models\EstablishmentUnit;
use App\Models\Business;
use App\Models\User;
use App\Models\Ordinance;


class GuestController extends Controller
{

    

    public function welcome() {

        $featured_areas = Area::latest()->take(3)->get();
        //dd($featured_areas);
        $area_count = Area::count();
        return Inertia::render('GuestPage/Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'featured_areas' => $featured_areas,
            'area_count' => $area_count
        ]);
    }

    public function about() {
        return Inertia::render('GuestPage/About');
    }

    public function services() {
        $areas = Area::get();
        //dd($areas_in_map);
        return Inertia::render('GuestPage/Services', ['areas' => $areas]);
    }

    public function establishment($id){
        $name = Area::find($id)->name;
        $establishments = Establishment::with(['establishment_units', 'establishment_units.establishment_images'])->where('area_id', $id)->get();
        return Inertia::render('GuestPage/Establishment', ['establishments' => $establishments, 'name' => $name,]);
    }

    public function establishment_unit($id){
        //dd(auth()->user()->profile->id);
        $establishment_units = EstablishmentUnit::with('establishment_images')->where('establishment_id', $id)->get();
        $establishment_info = Establishment::with('area')->where('id', $id)->first();
        //dd($establishment_info);
        if(auth()->check()){
            if(auth()->user()->profile){
                $inBusiness = Business::where('profile_id', auth()->user()->profile->id)->whereIn('status', [0,1,2])->first();
            }
        }else{
            $inBusiness = null;
        }
        return Inertia::render('GuestPage/Stall', ['establishment_units' => $establishment_units, 'establishment_info' => $establishment_info, 'inBusiness' => $inBusiness]);
        
    }

    public function ordinance(){
        
        $ordinances = Ordinance::get();
        return Inertia::render('GuestPage/Ordinance', compact('ordinances'));
        
    }

    

    public function createNewPassword(){

    }

    

    
}
