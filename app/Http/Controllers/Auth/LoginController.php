<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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

        // For now, redirect all users to the main dashboard
        // TODO: Implement role-specific dashboard routes when needed
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

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'e_no';
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $login_input = $request->input('e_no'); // This field now accepts both E No and email
        $password = $request->input('password');

        // First, try to find user by E No
        $user = \App\Models\User::where('e_no', $login_input)->where('is_active', true)->first();

        // If not found by E No, try to find by email (for System Administrator)
        if (!$user) {
            $user = \App\Models\User::where('email', $login_input)->where('is_active', true)->first();
        }

        if (!$user) {
            return false; // User not found
        }

        // Check if user is System Administrator - use traditional password authentication
        if ($user->hasRole('System Administrator (DMOV)')) {
            // Traditional Laravel authentication for System Administrator
            $credentials = [
                'email' => $user->email,
                'password' => $password,
                'is_active' => true
            ];

            return Auth::attempt($credentials, $request->filled('remember'));
        }

        // For other users, use API authentication
        try {
            $authenticated = $this->authenticateWithAPI($user->e_no, $password);

            if ($authenticated) {
                // Log the user in without password verification
                Auth::login($user, $request->filled('remember'));
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('API Authentication failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Authenticate user with external API
     *
     * @param string $e_no
     * @param string $password
     * @return bool
     */
    protected function authenticateWithAPI($e_no, $password)
    {
        // TODO: Replace with actual API endpoint
        // For now, return true for testing purposes
        // In production, this should call the actual authentication API

        try {
            // Example API call structure:
            /*
            $response = Http::post('https://api.army.lk/auth', [
                'e_no' => $e_no,
                'password' => $password
            ]);

            return $response->successful() && $response->json('authenticated') === true;
            */

            // Temporary: Accept any non-empty password for testing
            return !empty($password);
        } catch (\Exception $e) {
            Log::error('API authentication error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        // This method is not used anymore since we override attemptLogin
        return [];
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $login_input = $request->input('e_no');

        // Check if user exists by E No first
        $user = \App\Models\User::where('e_no', $login_input)->first();

        // If not found by E No, try by email (for System Administrator)
        if (!$user) {
            $user = \App\Models\User::where('email', $login_input)->first();
        }

        if (!$user) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'e_no' => ['User not found. Please check your E No or email.'],
            ]);
        }

        if (!$user->is_active) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'e_no' => ['Your account has been deactivated. Please contact the administrator.'],
            ]);
        }

        // If user exists and is active, authentication failed
        if ($user->hasRole('System Administrator (DMOV)')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'password' => ['Invalid password. Please check your login credentials.'],
            ]);
        } else {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'password' => ['API authentication failed. Please check your password.'],
            ]);
        }
    }
}
