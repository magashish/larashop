<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UnregisteredUser;
use App\Models\User;
use App\Helpers\PhoneHelper;
use App\Services\SmsService;
use Carbon\Carbon;
use Exception;

class Test_ProcessUnregisteredUserFollowUps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test_process-unregistered-user-follow-ups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Processing unregistered user follow-ups...');

        $timezone = env('APP_TIMEZONE', 'Australia/Sydney');

        // ✅ Remove users who completed registration
        UnregisteredUser::whereIn('email', User::pluck('email'))->delete();

        // ✅ Users ready for follow-up
        $email = 'developer379922345@gmail.com';
        $users = UnregisteredUser::where('email', $email)
        ->whereNotNull('next_followup_at')
        ->where('next_followup_at', '<=', Carbon::now($timezone))
        ->where('followup_step', '<=', 3)
        ->where('sms_unsubscribed', false) 
        ->get();

        if ($users->isEmpty()) {
            $this->info('✅ No users pending follow-ups.');
            return;
        }

        $this->info('Found ' . $users->count() . ' users to follow up.');

        $successCount = 0;
        $failCount    = 0;

        foreach ($users as $user) {

            // ✅ Skip entirely — don't update step for unsubscribed users
            if ($user->sms_unsubscribed) {
                $this->warn("⚠️ User {$user->email} has unsubscribed from SMS. Skipping.");
                continue;
            }

            $step = (int) $user->followup_step;

            // Send SMS
            $result = $this->sendFollowupSms($user, $step);

            if ($result) {
                $successCount++;
            } else {
                $failCount++;
            }

            // ✅ Update step regardless of SMS success
            // So failed SMS users don't get stuck in a loop
            $nextStep = $step + 1;

            UnregisteredUser::where('id', (string) $user->id)->update([
                'followup_step'    => $nextStep,
                'next_followup_at' => $this->nextTime($nextStep, $timezone),
            ]);

            // $user->update([
            //     'followup_step'    => $nextStep,
            //     'next_followup_at' => $this->nextTime($nextStep, $timezone),
            // ]);
        }

        $this->line('');
        $this->info("📊 Complete — ✅ {$successCount} sent, ❌ {$failCount} failed.");
        $this->info('🎉 Follow-up processing completed.');
    }

    /**
     * ⏱ Next follow-up time
     */
    private function nextTime(int $step, string $timezone): ?Carbon
    {
        // ✅ Use timezone from .env
        return match ($step) {
            2 => Carbon::now($timezone)->addMinutes(2),
            3 => Carbon::now($timezone)->addMinutes(2),
            default => null,
        };
    }

    /**
     * 📲 Send SMS
     */
    private function sendFollowupSms(UnregisteredUser $user, int $step): bool
    {

      

        if (!$user->mobile) {
            $this->warn("⚠️ No mobile number for {$user->email}. Skipping SMS.");
            return false;
        }

        // ✅ Only steps 1-3 are valid
        if (!in_array($step, [1, 2, 3])) {
            $this->warn("⚠️ Invalid step {$step} for {$user->email}. Skipping.");
            return false;
        }

        try {
            $rawPhone      = $user->mobile;
            $defaultRegion ='IN';
            $phoneInfo     = PhoneHelper::detectCountry($rawPhone, $defaultRegion);

            if (!$phoneInfo['valid']) {
                $this->error("❌ Invalid phone for {$user->email}: {$rawPhone}");
                return false;
            }

            $phoneNumber = $phoneInfo['formatted'];
            $country     = strtoupper($phoneInfo['country']);
            $name        = $user->name;
            $signupLink  = route('register');

            $unsubscribeToken = $user->id;
            $unsubscribeLink  = route('unsubscribe.sms', ['token' => $unsubscribeToken]);

            // $messages = [
            //     1 => "Hey {$name}, you were almost there 👀\n"
            //     . "Your Xhale signup is nearly complete. Finish your entry now for a chance to win cash prizes and unlock exclusive Aussie business discounts.\n\n"
            //     . "👉 Complete signup: {$signupLink}",

            //     2 => "Hey {$name}, quick reminder — completing your Xhale signup gives you entries into major prize giveaways 💸 plus access to exclusive discounts from Aussie businesses.\n\n"
            //     . "Finish here before you miss out 👉 {$signupLink}",

            //     3 => "Last reminder {$name} 👋\n"
            //     . "Your Xhale signup is still waiting. Complete it now to lock in your giveaway entries and member benefits.\n\n"
            //     . "Finalise here 👉 {$signupLink}",
            // ];

                $messages = [
                    1 => "Hey {$name}, you were almost there 👀\n" .
                    "Your Xhale signup is nearly complete. Finish your entry now for a chance to win cash prizes and unlock exclusive Aussie business discounts.\n\n" .
                    "👉 Complete signup: {$signupLink}\n\n" .
                    "🔕 To unsubscribe from SMS, click here: " . route('unsubscribe.sms', ['token' => $user->id]),

                    2 => "Hey {$name}, quick reminder — completing your Xhale signup gives you entries into major prize giveaways 💸 plus access to exclusive discounts from Aussie businesses.\n\n" .
                    "Finish here before you miss out 👉 {$signupLink}\n\n" .
                    "🔕 To unsubscribe from SMS, click here: " . route('unsubscribe.sms', ['token' => $user->id]),

                    3 => "Last reminder {$name} 👋\n" .
                    "Your Xhale signup is still waiting. Complete it now to lock in your giveaway entries and member benefits.\n\n" .
                    "Finalise here 👉 {$signupLink}\n\n" .
                    "🔕 To unsubscribe from SMS, click here: " . route('unsubscribe.sms', ['token' => $user->id]),
                ];

            $message  = $messages[$step];
            $response = SmsService::sendSms($phoneNumber, $message);

            if (isset($response['status']) && $response['status'] === 'success') {
                $this->info("✅ SMS sent to {$user->email} → {$phoneNumber} ({$country}) | Step {$step}");
                return true;
            } else {
                $this->error("⚠️ SMS failed for {$user->email} → {$phoneNumber}: " . ($response['error'] ?? 'Unknown error'));
                return false;
            }

        } catch (Exception $e) {
            $this->error("💥 SMS exception for {$user->email}: " . $e->getMessage());
            logger()->error('Followup SMS exception', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
    }
}