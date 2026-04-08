<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password; // ✅ Add this

use App\Services\SmsService;
use App\Helpers\PhoneHelper;
use App\Notifications\UserResetPasswordNotification;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Send password reset link via email and SMS
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return $this->sendResetLinkFailedResponse($request, 'User not found.');
        }

        // 1️⃣ Generate a plain token
        $plainToken = Str::random(64);

        // 2️⃣ Store hashed token in password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Hash::make($plainToken),
                'created_at' => now(),
            ]
        );

        // 3️⃣ Build reset URL using the plain token
        $resetUrl = url(route('password.reset', [
            'token' => $plainToken,
            'email' => $user->email,
        ], false));

        // 4️⃣ Send Laravel default style email manually with plain token
        // Mail::raw("Reset your password using this link: $resetUrl", function($message) use ($user) {
        //     $message->to($user->email)
        //             ->subject('Reset Your Password');
        // });

        //$status = Password::sendResetLink($request->only('email'));
        $user->notify(new UserResetPasswordNotification($plainToken));


        // 5️⃣ Send SMS with the same reset link
        if ($user->mobile) {
            $defaultRegion = env('DEFAULT_REGION');
            $phoneInfo = PhoneHelper::detectCountry($user->mobile, $defaultRegion);

            if ($phoneInfo['valid']) {

                $name = ($user->first_name || $user->last_name)
               ? trim($user->first_name . ' ' . $user->last_name)
               : $user->email;

               $message = "Hello {$name},\n";
               $message .= "You requested a password reset for your Xhale account.\n";
               $message .= "Reset here: {$resetUrl}\n";
               $message .= "If you did not request this, ignore.";


                SmsService::sendSms($phoneInfo['formatted'], $message);
            }
        }

        return back()->with('status', 'Password reset link sent via email and SMS if phone exists.');
    }

    /**
     * Failed response
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return back()->withInput()->withErrors([
            'email' => 'Unable to send reset link. Please try again.'
        ]);
    }
}
