<?php
namespace App\Console\Commands;

use App\Models\PrizeDraw;
use App\Models\PrizeDrawEntry; // <--- THIS LINE IS CRUCIAL AND MUST BE EXACTLY LIKE THIS]
use App\Models\PrizeDrawEntryBackup; 
use App\Models\UserPackageSubscription;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB; // If you're using DB facade
use App\Notifications\NewPrizeDrawEntryNotification; 


class GeneratePrizeDrawEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prize:generate-entries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates prize draw entries for active subscriptions and packages, respecting individual weekly cycles.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting prize draw entry generation...');

        $today = Carbon::today();


        $activeSubscriptions = UserPackageSubscription::where('type', 'subscription')->where('status', 'active')->get();

        $entriesGeneratedCount = 0;
        foreach ($activeSubscriptions as $subscription) {
            $package_subscriptions_id=$subscription->id;
            $userId = $subscription->user_id;
            $entriesToAdd = 0;

            // Determine entries based on type and assumed duration
            // This logic is simplified; in a real app, 'plan_id' would link to a plans table
            // defining duration and entry rates.
            $isSubscription = ($subscription->type === 'subscription');
            $isPackage = ($subscription->type === 'package');

            // Get the subscription start date for calculating the user's "week"
            $subscriptionStartDate = $subscription->starts_at; 
            if (!$subscriptionStartDate) {
                $this->warn("Skipping subscription ID {$subscription->id} for user {$userId} due to missing start date.");
                continue;
            }

            // Calculate the current "entry week" for this specific user based on their start date
            list($weekStart, $weekEnd) = $this->calculateUserWeekBounds($subscriptionStartDate, $today);


            echo "Week Start: " . $weekStart . PHP_EOL;
            echo "Week End: " . $weekEnd . PHP_EOL;


            // Now calculate the week number based on the start date
            $weeksPassed = floor($subscriptionStartDate->copy()->startOfDay()->diffInDays($today, false) / 7);
            $weekNumber = $weeksPassed + 1; // +1 to make it 1-indexed (Week 1, Week 2, etc.)
            

            // Now check if an entry was already added within this user's specific weekly period
            if (!$this->hasReceivedEntryInPeriod($userId, $package_subscriptions_id, $weekStart, $weekEnd)) {
                // If it's a yearly subscription, it's 1 entry per week
                if ($isSubscription) {
                    $entriesToAdd = 1;
                }
                // If it's a package, it's also 1 entry per week (as per your rules)
                elseif ($isPackage) {
                    $entriesToAdd = 1;
                }
            }

            if ($entriesToAdd > 0) {

                // Construct the enhanced description string
                $description = sprintf(
                    'Automatic entry from %s : %s to %s.',
                    $subscription->type,
                    $weekStart->format('Y-m-d'),
                    $weekEnd->format('Y-m-d')
                );

                // Add an entry to the log
                PrizeDrawEntry::create([
                    'user_id' => $userId,
                    'package_subscriptions_id' => $package_subscriptions_id,
                    'entries_added' => $entriesToAdd,
                    'description' => $description,
                ]);
                $entriesGeneratedCount++;
                $this->info("User {$userId} received {$entriesToAdd} entry for prize draw.");

                 $user = User::find($userId);
                    if ($user) {
                       // $user->notify(new NewPrizeDrawEntryNotification($user, $subscription, $entriesToAdd));
                    }
                    
            }
        }

        $this->info("Completed prize draw entry generation. Total new entries today: {$entriesGeneratedCount}.");
        return Command::SUCCESS;
    }

    /**
     * Calculates the start and end dates of the current "weekly period" for a user,
     * based on their subscription start date.
     *
     * @param Carbon $subscriptionStartDate
     * @param Carbon $currentDate
     * @return array [Carbon $weekStart, Carbon $weekEnd]
     */
    protected function calculateUserWeekBounds(Carbon $subscriptionStartDate, Carbon $currentDate): array
    {
        // Clone the subscription start date to avoid modifying the original
       echo "Week Start: " . $start = $subscriptionStartDate->copy()->startOfDay();
       echo "Week End: " . $end = $subscriptionStartDate->copy()->addDays(7)->endOfDay(); // End of the first week

        // If the current date is within the first week, use those bounds
        if ($currentDate->between($start, $end)) {
            return [$start, $end];
        }



        // If not in the first week, calculate which subsequent week the current date falls into.
        // Calculate the difference in days from the subscription start date to the current date
        echo 'diffInDays '.$diffInDays = $start->diffInDays($currentDate, false); // `false` to get negative for past dates

        // If the diff is negative (currentDate is before subscription start),
        // or if subscription is in the future, return a non-matching period.
        if ($diffInDays < 0) {
            echo "i am heree ";
            return [Carbon::minValue(), Carbon::minValue()]; // Effectively ensures no entry
        }

        // Calculate how many full weeks have passed since the subscription started
        // A "week" is 7 days from the start day.
       echo  'weeksPassed '.$weeksPassed = floor($diffInDays / 7);

        // Calculate the start of the current 'user week'
        $weekStart = $start->copy()->addWeeks($weeksPassed)->startOfDay();
        $weekEnd = $weekStart->copy()->addDays(7)->endOfDay();

        return [$weekStart, $weekEnd];
    }

    /**
     * Checks if a user has already received an entry within a specific date period
     * for a given prize draw.
     *
     * @param int $userId
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return bool
     */
    protected function hasReceivedEntryInPeriod(string $userId, string $package_subscriptions_id,Carbon $periodStart, Carbon $periodEnd): bool
    {

        // return false;

         // Check in the active PrizeDrawEntry table
        $existsInActive = PrizeDrawEntry::where('user_id', $userId)
            ->where('package_subscriptions_id', $package_subscriptions_id)
            ->where('type', 'paid')
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->exists();

        if ($existsInActive) {
            return true;
        }
        // Check in the PrizeDrawEntryBackup table if not found in active
        $existsInBackup = PrizeDrawEntryBackup::where('user_id', $userId)
            ->where('package_subscriptions_id', $package_subscriptions_id)
            ->where('type', 'paid')
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->exists();

        return $existsInBackup;
    }
}