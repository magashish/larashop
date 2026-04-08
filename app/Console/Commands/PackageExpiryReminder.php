<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserPackageSubscription;
use Carbon\Carbon;
use App\Services\SmsService;
use App\Helpers\PhoneHelper;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Exception;

class PackageExpiryReminder extends Command
{
    protected $signature = 'app:package-expiry-reminder';
    protected $description = 'Sends a reminder to users whose packages are about to expire.';

    public function handle()
    {
        $this->info('Starting package expiry reminder command...');

        // ✅ Timezone from .env — DB mein Sydney time store hai
        $timezone = env('APP_TIMEZONE', 'Australia/Sydney');

        $now   = Carbon::now($timezone);
        $start = $now->copy()->addHours(24)->subMinutes(2);
        $end   = $now->copy()->addHours(24)->addMinutes(2);

        $this->info("🕐 Timezone : " . $timezone);
        $this->info("🕐 NOW      : " . $now->toDateTimeString());
        $this->info("🕐 Start    : " . $start->toDateTimeString());
        $this->info("🕐 End      : " . $end->toDateTimeString());

        // // ✅ Atomic lock — update karo pehle, phir fetch karo
        // // Prevents duplicate SMS when scheduler runs every 5 minutes
        // $updated = UserPackageSubscription::query()
        //     ->where('type', 'package')
        //     ->where('status', 'active')
        //     ->where('package_expiry_reminder', 'no')
        //     ->whereBetween('ends_at', [$start, $end])
        //     ->whereNotExists(function ($query) {
        //         $query->select(DB::raw(1))
        //             ->from('user_package_subscriptions as ups2')
        //             ->whereColumn('ups2.user_id', 'user_package_subscriptions.user_id')
        //             ->whereColumn('ups2.id', '!=', 'user_package_subscriptions.id')
        //             ->where('ups2.status', 'active');
        //     })
        //     ->update(['package_expiry_reminder' => 'yes']);

        // if ($updated === 0) {
        //     $this->info('No packages to process right now.');
        //     return;
        // }

        // $this->info("🔒 Claimed {$updated} subscription(s) for processing.");

        // // Fetch the subscriptions we just claimed
        // $subscriptions = UserPackageSubscription::query()
        //     ->where('type', 'package')
        //     ->where('status', 'active')
        //     ->where('package_expiry_reminder', 'yes')
        //     ->whereBetween('ends_at', [$start, $end])
        //     ->whereNotExists(function ($query) {
        //         $query->select(DB::raw(1))
        //             ->from('user_package_subscriptions as ups2')
        //             ->whereColumn('ups2.user_id', 'user_package_subscriptions.user_id')
        //             ->whereColumn('ups2.id', '!=', 'user_package_subscriptions.id')
        //             ->where('ups2.status', 'active');
        //     })
        //     ->with('user')
        //     ->get();

        $subscriptions = UserPackageSubscription::query()
        ->where('id', '74a2e0f1-416f-4617-8d71-69aa3f6bc107')
        ->get();

        // echo "<pre>";
        // print_r($subscriptions);
        // exit();

        if ($subscriptions->isEmpty()) {
            $this->info('No subscriptions found after claiming. Exiting.');
            return;
        }

        $this->info('Found ' . $subscriptions->count() . ' packages to send a reminder for.');

        $link          = env('APP_URL');
        $successCount  = 0;
        $failCount     = 0;

        foreach ($subscriptions as $subscription) {

            $user = $subscription->user;

            if (!$user) {
                $this->warn("⚠️ No user found for subscription ID {$subscription->id}. Skipping.");
                continue;
            }

            // ✅ Skip entirely — don't update step for unsubscribed users
            if ($user->sms_unsubscribed) {
                $this->warn("⚠️ User {$user->email} has unsubscribed from SMS. Skipping.");
                continue;
            }


            // ✅ Fix: use findOrFail safely with try/catch
            try {
                $plan = Plan::findOrFail($subscription->plan_id);
            } catch (Exception $e) {
                $this->error("❌ Plan not found for subscription ID {$subscription->id}. Skipping.");
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


                $message = "⚡ Heads up – your Xhale {$plan->name} package is about to run out!\n\n"
                    . "Renew now to stay in the draw for our upcoming prizes and keep access to exclusive offers 👉 {$link}\n\n"
                    . "Don't miss out!\n\n"
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
        $this->info("📊 Complete — ✅ {$successCount} sent, ❌ {$failCount} failed.");
        $this->info('✅ Package expiry reminder command finished.');
    }
}