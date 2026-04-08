<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PrizeDraw;
use App\Models\PrizeDrawEntry;
use App\Models\PrizeDrawEntryBackup;

class PackageEntryResetLogic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:package-entry-reset-logic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset entries for users with inactive packages and backup their entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting package entry reset logic...');

        // Get users who have only packages, no subscriptions, and no active package
        $users = User::whereIn('id', function($query) {
            $query->select('user_id')
                  ->from('user_package_subscriptions')
                  ->groupBy('user_id')
                  ->havingRaw("SUM(CASE WHEN type = 'subscription' THEN 1 ELSE 0 END) = 0")   // no subscription
                  ->havingRaw("SUM(CASE WHEN type = 'package' THEN 1 ELSE 0 END) > 0")        // has packages
                  ->havingRaw("SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) = 0");      // no active package
        })->get();

        $this->info('Users found: ' . $users->count());

        foreach ($users as $user) {
            $this->info('Processing user ID: ' . $user->id);

            // Get all entries of the user
            $entriesToMove = PrizeDrawEntry::where('user_id', $user->id)->get();

            foreach ($entriesToMove as $entry) {
                // Move entry to backup table
                PrizeDrawEntryBackup::create([
                    'user_id' => $entry->user_id,
                    'prize_draw_id' => $entry->prize_draw_id,
                    'entries_added' => $entry->entries_added,
                    'description' => $entry->description,
                    'created_at' => $entry->created_at,
                    'updated_at' => $entry->updated_at,
                ]);

                // Delete the original entry
                $entry->delete();
            }

            $this->info('Entries backed up and deleted for user ID: ' . $user->id);
        }

        $this->info('Package entry reset logic completed.');
    }
}
