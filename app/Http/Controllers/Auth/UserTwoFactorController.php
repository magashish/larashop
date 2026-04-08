<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\UserTwoFactorCode;
use Illuminate\Http\Request;

use App\Services\SmsService;
use App\Helpers\PhoneHelper;

class UserTwoFactorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa.user']);
    }

    // public function index() 
    // {
    //     return view('auth.usertwofactor');
    // }

    public function index()
    {
        $user = auth()->user();
    // If user is not logged in, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

    // If user does NOT have a two_factor_code, redirect to dashboard
        if (!$user->two_factor_code) {
        // Replace with your dashboard route
            return redirect()->route('subscriber.dashboard.index');
        }

    // Otherwise, show the 2FA page
        return view('auth.usertwofactor');
    }


    public function store(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'integer|required',
        ]);
        $user = auth()->user();
        if($request->input('two_factor_code') == $user->two_factor_code)
        {
            $user->resetTwoFactorCode();
            if ($user->level == 'member') {
                return redirect()->route('subscriber.dashboard.index');
            }

            return redirect()->route('business.myaccount.index');
        }
        return redirect()->back()->withErrors(['two_factor_code' => 'The two factor code you have entered does not match']);
    }

    public function resend()
    {
        $user = auth()->user();
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



        return redirect()->back()->withMessage('The two factor code has been sent again');
    }
}