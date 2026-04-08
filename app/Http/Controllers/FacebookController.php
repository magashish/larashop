<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 

class FacebookController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleFacebookCallback(Request $request)
    {
        if ($request->has('error')) {
            // You can log the error for debugging purposes if needed.
            // \Log::error('Facebook login was cancelled by the user.', ['error' => $request->error]);

            // Redirect the user to your main login page or another appropriate page.
            // Make sure you have a named 'login' route in your routes/web.php file.
            return redirect()->route('login')->with('message', 'Facebook login was cancelled.');
        }

        try {
            $user = Socialite::driver('facebook')->user();
            $findUser = User::where('facebook_id', $user->id)->first();
            if($findUser){
                Auth::login($findUser);
                return redirect()->route('subscription_package')->with('status', 'Your Facebook account has been successfully login!');
            }else {
                $existingUserByEmail = User::where('email', $user->email)->first();
                if ($existingUserByEmail) {
                    $existingUserByEmail->facebook_id = $user->id;
                    if (is_null($existingUserByEmail->email_verified_at)) {
                        $existingUserByEmail->email_verified_at = Carbon::now();
                    }
                    $existingUserByEmail->save();
                    Auth::login($existingUserByEmail);
                    return redirect()->route('subscription_package')->with('status', 'Your Facebook account has been successfully linked!');
                } else {
                    $newUser = User::create([
                        'name' => $user->name,
                        'email' => $user->email,
                        'facebook_id' => $user->id,
                        'password' => bcrypt(generateStrongPassword(12)),
                        'email_verified_at' => Carbon::now(), 
                    ]);
                    Auth::login($newUser);
                    return redirect()->route('subscription_package')->with('status', 'Your Facebook account has been successfully Created!');
                }
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}