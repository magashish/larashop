<?php
namespace App\Http\Controllers;
use App\Models\PrizeDraw;
use App\Models\PrizeDrawEntry; 
use App\Models\PrizeDrawEntryBackup;
use App\Models\UserPackageSubscription;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate; 
use App\Models\User; 


use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

use Illuminate\Support\Str;
use League\Csv\Reader;


class PrizeDrawEntryController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    // public function index()


    public function index(Request $request)
    {
        
        $users = User::orderBy('created_at', 'desc')->get();
        $query = PrizeDrawEntry::with('user', 'packageSubscription');
        $filterType = $request->input('type');
        if ($filterType) {
            if ($filterType =='manual') {
                $query->where('type', 'manual');
            } elseif ($filterType =='dynamic') {
                $query->whereIn('type', ['paid', 'free']);
            }
        }
        $entries = $query->latest()->paginate(50);
        return view('entries.index', compact('users','entries'));
    }



    //   public function index(Request $request)
    // {

    //     $entries = PrizeDrawEntry::with('user', 'packageSubscription')->latest()->paginate(20);
    //     $paidfreeentries = PrizeDrawEntry::with('user', 'packageSubscription')->whereIn('type', ['paid', 'free'])->latest()->paginate(20);
    //     $manualentries = PrizeDrawEntry::with('user', 'packageSubscription')->where('type', 'manual')->latest()->paginate(20);
        
    //     return view('entries.index', compact('entries','paidfreeentries','manualentries'));
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch all users to populate the user dropdown
        $users = User::all();

        // Fetch all package subscriptions to populate the subscription dropdown
        $subscriptions = UserPackageSubscription::with('plan')->get();

        return view('entries.create', compact('users', 'subscriptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|uuid|exists:users,id',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 2. Get the validated data
        $validatedData = $validator->validated();

        // 3. Manually set other required fields
        $validatedData['type'] = 'manual';
        $validatedData['entries_added'] = 1; // Assuming a default of 1 entry for manual entries
        $validatedData['package_subscriptions_id'] = null; // Assuming manual entries don't have a subscription

        // 4. Create the new entry in the database
        PrizeDrawEntry::create($validatedData);

        // 5. Redirect back with a success message
        return redirect()->route('entries.index')->with('status', 'Manual prize draw entry created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PrizeDraw $prizeDraw)
    {

        // echo "<pre>";
        // print_r($prizeDraw->user_entry_breakdown);
       return view('entries.show', compact('prizeDraw'));
   }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrizeDraw $prizeDraw)
    {
       return view('entries.edit', compact('prizeDraw'));
   }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrizeDraw $prizeDraw)
    {




    }


     public function uploadcsv()
    {
        // Clear any previous session data if starting fresh
        return view('entries.entriesuploadcsv'); // Your initial upload form Blade file
    }


   public function uploadcsvaction(Request $request)
    {
        $rules = [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ];

        $messages = [
            'csv_file.required' => 'Please select a CSV file to upload.',
            'csv_file.file' => 'The uploaded data is not a valid file.',
            'csv_file.mimes' => 'The uploaded file must be a CSV file.',
            'csv_file.max' => 'The CSV file size cannot exceed 10 MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $filePath = $request->file('csv_file')->getPathname();
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();
            $importedEntries = 0;
            DB::beginTransaction();
            foreach ($records as $record) {
                $createdAtRaw = $record['Date (UTC)'] ?? null;
                $createdAtCarbon = now(); 
                if ($createdAtRaw) {
                    try {
                        $createdAtCarbon = Carbon::createFromFormat('d/m/Y H:i', $createdAtRaw);
                    } catch (\Exception $e) {
                        try {
                            $createdAtCarbon = Carbon::parse($createdAtRaw);
                        } catch (\Exception $e) {
                        }
                    }
                } 
                
                // Fetch the user
                $customer_id = $record['Customer'];
                $user = User::where('stripe_id', $customer_id)->first();
                
                if ($user) {
                    $user_id = $user->id;
                    $description = $record['description'] ?? null;
                    $entries_added = $record['entries_added'] ?? 1;
                    $type = $record['type'] ?? 'manual';
                    
                    if (!$user_id) {
                        continue;
                    }

                    // *** CONVERT CARBON OBJECT TO DATABASE STRING FORMAT (YYYY-MM-DD HH:MM:SS) ***
                    $historicalTimestamp = $createdAtCarbon->toDateTimeString();
                    $data = [
                        'id' => Str::uuid(),
                        'user_id' => $user_id,
                        'entries_added' => $entries_added,
                        'description' => $description,
                        'type' => $type,
                        'created_at' => $historicalTimestamp, 
                        'updated_at' => $historicalTimestamp, 
                    ];
                    DB::table('prize_draw_entries')->insert($data);
                    $importedEntries++;
                }
            }

            // 4. Commit Transaction
            DB::commit(); 
            
            return redirect()->back()->with('status', "Successfully imported {$importedEntries} entries.");
            
        } catch (\Exception $e) {
            // 5. Rollback and Error Handling
            DB::rollBack();
            return redirect()->back()->with('status', "An error occurred during import: " . $e->getMessage());
        }
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrizeDrawEntry $entry)
    {
    // Check if the entry is a manual entry before deleting
        if ($entry->type !== 'manual') {
            return back()->with('status', 'Only manual entries can be deleted.');
        }

        $entry->delete();

        return redirect()->route('entries.index')->with('status', 'Prize draw entry deleted successfully!');
    }
}
