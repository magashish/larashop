<?php
namespace App\Http\Controllers;
use App\Models\Organisation;
use App\Models\Offer;
use App\Models\Category;     // Assuming you have a Category model
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\OrganisationAddressType; // Added use statement for OrganisationAddressType
use App\Enums\OrganisationStatus;       // Added use statement for OrganisationStatus
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Auth; // Import the Auth facade
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // <--- ADD THIS LINE IF MISSING
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon; 

use App\Models\OrganisationUser; // Make sure this is imported


use Illuminate\Support\Facades\Http; // For downloading images
use Illuminate\Support\Str;
use League\Csv\Reader;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Validator; // Don't forget to import this!


// 🔥 WebP
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;



class OrganisationController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:view organisations'])->only('index');
        $this->middleware(['permission:create organisations'])->only(['create', 'store']);
        $this->middleware(['permission:edit organisations'])->only(['edit', 'update']);
        $this->middleware(['permission:delete organisations'])->only('destroy');
    }


    private function generateWebpPath(string $originalName, string $folder): string
    {
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        $baseName = Str::slug($name) ?: 'image';

        $filename = $baseName . '.webp';
        $path = trim($folder, '/') . '/' . $filename;

        $counter = 1;
        while (Storage::disk('public')->exists($path)) {
            $filename = $baseName . '-' . $counter . '.webp';
            $path = trim($folder, '/') . '/' . $filename;
            $counter++;
        }

        return $path;
    }


    
    public function index(Request $request)
    {
        // Fetch organisations based on their processing status
        // $organisations_new = Organisation::where('processing_status', OrganisationStatus::NEW)->get();
        // $organisations_approved = Organisation::where('processing_status', OrganisationStatus::APPROVED)->get();
        // $organisations_rejected = Organisation::where('processing_status', OrganisationStatus::REJECTED)->get();


        $searchQuery = $request->input('business');
        $organisations_new = Organisation::where('processing_status', OrganisationStatus::NEW)
        ->when($searchQuery, function ($query) use ($searchQuery) {
            $query->where('title', 'like', '%' . $searchQuery . '%');
        })
        ->get();

        $organisations_approved = Organisation::where('processing_status', OrganisationStatus::APPROVED)
        ->when($searchQuery, function ($query) use ($searchQuery) {
            $query->where('title', 'like', '%' . $searchQuery . '%');
        })
        ->get();

        $organisations_rejected = Organisation::where('processing_status', OrganisationStatus::REJECTED)
        ->when($searchQuery, function ($query) use ($searchQuery) {
            $query->where('title', 'like', '%' . $searchQuery . '%');
        })
        ->get();



        // Pass all three collections to the view
        return view('organisation.index', compact('organisations_new','organisations_approved','organisations_rejected'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('organisation.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'abn' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'instagram' => 'nullable|max:255',
            'facebook' => 'nullable|max:255',
            'youtube' => 'nullable|max:255',
            'tiktok' => 'nullable|max:255',
            'twitter' => 'nullable|max:255',
            'organisation_address_type' => [
                'required',
                Rule::in(OrganisationAddressType::all()) 
            ],
            'full_address' => 'nullable|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'street_number' => 'nullable|string|max:24',
            'street_name' => 'nullable|string|max:128',
            'city' => 'nullable|string|max:64',
            'state' => 'nullable|string|max:3',
            'postcode' => 'nullable|string|max:6',
            'country' => 'nullable|string|max:24',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'user_id'=> 'required'

        ]);
        $validatedData['admin_id'] = Auth::id(); 

        // $organisation=Organisation::create($validatedData);
        // $userOrganisationsData = $request->input('user', []);
        // $submittedPivotIds = []; 
        // foreach ($userOrganisationsData as $rowKey => $orgData) {
        //     $organisationId = $organisation->id;
        //     $pivotData = [
        //         'start_date' => $orgData['start_date'] ?? null,
        //         'end_date' => $orgData['end_date'] ?? null,
        //         'role' => json_encode($orgData['role'] ?? []),
        //     ];
        //     $pivotData['admin_id'] = Auth::id();
        //     $pivotData['user_id'] =  null;
        //     $organisation->associatedUsers()->attach($organisationId, $pivotData);
            
        // }

        // 1. Create the new Organisation and capture its instance

        if ($request->input('organisation_address_type') === OrganisationAddressType::ONLINE) {
            $validatedData['full_address'] = $request->input('organisation_address_type');
            $validatedData['latitude'] =null;
            $validatedData['longitude'] =null;
            $validatedData['street_number'] =null;
            $validatedData['street_name'] =null;
            $validatedData['city'] =null;
            $validatedData['state'] =null;
            $validatedData['postcode'] =null;
            $validatedData['country'] =null;
        }


        //  $userOrganisationsData = $request->input('user', []);
        // // Custom validation for each row
        // foreach ($userOrganisationsData as $rowKey => $orgData) {
        //     if (!empty($orgData['start_date']) && !empty($orgData['end_date'])) {
        //         $start = \Carbon\Carbon::parse($orgData['start_date']);
        //         $end = \Carbon\Carbon::parse($orgData['end_date']);
        //         if ($end->lte($start)) {
        //             return back()
        //             ->withInput()
        //             ->withErrors([
        //                 "user.$rowKey.end_date" => "End Date must be greater than Start Date"
        //             ]);
        //         }
        //     }
        // }



        // if ($request->hasFile('image')) {
        //     $newImagePath = $request->file('image')->store('organisations/images', 'public');
        //     $validatedData['image'] = $newImagePath;
        // }


         if ($request->hasFile('image')) {
            // $manager = new ImageManager(new Driver());
            // $file = $request->file('image');
            // $filename = uniqid() . '.webp';
            // $path = 'organisations/images/' . $filename;
            // $absolutePath = storage_path('app/public/' . $path);
            // $image = $manager->read($file->getRealPath());
            // if ($image->width() > 1200) {
            //     $image->scaleDown(1200);
            // }
            // $image->toWebp(80)->save($absolutePath);
            // $validatedData['image'] = $path;

            $manager = new ImageManager(new Driver());
            $file = $request->file('image');
            $path = $this->generateWebpPath(
                $file->getClientOriginalName(),
                'organisations/images'
            );
            $absolutePath = storage_path('app/public/' . $path);
            $image = $manager->read($file->getRealPath());
            if ($image->width() > 1200) {
                $image->scaleDown(1200);
            }
            $image->toWebp(80)->save($absolutePath);
            $validatedData['image'] = $path;

        }




        
        $organisation = Organisation::create($validatedData);



         // 4️⃣ Update or insert users in pivot
        $userOrganisationsData = $request->input('user', []);
        $submittedPivotIds = []; 
        foreach ($userOrganisationsData as $rowKey => $orgData) {
            $user_id = $orgData['id'];
            $pivotData = [
                'start_date' => $orgData['start_date'] ?? null,
                'end_date' => $orgData['end_date'] ?? null,
                'role' => json_encode($orgData['role'] ?? []),
            ];
            $assignedRoleIds = $orgData['role'] ?? [];

                $pivotData['admin_id'] = null;
                $pivotData['user_id'] = Auth::id(); 
                $pivotData['organisation_assign_id'] = $organisation->id; 
                $pivotData['user_assign_id'] = $user_id; 
                $newOrganisationUser = OrganisationUser::create($pivotData);
                $newOrganisationUser->roles()->sync($assignedRoleIds);
        }


        // $userOrganisationsData = $request->input('user', []);
        // $submittedPivotIds = []; 
        // foreach ($userOrganisationsData as $rowKey => $orgData) {
        //     $userIdToAttach = $orgData['id']; 
        //     $pivotData = [
        //         'start_date' => $orgData['start_date'] ?? null,
        //         'end_date' => $orgData['end_date'] ?? null,
        //         'role' => json_encode($orgData['role'] ?? []),
        //         'admin_id' => Auth::id(),

        //     ];
        //     $organisation->AllassociatedUsers()->attach($userIdToAttach, $pivotData);
        // }



        return to_route('organisations.index')->with('status', 'Organisation added successfully.');
    }

    /**
     * Display the specified resource.
     */
   

     public function show_old(Organisation $organisation)
      {

        $today = Carbon::today();
        $offer_new = Offer::with(['organisation', 'categories'])
        ->where('organisation_id', $organisation->id)
        ->where('processing_status', OrganisationStatus::NEW)
        ->get();

        $offer_approved = Offer::with(['organisation', 'categories'])
        ->where('organisation_id', $organisation->id)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->get();

        $offer_rejected = Offer::with(['organisation', 'categories'])
        ->where('organisation_id', $organisation->id)
        ->where('processing_status', OrganisationStatus::REJECTED)
        ->get();
        
        // Get expired offers: end_date is on or before the current moment
        $offer_expire = Offer::with(['organisation', 'categories'])
        ->where('organisation_id', $organisation->id)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->where('end_date', '<', $today)
        ->get();

       // Get live offers: start_date is on or before the current moment AND end_date is on or after the current moment
        $live_offers = Offer::with(['organisation', 'categories'])
        ->where('organisation_id', $organisation->id)
        ->where('start_date', '<=', $today)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->where('end_date', '>=', $today)
        ->get();


        $offers = Offer::with(['organisation', 'categories'])->latest()->paginate(10);
        return view('organisation.show', compact('organisation','offers','offer_new','offer_expire','live_offers','offer_approved','offer_rejected'));
   }


       public function show(Organisation $organisation)
       {
        $today = Carbon::today();

        // Base query scoped to this organisation
        $baseQuery = Offer::with(['organisation', 'categories'])
        ->where('organisation_id', $organisation->id);

        $offer_new = (clone $baseQuery)
        ->where('processing_status', OrganisationStatus::NEW)
        ->get();

        $offer_approved = (clone $baseQuery)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->get();

        $offer_rejected = (clone $baseQuery)
        ->where('processing_status', OrganisationStatus::REJECTED)
        ->get();

        // Get expired offers: end_date is before today and no_end_date is not set
        $offer_expire = (clone $baseQuery)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->where('end_date', '<', $today)
        ->where('no_end_date', 0)
        ->get();

        // Get live offers: approved, started, and either not expired or has no end date
        $live_offers = (clone $baseQuery)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->where('start_date', '<=', $today)
        ->where(function ($query) use ($today) {
            $query->where('end_date', '>=', $today)
            ->orWhere('no_end_date', 1);
        })
        ->get();

        $offers = (clone $baseQuery)->latest()->paginate(10);

        return view('organisation.show', compact(
            'organisation',
            'offers',
            'offer_new',
            'offer_expire',
            'live_offers',
            'offer_approved',
            'offer_rejected'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation)
    {
        //  $organisation->load(['associatedUsers' => function($query) {
        //     $query->withPivot('role', 'start_date', 'end_date');
        // }]);
        // $availableRoles = Role::all()->pluck('name', 'id')->toArray();
        //  return view('organisation.edit', compact('organisation','availableRoles'));




       $organisation->load('AllassociatedUsers'); 
        //  // Eager load associated users with pivot data for the current organisation
        // $organisation->load(['AllassociatedUsers' => function($query) {
        //     $query->withPivot('id', 'role', 'start_date', 'end_date');
        // }]);
        // You might also need available roles if you plan to edit them on this page
        // For now, I'll assume you don't need them for just displaying,
        // but if you add editing functionality, you'll need to fetch them.
        $availableRoles = Role::all()->pluck('name', 'id')->toArray();
        return view('organisation.edit', compact('organisation','availableRoles'));

    }

  

    public function update(Request $request, Organisation $organisation)
    {

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'abn' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'instagram' => 'nullable|max:255',
            'facebook' => 'nullable|max:255',
            'youtube' => 'nullable|max:255',
            'tiktok' => 'nullable|max:255',
            'twitter' => 'nullable|max:255',
            'organisation_address_type' => [
                'required',
                Rule::in(OrganisationAddressType::all())
            ],

            'full_address' => 'nullable|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'street_number' => 'nullable|string|max:24',
            'street_name' => 'nullable|string|max:128',
            'city' => 'nullable|string|max:64',
            'state' => 'nullable|string|max:3',
            'postcode' => 'nullable|string|max:6',
            'country' => 'nullable|string|max:24',

            'processing_status' => [
                'required',
                Rule::in(OrganisationStatus::all())
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        if ($request->input('organisation_address_type') === OrganisationAddressType::ONLINE) {
            $validatedData['full_address'] = $request->input('organisation_address_type');
            $validatedData['latitude'] =null;
            $validatedData['longitude'] =null;
            $validatedData['street_number'] =null;
            $validatedData['street_name'] =null;
            $validatedData['city'] =null;
            $validatedData['state'] =null;
            $validatedData['postcode'] =null;
            $validatedData['country'] =null;
        }

        $currentImagePath = $organisation->image; 
        if ($request->hasFile('image')) {
            // if ($currentImagePath && Storage::disk('public')->exists($currentImagePath)) {
            //     Storage::disk('public')->delete($currentImagePath);
            // }
            // $newImagePath = $request->file('image')->store('organisations/images', 'public');
            // $validatedData['image'] = $newImagePath;

            if ($organisation->image && Storage::disk('public')->exists($organisation->image)) {
                Storage::disk('public')->delete($organisation->image);
            }
            // $manager = new ImageManager(new Driver());
            // $file = $request->file('image');
            // $filename = uniqid() . '.webp';
            // $path = 'organisations/images/' . $filename;
            // $absolutePath = storage_path('app/public/' . $path);
            // $image = $manager->read($file->getRealPath());
            // if ($image->width() > 1200) {
            //     $image->scaleDown(1200);
            // }
            // $image->toWebp(80)->save($absolutePath);
            // $validatedData['image'] = $path;

            $manager = new ImageManager(new Driver());
            $file = $request->file('image');
            $path = $this->generateWebpPath(
                $file->getClientOriginalName(),
                'organisations/images'
            );
            $absolutePath = storage_path('app/public/' . $path);
            $image = $manager->read($file->getRealPath());
            if ($image->width() > 1200) {
                $image->scaleDown(1200);
            }
            $image->toWebp(80)->save($absolutePath);
            $validatedData['image'] = $path;

        }elseif ($request->has('remove_image') && $currentImagePath) {
            if (Storage::disk('public')->exists($currentImagePath)) {
                Storage::disk('public')->delete($currentImagePath);
            }
            $validatedData['image'] = null;
        }
        else {
            $validatedData['image'] = $currentImagePath;
        }
        $organisation->update($validatedData);


        $userOrganisationsData = $request->input('user', []);
        $submittedPivotIds = []; 

         foreach ($userOrganisationsData as $rowKey => $orgData) {
            $usere_id = $orgData['id'];
             $pivotId = $orgData['pivot_id'] ?? null;
            $pivotData = [
                'start_date' => $orgData['start_date'] ?? null,
                'end_date' => $orgData['end_date'] ?? null,
                'role' => json_encode($orgData['role'] ?? []),
            ];
            $assignedRoleIds = $orgData['role'] ?? [];
            if ($pivotId) {
                DB::table('organisation_user')
                ->where('id', $pivotId)
                ->where('organisation_assign_id', $organisation->id)
                ->update($pivotData);
                $submittedPivotIds[] = $pivotId;
                $organisationUserInstance = OrganisationUser::find($pivotId);
                if ($organisationUserInstance) {
                    $organisationUserInstance->roles()->sync($assignedRoleIds);
                }

            } else {
                $pivotData['admin_id'] = Auth::id(); 
                $pivotData['user_id'] = null;
                $pivotData['organisation_assign_id'] = $organisation->id; 
                $pivotData['user_assign_id'] = $usere_id; 
                $newOrganisationUser = OrganisationUser::create($pivotData);
                $newOrganisationUser->roles()->sync($assignedRoleIds);

            }
        }

        // foreach ($userOrganisationsData as $rowKey => $orgData) {
        //     $organisationId = $orgData['id'];
        //     $pivotId = $orgData['pivot_id'] ?? null; 
        //     $pivotData = [
        //         'start_date' => $orgData['start_date'] ?? null,
        //         'end_date' => $orgData['end_date'] ?? null,
        //         'role' => json_encode($orgData['role'] ?? []),
        //     ];

        //     if ($pivotId) {
        //         DB::table('organisation_user')
        //             ->where('id', $pivotId)
        //             ->where('organisation_assign_id', $organisation->id)
        //             ->update($pivotData);

        //         $submittedPivotIds[] = $pivotId;
        //     } else {

        //     $pivotData['admin_id'] = Auth::id();
        //     $pivotData['user_id'] =  null;
        //     $organisation->AllassociatedUsers()->attach($organisationId, $pivotData);
        //     }
        // }

        return to_route('organisations.index')->with('status', 'Organisation updated successfully.');
    }

  
    public function destroy(Organisation $organisation)
    {
        if ($organisation->image) {
            Storage::disk('public')->delete($organisation->image);
        }
        $organisation->delete();
        return to_route('organisations.index')->with('status', 'Organisation deleted successfully.');
    }

     public function organisationsuploadcsv()
    {
        // Clear any previous session data if starting fresh
        return view('organisation.organisationsuploadcsv'); // Your initial upload form Blade file
    }

     public function organisationsuploadcsvaction(Request $request)
    {

         $rules = [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ];
        $messages = [
            'csv_file.required' => 'Please select a CSV file to upload.',
            'csv_file.file' => 'The uploaded data is not a valid file.',
            'csv_file.mimes' => 'The uploaded file must be a CSV  file.',
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
            $importedOrganisations = 0;
            $importedOffers = 0;
            $passarray=array();

            DB::beginTransaction();


            // Helper function to download and store images from URL
            $downloadAndStoreImage = function($imageUrl, $rowData) {
                if (empty($imageUrl)) {
                    return null;
                }
                
                try {
                    if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                        Log::warning("CSV Import: Invalid image URL provided: '" . $imageUrl . "' for row: " . json_encode($rowData));
                        return null;
                    }
                    
                    $response = Http::timeout(10)->get($imageUrl);
                    
                    if ($response->successful()) {
                        $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                        if (empty($extension) && $response->header('Content-Type')) {
                            $mimeType = $response->header('Content-Type');
                            $extensionsMap = [
                                'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp',
                            ];
                            $extension = $extensionsMap[$mimeType] ?? null;
                        }
                        $extension = $extension ?: 'jpg'; // Default to jpg

                        $filename = Str::uuid() . '.' . $extension;
                        $fullPath = 'organisations/images/' . $filename;
                        Storage::disk('public')->put($fullPath, $response->body());
                        return $fullPath;
                    } else {
                        Log::warning("CSV Import: Failed to download image from URL '" . $imageUrl . "' (Status: " . $response->status() . ") for row: " . json_encode($rowData));
                        return null;
                    }
                } catch (\Exception $e) {
                    Log::error("CSV Import: Exception while downloading image '" . $imageUrl . "' for row: " . json_encode($rowData) . " - " . $e->getMessage());
                    return null;
                }
            };


            foreach ($records as $record) {
                // First, create or find the Organisation based on MerchantId
                $organisationId = $record['MerchantId'] ?? null;
                $organisationTitle = $record['MerchantCampaignName'] ?? null;
                $organisationAvatar = $record['MerchantAvatar'] ?? null;
                
                if (empty($organisationId)) {
                    $passarray[]=$organisationId;
                    continue; 
                }

               // $imagePath = $downloadAndStoreImage($organisationAvatar, $record);

                $organisation = Organisation::where('merchantid', $organisationId)->first();

                if (!$organisation) {
                   $imagePath = $downloadAndStoreImage($organisationAvatar, $record);
                    // Create the new organisation
                    $organisation = Organisation::create([
                        'title' => $organisationTitle,
                        'image' => $imagePath,
                        'merchantid' => $organisationId,
                        // Set other default values as needed for your table
                        'organisation_address_type' => 'online_only',
                        'processing_status' => 'new',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $importedOrganisations++;
                }



                $importofferid = $record['Id'] ?? null;

                $ExitOffer = Offer::where('importofferid', $importofferid)->first();
                if (!$ExitOffer) {

                    $DateCreated  = $record['DateCreated'] ?? null;
                    $DateModified = $record['DateModified'] ?? null;

                    $DateCreated = (!empty($DateCreated) && strtotime($DateCreated) !== false)
                    ? date('Y-m-d', strtotime($DateCreated))
                    : null;

                    $DateModified = (!empty($DateModified) && strtotime($DateModified) !== false)
                    ? date('Y-m-d', strtotime($DateModified))
                    : null;



                    $rawStart = $record['StartDate'] ?? null;
                    $rawEnd   = $record['EndDate'] ?? null;

                   $startDate = (!empty($rawStart) && strtotime($rawStart) !== false)
                    ? date('Y-m-d', strtotime($rawStart))
                    : null;

                    $endDate = (!empty($rawEnd) && strtotime($rawEnd) !== false)
                    ? date('Y-m-d', strtotime($rawEnd))
                    : null;

                    $no_end_date = $endDate ? 0 : 1;

                 
                 $fullTitle = $record['Name'] ?? $record['MerchantCampaignName'];
                 $title = Str::words($fullTitle, 20, '...');

                // Second, create the Offer and link it to the Organisation
                $offerData = [
                    'id'                      => Str::uuid()->toString(),
                    'organisation_id'         => $organisation->id,
                    'title'                   => $title,
                    'description'             => $record['Description'] ?? null,
                    'custom_redemption_code'  => $record['Code'] ?? null,
                    'custom_redemption_url'   => $record['TargetUrl'] ?? null,
                    'start_date'              => $startDate,
                    'no_end_date'             => $no_end_date,
                    'end_date'                => $endDate,
                    'created_at'              => $DateCreated,
                    'updated_at'              => $DateModified,
                    'redemption_process'      => 'custom',
                    'pricing'                 => 'fixed',
                    'full_price'              => 0.00,
                    'discount_price'          => 0.00,
                    'redemption_limit'        => 0,
                    'offer_address_type'      => 'online_only',
                    'processing_status'       => 'new',
                    'is_featured'             => 'no',
                    'importofferid'           => $importofferid,
                ];
                

               $offer = Offer::create($offerData);
               $offer->categories()->sync('7533386c-0766-4620-a563-e1f8ce6d7d2a');

                $importedOffers++;

                }

            }


            DB::commit();

         

            return redirect()->back()->with('status', "Successfully imported {$importedOrganisations} organisation(s) and {$importedOffers} offer(s).");

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('status', "An error occurred during the import: " . $e->getMessage());
        }

    }

    
}

