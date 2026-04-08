<?php

namespace App\Console\Commands;

use App\Models\PrizeDraw;
use App\Models\PrizeDrawEntry; // Use the correct singular model name
use App\Models\PrizeDrawEntryBackup; // We will create this model for the backup table
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB; // For database transactions

class DrawPrizeWinnerByEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prize:draw-winner';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Draws a winner for a specified prize draw based on maximum entries and moves entries to backup.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting prize draw winner selection...');
        $prizeDraw = PrizeDraw::where('draw_type', 'users')->first();


        if ($prizeDraw->winner_user_finalised == 'yes') {
            $this->info("Winner for Prize Draw ID {$prizeDraw->id} has already been finalized. Skipping.");
            return Command::SUCCESS;
        }

        // Get total entries per user for this prize draw
        $entriesByUsers = PrizeDrawEntry::select('user_id', DB::raw('SUM(entries_added) as total_entries'))
            ->where('prize_draw_id', $prizeDraw->id)
            ->groupBy('user_id')
            ->orderByDesc('total_entries')
            ->get();

        if ($entriesByUsers->isEmpty()) {
            $this->warn("No entries found for Prize Draw ID {$prizeDraw->id}. Cannot draw a winner.");
            return Command::FAILURE;
        }

        // Find the user(s) with the maximum entries
        $maxEntries = $entriesByUsers->first()->total_entries;
        $potentialWinners = $entriesByUsers->filter(function ($entry) use ($maxEntries) {
            return $entry->total_entries === $maxEntries;
        });

        // If there's more than one user with max entries, pick one randomly among them
        $winnerEntry = $potentialWinners->random();
        $winnerUserId = $winnerEntry->user_id;

        // Start database transaction for atomicity (winner update and entry movement)
        DB::beginTransaction();
        try {
            // 1. Update the PrizeDraw with the winner's information
            $prizeDraw->winner_id = $winnerUserId;
            $prizeDraw->winner_user_finalised = 'yes';
            $prizeDraw->draw_datetime = now();
            $prizeDraw->save();

            // 2. Move entries to backup table
            $entriesToMove = PrizeDrawEntry::where('prize_draw_id', $prizeDraw->id)->get();
            foreach ($entriesToMove as $entry) {
                PrizeDrawEntryBackup::create([
                    'user_id' => $entry->user_id,
                    'prize_draw_id' => $entry->prize_draw_id,
                    'entries_added' => $entry->entries_added,
                    'description' => $entry->description,
                    'created_at' => $entry->created_at,
                    'updated_at' => $entry->updated_at,
                ]);
                $entry->delete(); 
            }


            // 3. Move winner User entries to backup table 
            $entriesToMove = PrizeDrawEntry::where('user_id', $winnerUserId)->get();
            foreach ($entriesToMove as $entry) {
                PrizeDrawEntryBackup::create([
                    'user_id' => $entry->user_id,
                    'prize_draw_id' => $entry->prize_draw_id,
                    'entries_added' => $entry->entries_added,
                    'description' => $entry->description,
                    'created_at' => $entry->created_at,
                    'updated_at' => $entry->updated_at,
                ]);
                $entry->delete(); 
            }




            DB::commit();
            $winnerUser = $prizeDraw->winnerUser; 
            $this->info("Winner for Prize Draw ID {$prizeDraw->id} is User ID: {$winnerUserId} (Max entries: {$maxEntries}).");
            $this->info("All entries for Prize Draw ID {$prizeDraw->id} moved to prize_draw_entries_backups.");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error drawing winner or moving entries for Prize Draw ID {$prizeDraw->id}: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
