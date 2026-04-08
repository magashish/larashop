<?php
namespace App\Http\Controllers;
use App\Models\PrizeDraw;
use App\Models\PrizeDrawEntry; // Use the correct singular model name
use App\Models\PrizeDrawEntryBackup; // We will create this model for the backup table
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate; 
use App\Models\User; 


use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use App\Notifications\PrizeDrawWinnerNotification;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PrizeDrawController extends Controller
{

   public function __construct()
   {
    $this->middleware(['permission:view prize_draws'])->only('index');
    $this->middleware(['permission:create prize_draws'])->only(['create', 'store']);
}

private function getUniqueFileName($originalName, $directory)
{
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $filenameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
    $uniqueFilename = $originalName;
    $counter = 0;
    while (Storage::disk('public')->exists($directory . '/' . $uniqueFilename)) {
        $counter++;
        $uniqueFilename = $filenameWithoutExt . '-' . $counter . '.' . $extension;
    }
    return $uniqueFilename;
}


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Example authorization check (requires a PrizeDrawPolicy configured)
        // Gate::authorize('viewAny', PrizeDraw::class);

        $prizeDraws = PrizeDraw::orderBy('draw_date', 'desc')->get();
        return view('prize-draws.index', compact('prizeDraws'));
    }


  public function prizedrawlog()
{
    $logs = DB::table('user_activity_logs')
        ->join('users', 'users.id', '=', 'user_activity_logs.user_id')
        ->select(
            'user_activity_logs.*',
            'users.first_name',
            'users.last_name',
            'users.email'
        )
        ->where('event', 'prize_won')
        ->orderBy('user_activity_logs.created_at', 'desc')
        ->get();

    return view('prize-draws.prize-draw-logs', compact('logs'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Gate::authorize('create', PrizeDraw::class);
        return view('prize-draws.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $request->merge([
        'show_on_site' => $request->has('show_on_site'),
    ]);
       $rules = [
        'title'             => 'required|string|max:255',
        'prize_type'        => 'required|in:cash,non_cash',
        'prize_description' => 'required|string',
        'prize_image'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        'draw_type'         => 'required|in:subscribers,packages',
        'draw_date'         => 'required|date',
        'show_on_site'      => 'nullable|boolean',
    ];
    if ($request->prize_type === 'cash') {
        $rules['cash_amount'] = 'required|numeric|min:0';
    }

    if ($request->prize_type === 'non_cash') {
        $rules['prize_title'] = 'required|string|max:255';
        $rules['prize_sub_title'] = 'required|string|max:255';
        $rules['prize_value_amount'] = 'required|numeric|min:0';
    }
    $validated = $request->validate($rules);

    if ($request->hasFile('prize_image')) {
        $uploadDirectory = 'prize-draws'; 
        $file = $request->file('prize_image');
        $originalFileName = $file->getClientOriginalName();
        $uniqueFileName = $this->getUniqueFileName($originalFileName, $uploadDirectory);
        $path = $file->storeAs($uploadDirectory, $uniqueFileName, 'public');
        try {
            $absolutePath = Storage::disk('public')->path($path);
            $optimizerChain->optimize($absolutePath); 
            \Log::info("Spatie Prize Image optimized successfully: " . $path);
        } catch (\Exception $e) {
            \Log::error("Spatie Prize Image Optimizer failed: " . $e->getMessage());
        }
        $validated['prize_image'] = $path;
    }

    

    PrizeDraw::create($validated);
    return redirect()
    ->route('prize_draws.index')
    ->with('status', 'Prize draw created successfully!');
}

    /**
     * Display the specified resource.
     */
    public function show(PrizeDraw $prizeDraw)
    {
     return view('prize-draws.show', compact('prizeDraw'));
 }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrizeDraw $prizeDraw)
    {
       return view('prize-draws.edit', compact('prizeDraw'));
   }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrizeDraw $prizeDraw)
    {



        $action = $request->input('action');
        if ($prizeDraw->winner_user_finalised == 'yes') {
            return back()->with('status', "Winner for Prize Draw ID {$prizeDraw->id} has already been finalized. Skipping.");
        }
        
        if ($action == 'draw' || $action == 'redraw') {

            // 1️⃣ Subscribers only
            $subscribersOnly = DB::table('user_package_subscriptions as ups')
                ->join('users as u', 'u.id', '=', 'ups.user_id')
                ->where('u.exclude_from_prize_draw', false)
                ->where('ups.type', 'subscription')
                ->where('ups.status', 'active')
                ->pluck('ups.user_id')
                //->pluck('u.email', 'ups.user_id')
                ->unique()
                ->toArray();


            // 2️⃣ Package holders only

            $packagesOnly = DB::table('user_package_subscriptions as ups')
                ->join('users as u', 'u.id', '=', 'ups.user_id')
                ->where('u.exclude_from_prize_draw', false)
                ->where('ups.type', 'package')
                ->where('ups.status', 'active')
                ->pluck('ups.user_id')
                // ->pluck('u.email', 'ups.user_id')
                ->unique()
                ->toArray();


           // 3️⃣ Subscribers who also have packages
            $subscribersWithPackages = DB::table('user_package_subscriptions as ups')
            ->join('users as u', 'u.id', '=', 'ups.user_id')
            ->where('u.exclude_from_prize_draw', false)
            ->groupBy('ups.user_id')
            ->havingRaw('SUM(ups.type = "subscription" AND ups.status = "active") > 0')
            ->havingRaw('SUM(ups.type = "package" AND ups.status = "active") > 0')
            ->pluck('ups.user_id')
            ->toArray();

            
            if ($prizeDraw->draw_type=='subscribers') {

                $previousWinners = PrizeDraw::whereNotNull('winner_id')
                    ->where('draw_type', 'subscribers')
                    ->pluck('winner_id')
                    ->toArray();

                    
                $smallGiveawayUsers = array_diff($subscribersOnly, $previousWinners);

                // $entriesByUsers = PrizeDrawEntry::select('user_id', DB::raw('SUM(entries_added) as total_entries'))
                // ->whereHas('user', function($query) {
                //     $query->where('exclude_from_prize_draw', false);
                // })
                // ->whereIn('user_id', $smallGiveawayUsers)
                // ->groupBy('user_id')
                // ->orderByDesc('total_entries')
                // ->get();

                $entriesByUsers = PrizeDrawEntry::select('user_id', DB::raw('SUM(entries_added) as total_entries'))
                ->whereHas('user', function ($query) {
                    $query->where('exclude_from_prize_draw', false);
                })
                ->whereIn('user_id', $smallGiveawayUsers)
                ->groupBy('user_id')
                ->orderByDesc('total_entries')
               // ->limit(20)
                //->get();
                ->pluck('total_entries', 'user_id');

                $winnerUserId = $entriesByUsers->keys()->random();
                $maxEntries = $entriesByUsers[$winnerUserId];
                $winner = [
                    'user_id' => $winnerUserId,
                    'entries' => $maxEntries,
                ];

                


            }elseif($prizeDraw->draw_type=='packages'){

                 $majorGiveawayUsers = array_unique(array_merge($subscribersOnly, $packagesOnly));

                // $entriesByUsers = PrizeDrawEntry::select('user_id', DB::raw('SUM(entries_added) as total_entries'))
                // ->whereHas('user', function($query) {
                //     $query->where('exclude_from_prize_draw', false);
                // })
                // ->whereIn('user_id', $majorGiveawayUsers)
                // ->groupBy('user_id')
                // ->orderByDesc('total_entries')
                // ->get();


                $entriesByUsers = PrizeDrawEntry::select('user_id', DB::raw('SUM(entries_added) as total_entries'))
                ->whereHas('user', function ($query) {
                    $query->where('exclude_from_prize_draw', false);
                })
                ->whereIn('user_id', $majorGiveawayUsers)
                ->groupBy('user_id')
                ->orderByDesc('total_entries')
                //->limit(20)
                //->get();
                ->pluck('total_entries', 'user_id');

                $pool = [];
                foreach ($entriesByUsers as $userId => $entries) {
                    for ($i = 0; $i < $entries; $i++) {
                        $pool[] = $userId;
                    }
                }
                $winnerUserId = $pool[array_rand($pool)];
                $maxEntries = $entriesByUsers[$winnerUserId];
                $winner = [
                    'user_id' => $winnerUserId,
                    'entries' => $maxEntries,
                ];

            }

            

            if ($entriesByUsers->isEmpty()) {
                return back()->with('status', "No entries found for Prize Draw ID {$prizeDraw->id}. Cannot draw a winner.");
            }

            // $maxEntries = $entriesByUsers->first()->total_entries;
            // $potentialWinners = $entriesByUsers->filter(function ($entry) use ($maxEntries) {
            //     return $entry->total_entries === $maxEntries;
            // });
            // $winnerEntry = $potentialWinners->random();
            // $winnerUserId = $winnerEntry->user_id;

            


            




            DB::beginTransaction();

            $prizeDraw->winner_id = $winnerUserId;
            $prizeDraw->draw_datetime = now();
            $prizeDraw->save();

            DB::commit();
            return back()->with('status', "Winner for Prize Draw ID {$prizeDraw->id} has been drawn: User ID: {$winnerUserId}  User ID: {$winnerUserId}with {$maxEntries} entries.");

        }elseif ($action == 'finalize') {
            
            $winnerUserId=$prizeDraw->winner_id;
            $prizeDraw->winner_user_finalised = 'yes';
            $prizeDraw->draw_datetime = now();
            $prizeDraw->save();
            logUserActivity(
                $prizeDraw->winner_id,
                'prize_won',
                'User Won Prize : ' . $prizeDraw->title,
                [
                    'prize_id' => $prizeDraw->id,
                    'prize_name' => $prizeDraw->title
                ]
            );

            if ($prizeDraw->draw_type=='packages') {
                $entriesToMove = PrizeDrawEntry::where('user_id', $prizeDraw->winner_id)->get();
                foreach ($entriesToMove as $entry) {
                    PrizeDrawEntryBackup::create([
                        'user_id' => $entry->user_id,
                        'package_subscriptions_id' => $entry->package_subscriptions_id,
                        'entries_added' => $entry->entries_added,
                        'description' => $entry->description,
                        'created_at' => $entry->created_at,
                        'updated_at' => $entry->updated_at,
                    ]);
                    $entry->delete();
                }
                DB::commit();
            }
            
            $winner = User::find($winnerUserId);
            if ($winner) {
                // $winner->notify(new PrizeDrawWinnerNotification($prizeDraw)); 
            }

            return back()->with('status', "Winner for Prize Draw ID {$prizeDraw->id} has been drawn: User ID: {$winnerUserId}.");

        }elseif ($action == 'update') {

            // echo "<pre>";
            // print_r($_POST);
            // exit();

            $request->merge([
                'show_on_site' => $request->has('show_on_site'),
            ]);

            $rules = [
                'title'             => 'required|string|max:255',
                'prize_type'        => 'required|in:cash,non_cash',
                'prize_description' => 'required|string',
                'prize_image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'draw_type'         => 'required|in:subscribers,packages',
                'draw_date'         => 'required|date',
                'show_on_site'      => 'nullable|boolean',
            ];

            if ($request->prize_type === 'cash') {
                $rules['cash_amount'] = 'required|numeric|min:0';
                $request->merge(['prize_title' => null, 'prize_value_amount' => null]);
            }

            if ($request->prize_type === 'non_cash') {
                $rules['prize_title'] = 'required|string|max:255';
                $rules['prize_sub_title'] = 'required|string|max:255';
                $rules['prize_value_amount'] = 'required|numeric|min:0';
                $request->merge(['cash_amount' => null]);
            }

            $validated = $request->validate($rules);

            if ($request->hasFile('prize_image')) {
        // 1. Remove the old image from storage if it exists
                if ($prizeDraw->prize_image && Storage::disk('public')->exists($prizeDraw->prize_image)) {
                    Storage::disk('public')->delete($prizeDraw->prize_image);
                }

        // 2. Process and Optimize the new image
                $uploadDirectory = 'prize-draws'; 
                $file = $request->file('prize_image');
                $originalFileName = $file->getClientOriginalName();
                $uniqueFileName = $this->getUniqueFileName($originalFileName, $uploadDirectory);

                $path = $file->storeAs($uploadDirectory, $uniqueFileName, 'public');

                try {
                    $absolutePath = Storage::disk('public')->path($path);
            // Assuming $optimizerChain is available (e.g., via app() or injected)
                    app(\Spatie\ImageOptimizer\OptimizerChain::class)->optimize($absolutePath);
                    \Log::info("Prize Image updated and optimized: " . $path);
                } catch (\Exception $e) {
                    \Log::error("Optimizer failed on update: " . $e->getMessage());
                }

                $validated['prize_image'] = $path;
            } else {
        // If no new image is uploaded, remove it from $validated so it doesn't overwrite with null
                unset($validated['prize_image']);
            }

    // 3. Update the record
            $prizeDraw->update($validated);

            return redirect()
            ->route('prize_draws.index')
            ->with('status', 'Prize draw updated successfully!');

        }

    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrizeDraw $prizeDraw)
    {
        $prizeDraw->delete();
        return redirect()->route('prize_draws.index')->with('success', 'Prize draw deleted successfully!');
    }
}
