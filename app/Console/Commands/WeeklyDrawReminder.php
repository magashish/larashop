<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\PrizeDraw;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\SmsService;
use App\Helpers\PhoneHelper;
use Exception;

class WeeklyDrawReminder extends Command
{
    protected $signature = 'app:weekly-draw-reminder';
    protected $description = 'Sends SMS reminders to active subscribers 24 hours before a draw.';

    public function handle()
    {
        $this->info('Starting weekly draw reminder command...');

        $timezone = env('APP_TIMEZONE', 'Australia/Sydney');
        $now = Carbon::now($timezone);

    // kal ki date
        $targetDate = $now->copy()->addDay()->toDateString();

        $this->info("🕐 Timezone    : " . $timezone);
        $this->info("🕐 NOW         : " . $now->toDateTimeString());
        $this->info("🕐 Target Date : " . $targetDate);

    // Atomic lock — saare kal ke draws ek saath claim karo
        $updated = PrizeDraw::where('winner_user_finalised', 'no')
        ->where('weekly_draw_reminder', 'no')
        ->whereDate('draw_date', $targetDate)
        ->update(['weekly_draw_reminder' => 'yes']);

        if ($updated === 0) {
            $this->info('No draws to process right now.');
            return;
        }

        $this->info("🔒 Claimed {$updated} draw(s) for processing.");

    // Claimed draws fetch karo
        $draws = PrizeDraw::where('winner_user_finalised', 'no')
        ->where('weekly_draw_reminder', 'yes')
        ->whereDate('draw_date', $targetDate)
        ->get();

        if ($draws->isEmpty()) {
            $this->info('No draws found after claiming. Exiting.');
            return;
        }

    // Audience build karo
        $subscribersOnly = DB::table('user_package_subscriptions as ups')
        ->join('users as u', 'u.id', '=', 'ups.user_id')
        ->where('u.exclude_from_prize_draw', false)
        ->groupBy('ups.user_id')
        ->havingRaw('SUM(ups.type = "subscription" AND ups.status = "active") > 0')
        ->havingRaw('SUM(ups.type = "package" AND ups.status = "active") = 0')
        ->pluck('ups.user_id')
        ->toArray();
        $packagesOnly = DB::table('user_package_subscriptions as ups')
        ->join('users as u', 'u.id', '=', 'ups.user_id')
        ->where('u.exclude_from_prize_draw', false)
        ->groupBy('ups.user_id')
        ->havingRaw('SUM(ups.type = "package" AND ups.status = "active") > 0')
        ->havingRaw('SUM(ups.type = "subscription" AND ups.status = "active") = 0')
        ->pluck('ups.user_id')
        ->toArray();

        $uniqueUserIds = array_unique(array_merge($subscribersOnly, $packagesOnly));
        $audience = User::whereIn('id', $uniqueUserIds)->get();


        // $audience = User::where('exclude_from_prize_draw', false)
        // ->whereHas('userPackageSubscriptions', function ($query) {
        //     $query->where('status', 'active');
        // })
        // ->get();

        if ($audience->isEmpty()) {
            $this->info('No audience found. Skipping.');
            return;
        }

        $this->info('Found ' . $audience->count() . ' users to notify.');

        $successCount = 0;
        $failCount    = 0;

        foreach ($audience as $user) {

            if (!is_object($user)) {
                $this->warn("⚠️ Invalid user object found, skipping.");
                $failCount++;
                continue;
            }

            if (empty($user->mobile)) {
                $this->warn("⚠️ No phone for {$user->email}. Skipping.");
                $failCount++;
                continue;
            }

            if (!empty($user->sms_unsubscribed)) {
                $this->warn("⚠️ {$user->email} unsubscribed. Skipping.");
                continue;
            }

            try {


                $rawPhone    = $user->mobile;
                $phoneInfo   = \App\Helpers\PhoneHelper::detectCountry($rawPhone, env('DEFAULT_REGION'));

                if (!$phoneInfo['valid']) {
                    $this->error("❌ Invalid phone for {$user->email}");
                    $failCount++;
                    continue;
                }

                $unsubscribeLink = route('unsubscribe.sms', ['token' => $user->id]);

                $message = "🎉 Hi {$user->name},\n\n"
                . "Xhale Draw Tomorrow! We have exciting giveaways happening on {$targetDate}.\n\n"
                . "Make sure you're in it to win it! Good luck! 🍀 – Xhale Team\n\n"
                . "🔕 To unsubscribe from SMS, click here: {$unsubscribeLink}";

                $response = \App\Services\SmsService::sendSms($phoneInfo['formatted'], $message);

                if ($response['status'] === 'success') {
                    $this->info("✅ SMS sent to {$user->email}");
                    $successCount++;
                } else {
                    $this->error("❌ Failed for {$user->email}: " . ($response['error'] ?? 'Unknown'));
                    $failCount++;
                }

            } catch (Exception $e) {
                $this->error("💥 Exception for {$user->email}: " . $e->getMessage());
                $failCount++;
            }
        }

        $this->info("📊 Complete — ✅ {$successCount} sent, ❌ {$failCount} failed.");
        $this->info('✅ Weekly draw reminder command finished.');
    }
}