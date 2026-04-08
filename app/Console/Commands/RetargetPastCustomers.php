<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserPackageSubscription;
use App\Models\User;
use App\Models\PrizeDraw;
use Carbon\Carbon;
use App\Services\SmsService;
use App\Helpers\PhoneHelper;
use Exception;

class RetargetPastCustomers extends Command
{
    protected $signature = 'app:retarget-past-customers';
    protected $description = 'Sends a reminder to users who previously had a package but are currently inactive.';

    public function handle()
    {
        $this->info('Starting retargeting command...');

        $timezone = env('APP_TIMEZONE', 'Australia/Sydney');

        $now   = Carbon::now($timezone);
        $start = $now->copy()->addHours(24)->subMinutes(2);
        $end   = $now->copy()->addHours(24)->addMinutes(2);

        $this->info("🕐 Timezone    : " . $timezone);
        $this->info("🕐 NOW         : " . $now->toDateTimeString());
        $this->info("🕐 Start       : " . $start->toDateTimeString());
        $this->info("🕐 End         : " . $end->toDateTimeString());

        // ✅ Atomic lock — claim draws before fetching to prevent duplicate sends
        $updated = PrizeDraw::where('winner_user_finalised', 'no')
            ->where('retarget_past_customers', 'no')
            ->whereBetween('draw_date', [$start, $end])
            ->update(['retarget_past_customers' => 'yes']);

        if ($updated === 0) {
            $this->info('No draws to process right now.');
            return;
        }

        $this->info("🔒 Claimed {$updated} draw(s) for processing.");

        // Fetch the draws we just claimed
        $draws = PrizeDraw::where('winner_user_finalised', 'no')
            ->where('retarget_past_customers', 'yes')
            ->whereBetween('draw_date', [$start, $end])
            ->get();

        if ($draws->isEmpty()) {
            $this->info('No draws found after claiming. Exiting.');
            return;
        }

        // Get lapsed users — had subscription/package before but not active now
        $pastUserIds = UserPackageSubscription::whereIn('status', ['canceled', 'expired'])
            ->pluck('user_id')
            ->unique();

        $activeUserIds = UserPackageSubscription::where('status', 'active')
            ->pluck('user_id')
            ->unique();

        $lapsedUserIds = $pastUserIds->diff($activeUserIds);

        if ($lapsedUserIds->isEmpty()) {
            $this->info('No lapsed users to retarget.');
            return;
        }

        $lapsedUsers = User::whereIn('id', $lapsedUserIds)->get();

        $this->info('Found ' . $lapsedUsers->count() . ' lapsed users to retarget.');

        $link = env('APP_URL');

        foreach ($draws as $draw) {

            $this->line('');
            $this->line('--- Processing Draw: ' . $draw->title . ' ---');
            $this->line('Draw Date: ' . $draw->draw_date);

            // ✅ Build prize amount once per draw
            if ($draw->prize_type === 'cash') {
                $prizeAmount = '$' . number_format($draw->cash_amount, 2);
            } elseif ($draw->prize_type === 'non_cash') {
                $prizeAmount = $draw->prize_sub_title ?? '';
            } else {
                $prizeAmount = '';
            }

            $successCount = 0;
            $failCount    = 0;

            foreach ($lapsedUsers as $user) {

                if ($user->sms_unsubscribed) {
                    $this->warn("⚠️ User {$user->email} has unsubscribed from SMS. Skipping.");
                    continue;
                }

                try {
                    $rawPhone      = $user->mobile;
                    $defaultRegion = env('DEFAULT_REGION');
                    $phoneInfo     = \App\Helpers\PhoneHelper::detectCountry($rawPhone, $defaultRegion);

                    if (!$phoneInfo['valid']) {
                        $this->error("❌ Invalid phone for {$user->email}: {$rawPhone}");
                        $failCount++;
                        continue;
                    }

                    $phoneNumber = $phoneInfo['formatted'];
                    $country     = $phoneInfo['country'];
                    $unsubscribeLink = route('unsubscribe.sms', ['token' => $user->id]);

                    $message = "🔥 Don't sit this one out, {$user->name}!\n\n"
                        . "Tomorrow's {$draw->title} {$prizeAmount} draw is a big one!\n\n"
                        . "Reactivate your package or subscription today to get back in the game 👉 {$link}\n\n"
                        . "🔕 To unsubscribe from SMS, click here: {$unsubscribeLink}";

                    $response = \App\Services\SmsService::sendSms($phoneNumber, $message);

                    if ($response['status'] === 'success') {
                        $this->info("✅ SMS sent to {$user->email} → {$phoneNumber} ({$country})");
                        $successCount++;
                    } else {
                        $this->error("⚠️ SMS failed for {$user->email} → {$phoneNumber}: " . ($response['error'] ?? 'Unknown error'));
                        $failCount++;
                    }

                } catch (Exception $e) {
                    $this->error("💥 Exception for {$user->email}: " . $e->getMessage());
                    $failCount++;
                }
            }

            $this->line('');
            $this->info("📊 Draw '{$draw->title}' complete — ✅ {$successCount} sent, ❌ {$failCount} failed.");
        }

        $this->line('');
        $this->info('✅ Retargeting command finished.');
    }
}