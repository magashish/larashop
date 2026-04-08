<?php
namespace App\Http\Controllers;
use App\Models\Offer;
use App\Models\Organisation; // Assuming you have an Organisation model
use App\Models\Category;     // Assuming you have a Category model
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\RedemptionProcessType; // Make sure you have this enum defined
use App\Enums\OrganisationStatus;   // Make sure you have this enum defined
use App\Enums\OfferAddressType; 
use App\Enums\YesNo; 
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Import the Auth facade
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // <--- ADD THIS LINE IF MISSING
use Carbon\Carbon; // For current date
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Log; 

use Illuminate\Support\Facades\Validator; // Don't forget to import this!


use Spatie\ImageOptimizer\OptimizerChainFactory;


use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class OffersController extends Controller
{

   public function __construct()
   {
    $this->middleware(['permission:view offers'])->only('index');
    $this->middleware(['permission:create offers'])->only(['create', 'store']);
    $this->middleware(['permission:edit offers'])->only(['edit', 'update']);
    $this->middleware(['permission:delete offers'])->only('destroy');
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

private function processImageUpload($file, $folder)
{
    $manager = new ImageManager(new Driver());

    // Read image
    $image = $manager->read($file->getRealPath());

    // Resize large images
    if ($image->width() > 1600) {
        $image->scale(width: 1600);
    }

    // Dynamic quality
    $sizeInMB = $file->getSize() / 1024 / 1024;
    $quality = $sizeInMB > 2 ? 60 : 80;

    // Original filename (without extension)
    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

    // Slug-safe filename
    $baseName = Str::slug($originalName);

    $extension = 'webp';
    $filename = $baseName . '.' . $extension;
    $path = $folder . '/' . $filename;

    // Handle duplicate names → image-1.webp, image-2.webp
    $counter = 1;
    while (Storage::disk('public')->exists($path)) {
        $filename = $baseName . '-' . $counter . '.' . $extension;
        $path = $folder . '/' . $filename;
        $counter++;
    }

    // Save WebP
    Storage::disk('public')->put(
        $path,
        $image->toWebp($quality)
    );

    return $path;
}




public function index(Request $request)
{
    // Eager load relationships to avoid N+1 problem
    $today = Carbon::today();
    $searchQuery = $request->input('business');

    // Define the base query to apply the organization search filter
    $baseQuery = Offer::with(['organisation', 'categories']);
    if ($searchQuery) {
        $baseQuery->whereHas('organisation', function ($query) use ($searchQuery) {
            $query->where('title', 'like', '%' . $searchQuery . '%');
        });
    }

    // Now, apply the base query to all your lists before executing them with ->get() or ->paginate()
    $offers = (clone $baseQuery)->latest()->paginate(10);
    
    $offer_new = (clone $baseQuery)
        ->where('processing_status', OrganisationStatus::NEW)
        ->get();

    $offer_approved = (clone $baseQuery)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->get();

    $offer_rejected = (clone $baseQuery)
        ->where('processing_status', OrganisationStatus::REJECTED)
        ->get();
        
    // Get expired offers: end_date is on or before the current moment
    $offer_expire = (clone $baseQuery)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->where('end_date', '<', $today)
        ->get();

    // Get live offers: start_date is on or before the current moment AND end_date is on or after the current moment
    // $live_offers = (clone $baseQuery)
    //     ->where('start_date', '<=', $today)
    //     ->where('processing_status', OrganisationStatus::APPROVED)
    //     ->where('end_date', '>=', $today)
    //     ->get();

    $live_offers = (clone $baseQuery)
        ->where('start_date', '<=', $today)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->where(function($query) use ($today) {
            $query->where('end_date', '>=', $today)
            ->orWhere('no_end_date', 1); 
        })
        ->get();

    return view('offers.index', compact(
        'offers',
        'offer_new',
        'offer_expire',
        'live_offers',
        'offer_approved',
        'offer_rejected'
    ));
}
    // public function index_1()
    // {

    //     $today = Carbon::today();

    //     $offer_new = Offer::with(['organisation', 'categories'])
    //     ->where('processing_status', OrganisationStatus::NEW)
    //     ->get();

    //     $offer_approved = Offer::with(['organisation', 'categories'])
    //     ->where('processing_status', OrganisationStatus::APPROVED)
    //     ->get();

    //     $offer_rejected = Offer::with(['organisation', 'categories'])
    //     ->where('processing_status', OrganisationStatus::REJECTED)
    //     ->get();
        
    //     // Get expired offers: end_date is on or before the current moment
    //     $offer_expire = Offer::with(['organisation', 'categories'])
    //     ->where('processing_status', OrganisationStatus::APPROVED)
    //     ->where('end_date', '<', $today)
    //     ->get();

    //    // Get live offers: start_date is on or before the current moment AND end_date is on or after the current moment
    //     $live_offers = Offer::with(['organisation', 'categories'])
    //     ->where('start_date', '<=', $today)
    //     ->where('processing_status', OrganisationStatus::APPROVED)
    //     ->where('end_date', '>=', $today)
    //     ->get();

    //     $offers = Offer::with(['organisation', 'categories'])->latest()->paginate(10);
    //     return view('offers.index', compact('offers','offer_new','offer_expire','live_offers','offer_approved','offer_rejected'));
    // }

    public function create()
    {
        //$organisations = Organisation::orderBy('title')->pluck('title', 'id','full_address'); 
        $organisations = Organisation::orderBy('title')
                              ->select('id', 'title', 'full_address') 
                              ->get(); 

        $categories = Category::orderBy('name')->get(); 
        return view('offers.create', compact('organisations', 'categories'));
    }

   /**
     * Store a newly created offer in storage.
     */
   public function store_test(Request $request)
   {
        // Define base validation rules for all fields.
    $rules = [
        'title'                       => 'required|string|max:255',
        'custom_redemption_code'      => 'nullable|string|max:255',
        'custom_redemption_description' => 'nullable|string',
        'custom_redemption_url'       => 'nullable|url|max:255',
        'redemption_limit'            => 'required|integer|min:0',
        'full_price'                  => 'nullable|numeric|min:0|max:99999999.99',
        'discount_price'              => 'nullable|numeric|min:0|max:99999999.99',
        'percentage_off'              => 'nullable|numeric|min:0|max:100',
        'custom_saving'               => 'nullable|string|max:255',
        'description'                 => 'nullable|string',
        'terms_conditions'            => 'nullable|string',
        'organisation_id'             => ['required',Rule::exists('organisations', 'id')->where('processing_status', OrganisationStatus::APPROVED),],
        'offer_address_type' => [
            'required',
            Rule::in(OfferAddressType::all())
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
        'start_date'                  => 'nullable|date',
        'no_end_date'                 => ['sometimes', 'boolean'], 
        'end_date'                    => 'nullable|date|after:start_date|required_without:no_end_date',
        'processing_status'           => ['required', Rule::in(OrganisationStatus::all())],
        'is_featured'                 => ['required', Rule::in(YesNo::all())],
        'is_paid_advertisement'       => [Rule::in(YesNo::all())],
        'banner_image'                => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'offer_url'                   => 'nullable|url|max:255',
        'category_ids'                => 'required|nullable|array', 
        'category_ids.*'              => 'exists:categories,id',
        'offer_ongoing_subscription'  => [ Rule::in(['yes', 'no'])],
        'image_files'                   => 'nullable|array',
        'image_files.*'                 => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    $messages = ['organisation_id.exists' => 'This business is not approved yet. Please approve the business before update this offer.'];
    $validatedData = $request->validate($rules, $messages);

    $organisation = Organisation::find($request->input('organisation_id'));
    if($request->input('offer_address_type') === OfferAddressType::USE_BUSSINESS_ADDRESS) {
        $validatedData['full_address'] = $organisation->full_address;
        $validatedData['latitude'] =$organisation->latitude;
        $validatedData['longitude'] =$organisation->longitude;
        $validatedData['street_number'] =$organisation->street_number;
        $validatedData['street_name'] =$organisation->street_name;
        $validatedData['city'] =$organisation->city;
        $validatedData['state'] =$organisation->state;
        $validatedData['postcode'] =$organisation->postcode;
        $validatedData['country'] =$organisation->country;

    }elseif ($request->input('offer_address_type') === OfferAddressType::ONLINE) {
        $validatedData['full_address'] = $request->input('offer_address_type');
        $validatedData['latitude'] =null;
        $validatedData['longitude'] =null;
        $validatedData['street_number'] =null;
        $validatedData['street_name'] =null;
        $validatedData['city'] =null;
        $validatedData['state'] =null;
        $validatedData['postcode'] =null;
        $validatedData['country'] =null;
    }

    // Initialize Spatie Optimizer Chain
    $optimizerChain = OptimizerChainFactory::create();

        // Handle banner image and offer URL based on is_featured status
    if ($request->input('is_featured') === 'yes') {
        if ($request->hasFile('banner_image')) {
            $uploadDirectory = 'offers/banners'; // Custom directory for banners
            $file = $request->file('banner_image');
            $originalFileName = $file->getClientOriginalName();
            $uniqueFileName = $this->getUniqueFileName($originalFileName, $uploadDirectory);
            $path = $file->storeAs($uploadDirectory, $uniqueFileName, 'public');

            // 🌟 OPTIMIZE BANNER IMAGE 🌟
            try {
                $absolutePath = Storage::disk('public')->path($path);
                $optimizerChain->optimize($absolutePath); // Optimizes in place
                \Log::info("Spatie Banner Image optimized successfully: " . $path);

            } catch (\Exception $e) {
                \Log::error("Spatie Banner Image Optimizer failed: " . $e->getMessage());
            }


            $validatedData['banner_image'] = $path; // Store the relative path
        }
        // Save the offer URL
        $validatedData['offer_url'] = $request->input('offer_url');
    } else {
        // If not featured, ensure these fields are null in the database
        $validatedData['banner_image'] = null;
        $validatedData['offer_url'] = null;
    }



     // Get existing image paths (which will be an array because of the cast)
        $existingImageFilePaths =[];
        $updatedImageFilePaths = $existingImageFilePaths;
        $uploadDirectory = 'offers/image_files';
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                $originalFileName = $file->getClientOriginalName();
                $uniqueFileName = $this->getUniqueFileName($originalFileName, $uploadDirectory);
                $path = $file->storeAs($uploadDirectory, $uniqueFileName, 'public');
                // 🌟 OPTIMIZE ATTACHMENT IMAGE 🌟
            try {
                $absolutePath = Storage::disk('public')->path($path);
                $optimizerChain->optimize($absolutePath); // Optimizes in place
            } catch (\Exception $e) {
                \Log::error("Spatie Attachment Image Optimizer failed: " . $e->getMessage());
            }
                $updatedImageFilePaths[] = $path; 
            }
        }

       // $validatedData['image_file'] = array_values($updatedImageFilePaths); 



    $validatedData['admin_id'] = Auth::id(); 
    $offer = Offer::create($validatedData);
    if (isset($validatedData['category_ids'])) {
        $offer->categories()->sync($validatedData['category_ids']);
    } else {
        $offer->categories()->sync([]);
    }

    foreach ($updatedImageFilePaths as $filePath) {
        $offer->attachments()->create([ 
            'file_image' => $filePath,
        ]);
    }

    return redirect()->route('offers.index')->with('status', 'Offer created successfully!');
}



    public function store(Request $request)
    {
        $rules = [
            'title'                       => 'required|string|max:255',
            'custom_redemption_code'      => 'nullable|string|max:255',
            'custom_redemption_description' => 'nullable|string',
            'custom_redemption_url'       => 'nullable|url|max:255',
            'redemption_limit'            => 'required|integer|min:0',
            'full_price'                  => 'nullable|numeric|min:0|max:99999999.99',
            'discount_price'              => 'nullable|numeric|min:0|max:99999999.99',
            'percentage_off'              => 'nullable|numeric|min:0|max:100',
            'custom_saving'               => 'nullable|string|max:255',
            'description'                 => 'nullable|string',
            'terms_conditions'            => 'nullable|string',
            'organisation_id'             => ['required',Rule::exists('organisations', 'id')->where('processing_status', OrganisationStatus::APPROVED),],
            'offer_address_type'          => ['required', Rule::in(OfferAddressType::all())],
            'full_address'                => 'nullable|max:255',
            'latitude'                    => 'nullable|numeric|between:-90,90',
            'longitude'                   => 'nullable|numeric|between:-180,180',
            'street_number'               => 'nullable|string|max:24',
            'street_name'                 => 'nullable|string|max:128',
            'city'                        => 'nullable|string|max:64',
            'state'                       => 'nullable|string|max:3',
            'postcode'                    => 'nullable|string|max:6',
            'country'                     => 'nullable|string|max:24',
            'start_date'                  => 'nullable|date',
            'no_end_date'                 => ['sometimes', 'boolean'], 
            'end_date'                    => 'nullable|date|after:start_date|required_without:no_end_date',
            'processing_status'           => ['required', Rule::in(OrganisationStatus::all())],
            'is_featured'                 => ['required', Rule::in(YesNo::all())],
            'is_paid_advertisement'       => [Rule::in(YesNo::all())],
            'banner_image'                => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'offer_url'                   => 'nullable|url|max:255',
            'category_ids'                => 'required|nullable|array',
            'category_ids.*'              => 'exists:categories,id',
            'offer_ongoing_subscription'  => [Rule::in(['yes', 'no'])],
            'image_files'                 => 'nullable|array',
            'image_files.*'               => 'image|mimes:jpeg,png,jpg,gif,svg',
        ];

        $messages = ['organisation_id.exists' => 'This business is not approved yet. Please approve the business before update this offer.'];
        $validatedData = $request->validate($rules, $messages);

        $organisation = Organisation::find($request->input('organisation_id'));
        if ($request->input('offer_address_type') === OfferAddressType::USE_BUSSINESS_ADDRESS) {
            $validatedData = array_merge($validatedData, [
                'full_address' => $organisation->full_address,
                'latitude' => $organisation->latitude,
                'longitude' => $organisation->longitude,
                'street_number' => $organisation->street_number,
                'street_name' => $organisation->street_name,
                'city' => $organisation->city,
                'state' => $organisation->state,
                'postcode' => $organisation->postcode,
                'country' => $organisation->country,
            ]);
        } elseif ($request->input('offer_address_type') === OfferAddressType::ONLINE) {
            $validatedData = array_merge($validatedData, [
                'full_address' => $request->input('offer_address_type'),
                'latitude' => null,
                'longitude' => null,
                'street_number' => null,
                'street_name' => null,
                'city' => null,
                'state' => null,
                'postcode' => null,
                'country' => null,
            ]);
        }


        if ($request->input('is_featured') === 'yes' && $request->hasFile('banner_image')) {
            $uploadDirectory = 'offers/banners';
            $validatedData['banner_image'] = $this->processImageUpload($request->file('banner_image'), $uploadDirectory);
            $validatedData['offer_url'] = $request->input('offer_url');

        } else {
            $validatedData['banner_image'] = null;
            $validatedData['offer_url'] = null;
        }

        $updatedImageFilePaths = [];
        $uploadDirectory = 'offers/image_files';
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                $updatedImageFilePaths[] = $this->processImageUpload($file, $uploadDirectory);
            }
        }

        $validatedData['admin_id'] = Auth::id();
        $validatedData['no_end_date'] = $request->has('no_end_date');

        $offer = Offer::create($validatedData);

        $offer->categories()->sync($validatedData['category_ids'] ?? []);

        foreach ($updatedImageFilePaths as $filePath) {
            $offer->attachments()->create(['file_image' => $filePath]);
        }

        return redirect()->route('offers.index')->with('status', 'Offer created successfully!');
    }




    /**
     * Display the specified resource.
     */
    public function show(Offer $offers)
    {
    }




    // public function uploadcsv()
    // {
    //     return view('offers.uploadcsv');
    // }

    // public function uploadcsvaction(Request $request)
    // {
    //     $rules = [
    //         'csv_file' => 'required|file|mimes:csv|max:10240',
    //     ];
    //     $messages = [
    //         'csv_file.required' => 'Please select a CSV file to upload.',
    //         'csv_file.file' => 'The uploaded data is not a valid file.',
    //         'csv_file.mimes' => 'The uploaded file must be a CSV or TXT file.',
    //         'csv_file.max' => 'The CSV file size cannot exceed 10 MB.',
    //     ];
    //     $validator = Validator::make($request->all(), $rules, $messages);
    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator)->withInput();     
    //     }
    //     $file = $request->file('csv_file');
    //     if (!$file || !$file->isValid()) {
    //         return redirect()->back()->with('error', 'There was an issue with the uploaded file.');
    //     }

    // }


    public function uploadcsv()
    {
        // Clear any previous session data if starting fresh
        Session::forget(['csv_upload_path', 'csv_headers', 'csv_preview', 'csv_validation_errors']);
        return view('offers.upload_csv_form'); // Your initial upload form Blade file
    }

    /**
     * Stage 1.5: Handle the CSV file upload.
     * POST /offers/uploadcsv
     * Redirects to mapping page on success.
     */
    public function uploadcsvaction(Request $request)
    {
        $rules = [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ];
        $messages = [
            'csv_file.required' => 'Please select a CSV file to upload.',
            'csv_file.file' => 'The uploaded data is not a valid file.',
            'csv_file.mimes' => 'The uploaded file must be a CSV or TXT file.',
            'csv_file.max' => 'The CSV file size cannot exceed 10 MB.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput(); 
        }
        $file = $request->file('csv_file');
        $path = $file->store('temp_csv_imports'); 
        $fullPath = Storage::path($path);
        $csvHeaders = [];
        $csvPreview = [];
        $rowNum = 0;
        if (($handle = fopen($fullPath, "r")) !== FALSE) {
            while (($row = fgetcsv($handle)) !== FALSE) {
                $row = array_filter($row, fn($value) => !is_null($value) && $value !== '');
                if (empty($row)) continue;
                if ($rowNum === 0) {
                    $csvHeaders = $row; 
                } elseif ($rowNum <= 5) { 
                    $csvPreview[] = $row;
                }
                $rowNum++;
                if ($rowNum > 5) break;
            }
            fclose($handle);
        } else {
            Storage::delete($path); // Clean up temp file
            return redirect()->back()->with('error', 'Could not read the uploaded CSV file. Please check its format.');
        } 
        if (empty($csvHeaders)) {
            Storage::delete($path);
            return redirect()->back()->with('error', 'The uploaded CSV file appears to be empty or does not have a header row.');
        }
        Session::put('csv_upload_path', $path);
        Session::put('csv_headers', $csvHeaders);
        Session::put('csv_preview', $csvPreview);
        return redirect()->route('offers.map_csv_fields');
    }

    public function showCsvMappingForm()
    {
        $actualCsvHeaders = Session::get('csv_headers');
        $csvPreview = Session::get('csv_preview');
        if (!$actualCsvHeaders || !Session::has('csv_upload_path')) {
            return redirect()->route('offers.uploadcsv')->with('error', 'Please upload a CSV file first.');
        }
        $systemFields =systemFields();
        $organisations = Organisation::orderBy('title')->pluck('title', 'id'); 
        $categories = Category::orderBy('name')->get(); 
        return view('offers.map_csv_fields', compact('actualCsvHeaders', 'csvPreview', 'systemFields','organisations','categories'));
    }



    public function importCsv(Request $request)
    {
        $systemFields =systemFields();
        $dateFormats = [
            'd/m/Y',   
            'Y-m-d',    
            'm/d/Y',    
            'd-m-Y',   
            'Y/m/d',   
            'm-d-Y'
        ];
        $mappableDbColumns = array_keys($systemFields); 

        $rules = [
            'mapping' => 'required|array',
            'mapping.*' => ['nullable', 'string'], 
            'mapping.title' => 'required', 
        ];
        $messages = [
            'mapping.required' => 'Please select field mappings for all system fields.',
            'mapping.title.required' => 'The "Title" field must be mapped to a CSV column.',
        ];


        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $mapping = $request->input('mapping'); 
        $csvPath = Session::get('csv_upload_path');

        if (!Storage::exists($csvPath)) {
            return redirect()->route('offers.uploadcsv')->with('error', 'Uploaded CSV file not found or session expired. Please re-upload.');
        }

        $fullPath = Storage::path($csvPath);
        $importedRowCount = 0;
        $failedRows = [];



        $defaultImagePath =null;
        $uploadDirectory = 'offers/image_files';

          // --- Handle New Image Uploads with Custom Naming ---
        if ($request->hasFile('image_files')) {
            $file = $request->file('image_files');
            $originalFileName = $file->getClientOriginalName();
            $uniqueFileName = $this->getUniqueFileName($originalFileName, $uploadDirectory);
            $path = $file->storeAs($uploadDirectory, $uniqueFileName, 'public');
            $defaultImagePath = $path; 
        }

        if (($handle = fopen($fullPath, "r")) !== FALSE) {
            $headerRow = fgetcsv($handle); 
            $actualCsvHeaderMap = array_flip($headerRow); 
            while (($rowData = fgetcsv($handle)) !== FALSE) {
                if (count($rowData) !== count($headerRow)) {
                    $failedRows[] = ['row_data' => $rowData, 'reason' => 'Mismatch in column count.'];
                    continue;
                }
                $offerData = [];
                foreach ($mapping as $dbColumn => $actualCsvHeaderName) {
                    if (array_key_exists($dbColumn, $systemFields) && !empty($actualCsvHeaderName) && isset($actualCsvHeaderMap[$actualCsvHeaderName])) {
                        $csvColumnIndex = $actualCsvHeaderMap[$actualCsvHeaderName];
                        $offerData[$dbColumn] = $rowData[$csvColumnIndex];
                    }
                }

                $offerData['organisation_id'] = $request->input('organisation_id'); 
                $offerData['category_ids'] = $request->input('category_ids');
                $offerData['discount_price'] = (float) ($offerData['discount_price'] ?? 0.00);
                $offerData['percentage_off'] = (float) ($offerData['percentage_off'] ?? 0.00);

                // Helper function to parse dates
                $parseDate = function($dateString, $formats) use ($rowData) {
                    if (empty($dateString)) {
                        return null;
                    }
                    foreach ($formats as $format) {
                        try {
                            return Carbon::createFromFormat($format, $dateString)->toDateString();
                        } catch (\Exception $e) {
                        }
                    }
                    try {
                        return Carbon::parse($dateString)->toDateString();
                    } catch (\Exception $e) {
                        Log::warning("CSV Import: Failed to parse date '" . $dateString . "' after trying multiple formats for row: " . json_encode($rowData) . " - " . $e->getMessage());
                        return null; 
                    }
                };

                // Helper function to download and store images from URL
                $downloadAndStoreImage = function($imageUrl, $storageDisk, $storagePath) use ($rowData) { 
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
                    $fullPath = $storagePath . '/' . $filename;
                    Storage::disk($storageDisk)->put($fullPath, $response->body());
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
        $offerData['start_date'] = $parseDate($offerData['start_date'] ?? null, $dateFormats);
        $offerData['end_date'] = $parseDate($offerData['end_date'] ?? null, $dateFormats);


        $imageStorageDisk = 'public';
        $imageStoragePath = 'offers/image_files';
        $existingImageFilePaths = [];


        $downloadedImagePath = $downloadAndStoreImage($offerData['image'] ?? null, $imageStorageDisk, $imageStoragePath);
        $finalSingleImagePath = !empty($downloadedImagePath) ? $downloadedImagePath : $defaultImagePath;

        if (!empty($finalSingleImagePath)) {
            $offerData['image_file'] = array_values([$finalSingleImagePath]);
        } else {
            $offerData['image_file'] = null; 
        }




        if (empty($offerData['custom_redemption_url']) && empty($offerData['custom_redemption_code'])) {
            $failedRows[] = ['row_data' => $rowData, 'reason' => 'Either Coupon URL or Coupon Code is required.'];
            continue;
        }
        try {
            $offerData['admin_id'] = Auth::id(); 
            $offer = Offer::create($offerData);
            if (isset($offerData['category_ids'])) {
                $offer->categories()->sync($offerData['category_ids']);
            } else {
                $offer->categories()->sync([]);
            }

            $importedRowCount++;
        } catch (\Exception $e) {
            $failedRows[] = ['row_data' => $rowData, 'reason' => $e->getMessage()];
        }
    }
    fclose($handle);
}



Storage::delete($csvPath);
Session::forget(['csv_upload_path', 'csv_headers', 'csv_preview']);
if (!empty($failedRows)) {
    Session::flash('import_failed_rows', $failedRows);
    return redirect()->route('offers.index')->with('status', "CSV import completed with {$importedRowCount} offers imported, but some rows failed. See details or check logs.");
}

return redirect()->route('offers.index')->with('status', "CSV import complete! {$importedRowCount} offers imported successfully.");
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offer $offer) // Corrected type hint
    {
        
       // $organisations = Organisation::pluck('title', 'id');
        $organisations = Organisation::orderBy('title')
                              ->select('id', 'title', 'full_address') 
                              ->get(); 
        $categories = Category::all();
        $organisationStatuses = OrganisationStatus::labels();
        $yesNoOptions = YesNo::labels();
        $selectedCategoryIds = $offer->categories->pluck('id')->toArray();
        $currentorganisation = Organisation::find($offer->organisation_id);

        return view('offers.edit', compact(
            'offer',
            'organisations',
            'categories',
            'organisationStatuses',
            'yesNoOptions',
            'selectedCategoryIds',
            'currentorganisation'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified offer in storage.
     */
    public function update(Request $request, Offer $offer) // Corrected type hint
    {


        // Define base validation rules for all fields.
        $rules = [
            'title'                       => 'required|string|max:255',
            'custom_redemption_code'      => 'nullable|string|max:255',
            'custom_redemption_description' => 'nullable|string',
            'custom_redemption_url'       => 'nullable|url|max:255',
            'redemption_limit'            => 'integer|min:0',
            'full_price'                  => 'nullable|numeric|min:0|max:99999999.99',
            'discount_price'              => 'nullable|numeric|min:0|max:99999999.99',
            'percentage_off'              => 'nullable|numeric|min:0|max:100',
            'custom_saving'               => 'nullable|string|max:255',
            'description'                 => 'nullable|string',
            'terms_conditions'            => 'nullable|string',
            'organisation_id'             => ['required',Rule::exists('organisations', 'id')->where('processing_status', OrganisationStatus::APPROVED),],
            'offer_address_type'          => ['required',Rule::in(OfferAddressType::all())],
            'full_address' => 'nullable|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'street_number' => 'nullable|string|max:24',
            'street_name' => 'nullable|string|max:128',
            'city' => 'nullable|string|max:64',
            'state' => 'nullable|string|max:3',
            'postcode' => 'nullable|string|max:6',
            'country' => 'nullable|string|max:24',
            'start_date'                  => 'required|date',
            'no_end_date'                 => ['sometimes', 'boolean'], 
            'end_date'                    => 'nullable|date|after:start_date|required_without:no_end_date',
            'processing_status'           => ['required', Rule::in(OrganisationStatus::all())],
            'is_featured'                 => ['required', Rule::in(YesNo::all())],
            'is_paid_advertisement'       => [Rule::in(YesNo::all())],
            'banner_image'                => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'offer_url'                   => 'nullable|url|max:255',
            'category_ids'                => 'required|nullable|array', 
            'category_ids.*'              => 'exists:categories,id',
            'offer_ongoing_subscription'  => [ Rule::in(['yes', 'no'])],
            'image_files'                   => 'nullable|array',
            'image_files.*'                 => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];


        $messages = ['organisation_id.exists' => 'This business is not approved yet. Please approve the business before update this offer.'];
        $validatedData = $request->validate($rules, $messages);
        
        $organisation = Organisation::find($request->input('organisation_id'));


        // if($request->input('offer_address_type') === OfferAddressType::USE_BUSSINESS_ADDRESS) {
        //     $validatedData['full_address'] = $organisation->full_address;
        // }elseif ($request->input('offer_address_type') === OfferAddressType::ONLINE) {
        //     $validatedData['full_address'] = $request->input('offer_address_type');
        // }


        if($request->input('offer_address_type') === OfferAddressType::USE_BUSSINESS_ADDRESS) {
            
            $validatedData['full_address'] = $organisation->full_address;
            $validatedData['latitude'] =$organisation->latitude;
            $validatedData['longitude'] =$organisation->longitude;
            $validatedData['street_number'] =$organisation->street_number;
            $validatedData['street_name'] =$organisation->street_name;
            $validatedData['city'] =$organisation->city;
            $validatedData['state'] =$organisation->state;
            $validatedData['postcode'] =$organisation->postcode;
            $validatedData['country'] =$organisation->country;

        }elseif ($request->input('offer_address_type') === OfferAddressType::ONLINE) {
            $validatedData['full_address'] = $request->input('offer_address_type');
            $validatedData['latitude'] =null;
            $validatedData['longitude'] =null;
            $validatedData['street_number'] =null;
            $validatedData['street_name'] =null;
            $validatedData['city'] =null;
            $validatedData['state'] =null;
            $validatedData['postcode'] =null;
            $validatedData['country'] =null;
        }

// Initialize Spatie Optimizer Chain
    $optimizerChain = OptimizerChainFactory::create();
    // Handle banner image and offer URL based on is_featured status
    if ($request->input('is_featured') === 'yes') {
        if ($request->hasFile('banner_image')) {
            if ($offer->banner_image) {
                Storage::disk('public')->delete($offer->banner_image);
            }
            $uploadDirectory = 'offers/banners';
            $validatedData['banner_image'] = $this->processImageUpload($request->file('banner_image'), $uploadDirectory);

        } else if ($request->has('remove_banner_image')) {
            if ($offer->banner_image) {
                Storage::disk('public')->delete($offer->banner_image);
                $validatedData['banner_image'] = null;
            }
        }
        $validatedData['offer_url'] = $request->input('offer_url');
    } else {
        if ($offer->banner_image) {
            Storage::disk('public')->delete($offer->banner_image);
        }
        $validatedData['banner_image'] = null;
        $validatedData['offer_url'] = null;
    }


   


         // --- Handle Attachments Deletion based on remove_image_files[] ---
            $attachmentsToRemoveIds = $request->input('remove_image_files') ?? [];
            if (!empty($attachmentsToRemoveIds)) {
                $attachmentsToDelete = $offer->attachments()
                                            ->whereIn('id', $attachmentsToRemoveIds)
                                            ->get();
                foreach ($attachmentsToDelete as $attachment) {
                    if (Storage::disk('public')->exists($attachment->file_image)) {
                        Storage::disk('public')->delete($attachment->file_image);
                    }
                    $attachment->delete();
                }
            }
            // --- Handle New Image Uploads ---


            $uploadDirectory = 'offers/image_files';
            if ($request->hasFile('image_files')) {
                foreach ($request->file('image_files') as $file) {
                    $path = $this->processImageUpload($file, $uploadDirectory);
                    $offer->attachments()->create([
                        'file_image' => $path,
                    ]);
                }
            }
        


            // $uploadDirectory = 'offers/image_files';
            // if ($request->hasFile('image_files')) {
            //     foreach ($request->file('image_files') as $file) {
            //         $originalFileName = $file->getClientOriginalName();
            //         $uniqueFileName = $this->getUniqueFileName($originalFileName, $uploadDirectory);
            //         $path = $file->storeAs($uploadDirectory, $uniqueFileName, 'public');
            //          try {
            //     $absolutePath = Storage::disk('public')->path($path);
            //     $optimizerChain->optimize($absolutePath); // Optimizes in place
            // } catch (\Exception $e) {
            //     \Log::error("Spatie Attachment Image Optimizer failed: " . $e->getMessage());
            // }
            //         $offer->attachments()->create([
            //             'file_image' => $path,
            //         ]);
            //     }
            // }



        //$validatedData['image_file'] = array_values($updatedImageFilePaths); 


       $validatedData['no_end_date'] = $request->has('no_end_date');

        $offer->update($validatedData);
        // Sync categories
        if (isset($validatedData['category_ids'])) {
            $offer->categories()->sync($validatedData['category_ids']);
        } else {
            $offer->categories()->sync([]);
        }
        return redirect()->route('offers.index')->with('status', 'Offer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        // --- Changes for Multiple Images ---
        // Delete all associated image files from storage when an offer is deleted
        if (!empty($offer->image_file)) {
            foreach ($offer->image_file as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }
        // --- End Changes ---

        $offer->delete();
        return redirect()->route('offers.index')->with('status', 'Offer deleted successfully!');
    }
}
