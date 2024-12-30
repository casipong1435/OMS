<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\OfficialLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {

        if (auth()->check() && auth()->user()->role == 'user'){
            return redirect()->intended(route('user.dashboard', absolute: false));
        }else{
            return Inertia::render('Auth/Login', [
                'canResetPassword' => Route::has('password.request'),
                'status' => session('status'),
            ]);
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('user.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $role = Auth::user()->role;
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $role == 'user' ? redirect()->intended(route('welcome', absolute: false)) : redirect()->intended(route('official.auth', absolute: false));
         
    }


    public function create_official(): Response
    {
        return Inertia::render('Admin/Auth', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store_official(OfficialLoginRequest $request): RedirectResponse
    {
    
        $request->authenticate();

        $request->session()->regenerate();

        switch(auth()->user()->role){
            case 'admin':
                return redirect()->intended(route('admin.dashboard', absolute: false));
                break;
            case 'ceedo':
                return redirect()->intended(route('ceedo.dashboard', absolute: false));
                break;
            case 'treasurer':
                return redirect()->intended(route('treasurer.dashboard', absolute: false));
                break;
            case 'mayor':
                return redirect()->intended(route('mayor.dashboard', absolute: false));
                break;
        }
    }
}
