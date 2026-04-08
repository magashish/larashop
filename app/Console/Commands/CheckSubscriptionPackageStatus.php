<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserPackageSubscription; // Use your new model
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckSubscriptionPackageStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-subscription-package-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks and updates the status of user package subscriptions and user active flags.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting subscription status update...');

        

        $userPackageSubscriptions = UserPackageSubscription::whereNotIn('type', ['subscription'])->get();
        $totalSubscriptions = $userPackageSubscriptions->count();
        $updatedSubscriptionRecords = 0;
        $this->info("Found {$totalSubscriptions} subscriptions to evaluate.");
        foreach ($userPackageSubscriptions as $subscriptions) {
            $oldStatus = $subscriptions->status;
            $newStatus = $oldStatus; 
            if  ($subscriptions->ends_at && $subscriptions->ends_at->isPast()) {
                $newStatus = 'expired';
            }
        
            if ($oldStatus !== $newStatus) {
                $subscriptions->update(['status' => $newStatus]);
                $this->info("   -> UserPackageSubscription ID: {$subscriptions->id} (User: {$subscriptions->user_id}, Type: {$subscriptions->type}) status updated from {$oldStatus} to {$newStatus}.");
                $updatedSubscriptionRecords++;
            } else {
                $this->comment("   -> UserPackageSubscription ID: {$subscriptions->id} (User: {$subscriptions->user_id}, Type: {$subscriptions->type}) status is already {$oldStatus}, no change.");
            }
        }

        $this->info("Finished. Updated {$updatedSubscriptionRecords} subscription records.");

        return Command::SUCCESS;
    }
}