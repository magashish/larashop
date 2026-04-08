<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\PrizeDraw;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\SmsService;
use App\Helpers\PhoneHelper;
use Exception;

class Test_WeeklyDrawReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-weekly-draw-reminder';

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



        // Audience build karo
        // $subscribersOnly = DB::table('user_package_subscriptions as ups')
        // ->join('users as u', 'u.id', '=', 'ups.user_id')
        // ->where('u.exclude_from_prize_draw', false)
        // ->groupBy('ups.user_id')
        // ->havingRaw('SUM(ups.type = "subscription" AND ups.status = "active") > 0')
        // ->havingRaw('SUM(ups.type = "package" AND ups.status = "active") = 0')
        // ->pluck('ups.user_id')
        // ->toArray();

        // $packagesOnly = DB::table('user_package_subscriptions as ups')
        // ->join('users as u', 'u.id', '=', 'ups.user_id')
        // ->where('u.exclude_from_prize_draw', false)
        // ->groupBy('ups.user_id')
        // ->havingRaw('SUM(ups.type = "package" AND ups.status = "active") > 0')
        // ->havingRaw('SUM(ups.type = "subscription" AND ups.status = "active") = 0')
        // ->pluck('ups.user_id')
        // ->toArray();
        // $uniqueUserIds = array_unique(array_merge($subscribersOnly, $packagesOnly));
        // $all_users = User::whereIn('id', $uniqueUserIds)->pluck('mobile', 'email');


    // echo "<pre>";
    // print_r($all_users);



        // $users = User::withCount(['mainEntries', 'backupEntries'])
        // ->where('exclude_from_prize_draw', false)
        // ->whereHas('userPackageSubscriptions', function ($query) {
        //     $query->where('status', 'active');
        // })
        // ->pluck('mobile', 'email');

        // echo "<pre>";
        // print_r($users);


        $users = User::where('exclude_from_prize_draw', false)
        ->whereHas('userPackageSubscriptions', function ($query) {
            $query->where('status', 'active');
        })
        ->pluck('mobile', 'email');


        $users = User::where('exclude_from_prize_draw', false)
        ->whereHas('userPackageSubscriptions', function ($query) {
            $query->where('status', 'active');
        })
        ->pluck('mobile', 'email');


     


       


        $this->info('Starting weekly draw reminder command...');

        $timezone = env('APP_TIMEZONE', 'Australia/Sydney');
        $now = Carbon::now($timezone);

    // kal ki date
        $targetDate = $now->copy()->addDay()->toDateString();

        $this->info("🕐 Timezone    : " . $timezone);
        $this->info("🕐 NOW         : " . $now->toDateTimeString());
        $this->info("🕐 Target Date : " . $targetDate);


    // Atomic lock
        $updated = PrizeDraw::where('winner_user_finalised', 'no')
        ->where('test_weekly_draw_reminder', 'no')
        ->whereDate('draw_date', $targetDate)
        ->update(['test_weekly_draw_reminder' => 'yes']);

        if ($updated === 0) {
            $this->info('No draws to process right now.');
            return;
        }

        $this->info("🔒 Claimed {$updated} draw(s) for processing.");

        $draws = PrizeDraw::where('winner_user_finalised', 'no')
        ->where('test_weekly_draw_reminder', 'yes')
        ->whereDate('draw_date', $targetDate)
        ->get();

        if ($draws->isEmpty()) {
            $this->info('No draws found. Exiting.');
            return;
        }

        $audience = User::where('exclude_from_prize_draw', false)
        ->where('sms_unsubscribed', '0')
       // ->where('id', 'a3238720-0f57-47fc-9ce9-bd9624ce0ca5')
       //->pluck('mobile', 'email');
        ->get();

        // echo "<pre>";
        // print_r($audience);

        // exit();

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



            // try {

            //     $rawPhone    = $user->mobile;
            //     $defaultRegion = env('DEFAULT_REGION');
            //     $phoneInfo     = \App\Helpers\PhoneHelper::detectCountry($rawPhone, $defaultRegion);
            //     if (!$phoneInfo['valid']) {
            //         $this->error("❌ Invalid phone for {$user->email}");
            //         $failCount++;
            //         continue;
            //     }
            //     $unsubscribeLink = route('unsubscribe.sms', ['token' => $user->id]);
            //     $message = "🎉 Hi {$user->name},\n\n"
            //     . "Xhale Draw Tomorrow! We have exciting giveaways happening on {$targetDate}.\n\n"
            //     . "Make sure you're in it to win it! Good luck! 🍀 – Xhale Team\n\n"
            //     . "🔕 To unsubscribe from SMS, click here: {$unsubscribeLink}";
            //     $response = \App\Services\SmsService::sendSms($phoneInfo['formatted'], $message, null, 'Weekly draw reminder');
            //     if ($response['status'] === 'success') {
            //         $this->info("✅ Weekly draw reminder  SMS sent to {$user->mobile} {$user->email}");
            //         $successCount++;
            //     } else {
            //         $this->error("❌ Weekly draw reminder  Failed for {$user->email}: " . ($response['error'] ?? 'Unknown'));
            //         $failCount++;
            //     }

            // } catch (Exception $e) {
            //     $this->error("💥 Weekly draw reminder  Exception for {$user->email}: " . $e->getMessage());
            //     $failCount++;
            // }




            


            // $message = "✅ Weekly draw reminder SMS sent to {$user->mobile} {$user->email}";
            // $this->info($message);
            // Log::info($message);



        }

        $this->info("📊 Complete — ✅ {$successCount} sent, ❌ {$failCount} failed.");
        $this->info('✅ Weekly draw reminder command finished.');
    }
}