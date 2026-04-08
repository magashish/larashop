<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 


class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
        
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $finduser = User::where('google_id', $user->id)->first();
            if($finduser){
                Auth::login($finduser);
                // Set a session flag to bypass 2FA for this request
                session()->put('social_login_completed', true);
                return redirect()->route('subscription_package')->with('status', 'Your Google account has been successfully login!');
            }else {
                $existingUserByEmail = User::where('email', $user->email)->first();
                if ($existingUserByEmail) {
                    $existingUserByEmail->google_id = $user->id;
                    if (is_null($existingUserByEmail->email_verified_at)) {
                        $existingUserByEmail->email_verified_at = Carbon::now();
                    }
                    $existingUserByEmail->save();
                    Auth::login($existingUserByEmail);
                    session()->put('social_login_completed', true);
                    return redirect()->route('subscription_package')->with('status', 'Your Google account has been successfully linked!');
                } else {
                    $newUser = User::create([
                        'name' => $user->name,
                        'email' => $user->email,
                        'google_id' => $user->id,
                        'password' => bcrypt(generateStrongPassword(12)),
                        'email_verified_at' => Carbon::now(), 
                    ]);
                    Auth::login($newUser);
                     session()->put('social_login_completed', true);
                    return redirect()->route('subscription_package')->with('status', 'Your Google account has been successfully Created!');
                }
            }
            
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}