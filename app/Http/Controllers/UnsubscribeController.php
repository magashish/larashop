<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\UnregisteredUser;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UnsubscribeController extends Controller
{
    public function sms(Request $request, string $token)
    {
        Log::info('🔔 Unsubscribe route hit!', [
            'token'      => $token,
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
            'time'       => now(),
        ]);

        // ✅ Bot detect karo — real mobile users ka UA android/iphone hota hai
        $userAgent   = strtolower($request->userAgent() ?? '');
        $botKeywords = ['googlebot', 'bot', 'crawler', 'spider', 'preview', 
                        'fetch', 'scan', 'linux x86_64', 'x11'];

        foreach ($botKeywords as $keyword) {
            if (str_contains($userAgent, $keyword)) {
                Log::warning("🤖 Bot detected, skipping unsubscribe", [
                    'ip'         => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                return response('OK', 200); // Bot ko 200 do, kuch mat karo
            }
        }

        try {
            // ✅ UnregisteredUser check
            $user = UnregisteredUser::find($token);
            if ($user) {
                $user->sms_unsubscribed    = true;
                $user->sms_unsubscribed_at = now();
                $user->save();

                return view('unsubscribe.success', [
                    'message' => 'You have successfully unsubscribed from SMS notifications.'
                ]);
            }

            // ✅ Registered user check
            $user = User::find($token);
            if ($user) {
                $user->sms_unsubscribed    = true;
                $user->sms_unsubscribed_at = now();
                $user->save();

                return view('unsubscribe.success', [
                    'message' => 'You have successfully unsubscribed from SMS notifications.'
                ]);
            }

            return view('unsubscribe.error', [
                'message' => 'Invalid or expired unsubscribe link.'
            ]);

        } catch (\Exception $e) {
            Log::error('SMS Unsubscribe error', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);

            return view('unsubscribe.error', [
                'message' => 'Something went wrong. Unable to unsubscribe.'
            ]);
        }
    }
}
