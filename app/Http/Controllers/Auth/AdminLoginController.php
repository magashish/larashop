<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException; // Import for throwing validation exceptions
use App\Notifications\AdminTwoFactorCode;


class AdminLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating admin users.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect admin users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard'; // Set your admin dashboard route

    /**
     * Create a new controller instance.
     * Use 'guest:admin' to ensure only unauthenticated admins can access login.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    /**
     * Show the admin login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin'); // Specify the 'admin' guard
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {

        // First, attempt to log in using the default AuthenticatesUsers trait logic.
        // This validates credentials against the database.
        if ($this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        )) {
            // If credentials are correct, get the authenticated admin user.
            $admin = $this->guard()->user();

            // Now, check the 'status' of the authenticated admin.
            // Assuming 'status' column exists in your 'admins' table and 'active' is the value for active users.
            if ($admin->status !== 'active') {
                // If the admin is not active, log them out immediately.
                $this->guard()->logout();

                // Throw a validation exception to show an error message.
                // This will redirect back to the login form with an error.
                throw ValidationException::withMessages([
                    $this->username() => [trans('auth.inactive_admin')], // Use a translatable message
                ]);
            }

            // If the admin is active, return true to complete the login process.
            return true;
        }

        // If credentials were not correct initially, return false.
        return false;
    }

    /**
     * Log the admin out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 204)
            : redirect('/admin/login'); // Redirect to admin login after logout
    }




    protected function authenticated(Request $request, $admin)
    {
        $admin->generateTwoFactorCode();
        $admin->notify(new AdminTwoFactorCode());
    }


    // You can override other methods from AuthenticatesUsers trait if needed,
    // for example, to customize validation messages or redirect paths.
    // protected function credentials(Request $request)
    // {
    //      return $request->only($this->username(), 'password');
    // }

    // protected function sendLoginResponse(Request $request) { ... }
    // protected function sendFailedLoginResponse(Request $request) { ... }
}
