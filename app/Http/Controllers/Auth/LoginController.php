<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Get the post-authentication redirect path.
     */
    public function redirectTo()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Update last login timestamp
        $user->last_login_at = now();
        $user->save();
        
        // Redirect based on user role
        if ($user->hasRole('Bus Pass Subject Clerk (Branch)')) {
            return '/dashboard/branch/clerk';
        } elseif ($user->hasRole('Staff Officer (Branch)')) {
            return '/dashboard/branch/staff-officer';
        } elseif ($user->hasRole('Director (Branch)')) {
            return '/dashboard/branch/director';
        } elseif ($user->hasRole('Subject Clerk (DMOV)')) {
            return '/dashboard/movement/clerk';
        } elseif ($user->hasRole('Staff Officer 2 (DMOV)')) {
            return '/dashboard/movement/staff-officer-2';
        } elseif ($user->hasRole('Staff Officer 1 (DMOV)')) {
            return '/dashboard/movement/staff-officer-1';
        } elseif ($user->hasRole('Col Mov (DMOV)')) {
            return '/dashboard/movement/col-mov';
        } elseif ($user->hasRole('Director (DMOV)')) {
            return '/dashboard/movement/director';
        } elseif ($user->hasRole('Bus Escort (DMOV)')) {
            return '/dashboard/movement/escort';
        }
        
        return '/dashboard';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
