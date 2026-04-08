<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\UserTwoFactorCode;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Services\SmsService;
use App\Helpers\PhoneHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Illuminate\Cache\RateLimiter;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/my-account';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        $credentials['status'] = 'active';

        return $this->guard()->attempt(
            $credentials,
            $request->boolean('remember')
        );
    }

    protected function authenticated(Request $request, $user)
    {
        // Generate 2FA
        $user->generateTwoFactorCode();
        $user->notify(new UserTwoFactorCode());

        if ($user->mobile) {
            $defaultRegion = env('DEFAULT_REGION');
            $phoneInfo = PhoneHelper::detectCountry($user->mobile, $defaultRegion);

            if ($phoneInfo['valid']) {
                $name = ($user->first_name || $user->last_name)
                    ? trim($user->first_name . ' ' . $user->last_name)
                    : $user->email;

                $code = $user->two_factor_code;

                $message  = "Hello {$name},\n";
                $message .= "Your Xhale login verification code is: {$code}\n";
                $message .= "This code will expire in 10 minutes.";

                SmsService::sendSms($phoneInfo['formatted'], $message);
            }
        }

        // AJAX RESPONSE
        if ($request->ajax()) {
            return response()->json([
                'two_factor' => true,
                'redirect'   => route('verify.index'),
            ]);
        }

        // Non‑AJAX: let the trait handle redirect as usual
        return redirect()->intended($this->redirectPath());
    }

    // FAILED LOGIN FOR AJAX
    protected function sendFailedLoginResponse(Request $request)
    {
        if ($request->ajax()) {
            return response()->json([
                'errors' => [
                    'email' => ['These credentials do not match our records.'],
                ],
            ], 422);
        }

        return redirect()->back()
            ->withErrors([
                'email' => trans('auth.failed'),
            ]);
    }

    /**
     * LOCKOUT RESPONSE FOR RATE LIMIT (Too many attempts)
     */
    // protected function sendLockoutResponse(Request $request)
    // {
    //     $seconds = app(RateLimiter::class)->availableIn(
    //         $this->throttleKey($request)
    //     );

    //     $message = Lang::get('auth.throttle', [
    //         'seconds' => $seconds,
    //         'minutes' => ceil($seconds / 60),
    //     ]);

    //     // AJAX: return JSON 429 so your JS can show it in #loginError
    //     if ($request->ajax()) {
    //         return response()->json([
    //             'message' => $message,
    //         ], 429);
    //     }

    //     // Normal form: redirect back with error
    //     return redirect()->back()
    //         ->withErrors(['email' => $message]);
    // }


     /**
     * LOCKOUT RESPONSE FOR RATE LIMIT (Too many attempts)
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = app(RateLimiter::class)->availableIn(
            $this->throttleKey($request)
        );

        // Customize lockout message to reflect 5-minute lockout
        $message = Lang::get('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]);

        // AJAX: return JSON 429 so your JS can show it in #loginError
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
            ], 429);
        }

        // Normal form: redirect back with error
        return redirect()->back()
            ->withErrors(['email' => $message]);
    }
}
