<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // Required for DB::raw() and sum()
use App\Models\PrizeDrawEntry; // Required for PrizeDrawEntry model
use App\Models\PrizeDrawEntryBackup; // Required for PrizeDrawEntryBackup model
use App\Models\User; // Assuming your 'admin' and 'winner' relationships point to a User model
use Illuminate\Support\Collection;
use Illuminate\Support\Str; // Make sure to import the Str facade
use Illuminate\Database\Eloquent\SoftDeletes; // Added for soft deletes


class PrizeDraw extends Model
{
     use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prize_draws';
    protected $keyType = 'string';
    public $incrementing = false; // <-- ADD THIS LINE

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'admin_id',
    //     'title', // Added title field
    //     'money', // Added money field
    //     'draw_type',
    //     'draw_date',
    //     'participants',
    //     'winner_id',
    //     'draw_datetime',
    //     'winner_user_finalised',
    //     'weekly_draw_reminder',
    //     'retarget_past_customers',
    // ];

     protected $fillable = [
        'admin_id',
        'title',             // Admin/internal name
        'prize_type',        // cash or non_cash
        'cash_amount',       // Nullable if non_cash
        'prize_title',       // Nullable if cash
        'prize_sub_title',
        'prize_value_amount',
        'prize_description', 
        'prize_image',
        'draw_type',         // subscribers or packages
        'draw_date',
        'participants',
        'winner_id',
        'draw_datetime',
        'winner_user_finalised',
        'weekly_draw_reminder',
        'retarget_past_customers',
        'show_on_site',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'draw_date' => 'datetime',
        'draw_datetime' => 'datetime',
        'show_on_site' => 'boolean',
    ];


     /**
     * The accessors to append to the model's array form.
     * This tells Laravel to include 'total_all_entries' when converting the model to an array/JSON.
     *
     * @var array
     */

    protected $appends = ['total_all_entries', 'user_entry_breakdown'];



         protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    /**
     * Get the combined total of entries from both live and backup tables for the prize draw.
     * This is an accessor that creates a 'total_all_entries' attribute.
     *
     * @return int The sum of entries from PrizeDrawEntry and PrizeDrawEntryBackup for this draw.
     */
    public function getTotalAllEntriesAttribute(): int
    {
        $liveEntriesCount = 0;
        $backupEntriesCount = PrizeDrawEntryBackup::sum('entries_added');

        // Return the combined total.
        return $liveEntriesCount + $backupEntriesCount;
    }


   /**
     * Get a list of all users and their combined entry counts,
     * along with a list of individual entries, for this prize draw.
     * Entries are combined from both the live and backup tables.
     * Each individual entry will now also specify its source table ('live' or 'backup').
     *
     * @return \Illuminate\Support\Collection A collection of objects, each with 'user_id', 'total_entries', and 'individual_entries'.
     */
    public function getUserEntryBreakdownAttribute(): Collection
    {
        // Fetch all individual entries from the live table for this prize draw.
        // Add a 'source_table' attribute to indicate its origin.
        $liveEntries = PrizeDrawEntry::get()
            ->map(function ($entry) {
                $entry->source_table = 'live';
                return $entry;
            });
        // Fetch all individual entries from the backup table for this prize draw.
        // Add a 'source_table' attribute to indicate its origin.
        $backupEntries = PrizeDrawEntryBackup::get()
            ->map(function ($entry) {
                $entry->source_table = 'backup';
                return $entry;
            });

        // Merge the two collections of individual entries.
        $allEntries = $liveEntries->concat($backupEntries);
        // Group all entries by user_id
        $groupedByUser = $allEntries->groupBy('user_id');
        // Transform the grouped collection to the desired output format
        return $groupedByUser->map(function ($entriesCollection, $userId) {
            // Calculate the total entries for this user
            $totalEntries = $entriesCollection->sum('entries_added');

            return (object) [
                'user_id' => $userId,
                'total_entries' => $totalEntries,
                'individual_entries' => $entriesCollection->values() // Use values() to reset keys
            ];
        })->values(); // Use values() to reset keys to a simple numeric array for the final collection.
    }



    /**
     * Define the relationship to the Admin User.
     * A prize draw may be managed by an admin.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Define the relationship to the Winning User.
     * A prize draw has one winner.
     */
    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    /**
     * Define the relationship to the PrizeDrawEntryLog.
     * A prize draw can have many entries contributing to it.
     */
    public function entries()
    {
        return $this->hasMany(PrizeDrawEntry::class);
    }


 
}
