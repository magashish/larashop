<?php
namespace App\Http\Controllers\Business;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Organisation; 
use App\Models\OrganisationUser;
use App\Models\Category;     
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\RedemptionProcessType; 
use App\Enums\OrganisationStatus;   
use App\Enums\OfferAddressType; 
use App\Enums\YesNo; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon; 
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BusinessOffersController extends Controller
{


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
    $user = Auth::user();
    $currentUserId = $user->id;
    $userLevel = $user->level;
    $today = Carbon::today()->toDateString();
    $requiredRoleIds = ["6"]; 

    // ---------------------------------------------------
    // Determine accessible organisation & user ids
    // ---------------------------------------------------
    if ($userLevel === 'business') {

        $organisationIds = Organisation::where('user_id', $currentUserId)
            ->orWhere('parent_id', $currentUserId)
            ->pluck('id');

        $userIdForOffers = [$currentUserId];

        $assignedUsers = OrganisationUser::pluck('user_assign_id')
            ->toArray();

        $userIdForOffers = array_merge($userIdForOffers, $assignedUsers);
    }
    elseif ($userLevel === 'sub_business') {

        $organisationIds = Organisation::whereHas('ApproveassociatedUsers', function ($q) use ($currentUserId, $requiredRoleIds, $today) {
                $q->where('user_assign_id', $currentUserId)
                  ->whereJsonContains('organisation_user.role', $requiredRoleIds)
                  ->where(function ($qDates) use ($today) {
                        $qDates->whereNull('start_date')->orWhere('start_date', '<=', $today);
                  })
                  ->where(function ($qDates) use ($today) {
                        $qDates->whereNull('end_date')->orWhere('end_date', '>=', $today);
                  });
            })
            ->pluck('id');

        $ownerId = $user->parent_id;

        $assignedUsers = OrganisationUser::whereJsonContains('role', $requiredRoleIds)
            ->pluck('user_assign_id')
            ->toArray();

        $userIdForOffers = array_merge([$ownerId], $assignedUsers);
    }

    // ---------------------------------------------------
    // Offers Queries
    // ---------------------------------------------------
    // $offer_new = Offer::with(['organisation', 'categories'])
    //     ->whereIn('organisation_id', $organisationIds)
    //     ->whereIn('user_id', $userIdForOffers)
    //     ->where('processing_status', OrganisationStatus::NEW)
    //     ->get();

    // $offer_approved = Offer::with(['organisation', 'categories'])
    //     ->whereIn('organisation_id', $organisationIds)
    //     ->whereIn('user_id', $userIdForOffers)
    //     ->where('processing_status', OrganisationStatus::APPROVED)
    //     ->get();

    // $offer_rejected = Offer::with(['organisation', 'categories'])
    //     ->whereIn('organisation_id', $organisationIds)
    //     ->whereIn('user_id', $userIdForOffers)
    //     ->where('processing_status', OrganisationStatus::REJECTED)
    //     ->get();

    // $offer_expire = Offer::with(['organisation', 'categories'])
    //     ->whereIn('organisation_id', $organisationIds)
    //     ->whereIn('user_id', $userIdForOffers)
    //     ->where('processing_status', OrganisationStatus::APPROVED)
    //     ->where('end_date', '<', $today)
    //     ->get();

    // $live_offers = Offer::with(['organisation', 'categories'])
    //     ->whereIn('organisation_id', $organisationIds)
    //     ->whereIn('user_id', $userIdForOffers)
    //     ->where('processing_status', OrganisationStatus::APPROVED)
    //     ->where('start_date', '<=', $today)
    //     ->where(function ($query) use ($today) {
    //         $query->where('end_date', '>=', $today)
    //               ->orWhere('no_end_date', 1);
    //     })
    //     ->get();

    // $offers = Offer::with(['organisation', 'categories'])
    //     ->whereIn('organisation_id', $organisationIds)
    //     ->whereIn('user_id', $userIdForOffers)
    //     ->latest()
    //     ->paginate(10);


    // Define the condition once for clarity
$isSubBusiness = $user->level === 'sub_business';

$offer_new = Offer::with(['organisation', 'categories'])
    ->whereIn('organisation_id', $organisationIds)
    ->when($isSubBusiness, function ($query) use ($userIdForOffers) {
        return $query->whereIn('user_id', $userIdForOffers);
    })
    ->where('processing_status', OrganisationStatus::NEW)
    ->get();

$offer_approved = Offer::with(['organisation', 'categories'])
    ->whereIn('organisation_id', $organisationIds)
    ->when($isSubBusiness, function ($query) use ($userIdForOffers) {
        return $query->whereIn('user_id', $userIdForOffers);
    })
    ->where('processing_status', OrganisationStatus::APPROVED)
    ->get();

$offer_rejected = Offer::with(['organisation', 'categories'])
    ->whereIn('organisation_id', $organisationIds)
    ->when($isSubBusiness, function ($query) use ($userIdForOffers) {
        return $query->whereIn('user_id', $userIdForOffers);
    })
    ->where('processing_status', OrganisationStatus::REJECTED)
    ->get();

$offer_expire = Offer::with(['organisation', 'categories'])
    ->whereIn('organisation_id', $organisationIds)
    ->when($isSubBusiness, function ($query) use ($userIdForOffers) {
        return $query->whereIn('user_id', $userIdForOffers);
    })
    ->where('processing_status', OrganisationStatus::APPROVED)
    ->where('end_date', '<', $today)
    ->get();

$live_offers = Offer::with(['organisation', 'categories'])
    ->whereIn('organisation_id', $organisationIds)
    ->when($isSubBusiness, function ($query) use ($userIdForOffers) {
        return $query->whereIn('user_id', $userIdForOffers);
    })
    ->where('processing_status', OrganisationStatus::APPROVED)
    ->where('start_date', '<=', $today)
    ->where(function ($query) use ($today) {
        $query->where('end_date', '>=', $today)
              ->orWhere('no_end_date', 1);
    })
    ->get();

$offers = Offer::with(['organisation', 'categories'])
    ->whereIn('organisation_id', $organisationIds)
    ->when($isSubBusiness, function ($query) use ($userIdForOffers) {
        return $query->whereIn('user_id', $userIdForOffers);
    })
    ->latest()
    ->paginate(10);

    return view('business.offers.index', compact(
        'offers',
        'offer_new',
        'offer_expire',
        'live_offers',
        'offer_approved',
        'offer_rejected'
    ));
}



public function index_old()
{
    $user = Auth::user();
    $currentUserId = $user->id;
    $userLevel = $user->level;
    $today = Carbon::today();
    $requiredRoleIds = ["6"]; // admin / business

    // ---------------------------------------------------
    // Determine accessible organisation & user ids
    // ---------------------------------------------------
    if ($userLevel === 'business') {

        // Organisations owned by business user
        $organisationIds = Organisation::where('user_id', $currentUserId)
            ->orWhere('parent_id', $currentUserId)
            ->pluck('id');

        // users for offers
        $userIdForOffers = [$currentUserId];

        // assigned sub users
        $assignedUsers = OrganisationUser::whereJsonContains('role', $requiredRoleIds)
            ->pluck('user_assign_id')
            ->toArray();

        $userIdForOffers = array_merge($userIdForOffers, $assignedUsers);
    }
    elseif ($userLevel === 'sub_business') {

        $organisationIds = Organisation::whereHas('ApproveassociatedUsers', function ($q) use ($currentUserId, $requiredRoleIds) {
                $q->where('user_assign_id', $currentUserId)
                  ->whereJsonContains('organisation_user.role', $requiredRoleIds);
            })
            ->pluck('id');

        $ownerId = $user->parent_id;

        $assignedUsers = OrganisationUser::whereJsonContains('role', $requiredRoleIds)
            ->pluck('user_assign_id')
            ->toArray();

        $userIdForOffers = array_merge([$ownerId], $assignedUsers);
    }

    // ---------------------------------------------------
    // Use whereIn for multiple user IDs
    // ---------------------------------------------------
    $offer_new = Offer::with(['organisation', 'categories'])
        ->whereIn('organisation_id', $organisationIds)
        ->whereIn('user_id', $userIdForOffers)
        ->where('processing_status', OrganisationStatus::NEW)
        ->get();

    $offer_approved = Offer::with(['organisation', 'categories'])
        ->whereIn('organisation_id', $organisationIds)
        ->whereIn('user_id', $userIdForOffers)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->get();

    $offer_rejected = Offer::with(['organisation', 'categories'])
        ->whereIn('organisation_id', $organisationIds)
        ->whereIn('user_id', $userIdForOffers)
        ->where('processing_status', OrganisationStatus::REJECTED)
        ->get();

    $offer_expire = Offer::with(['organisation', 'categories'])
        ->whereIn('organisation_id', $organisationIds)
        ->whereIn('user_id', $userIdForOffers)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->where('end_date', '<', $today)
        ->get();

    $live_offers = Offer::with(['organisation', 'categories'])
        ->whereIn('organisation_id', $organisationIds)
        ->whereIn('user_id', $userIdForOffers)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->where('start_date', '<=', $today)
        ->where(function ($query) use ($today) {
            $query->where('end_date', '>=', $today)
                  ->orWhere('no_end_date', 1);
        })
        ->get();

    $offers = Offer::with(['organisation', 'categories'])
        ->whereIn('organisation_id', $organisationIds)
        ->whereIn('user_id', $userIdForOffers)
        ->latest()
        ->paginate(10);

    return view('business.offers.index', compact(
        'offers',
        'offer_new',
        'offer_expire',
        'live_offers',
        'offer_approved',
        'offer_rejected'
    ));
}



        // public function create()
        // {
        //  $user = Auth::user();
        //  $userLevel = $user->level;
        //  $currentUserId = $user->id;

        //  if ($userLevel === 'business') {
        //     $userId = $currentUserId;
        // } elseif ($userLevel === 'sub_business') {
        //     $userId = $user->parent_id;
        // }
        //     //$organisations = Organisation::where('user_id', $userId)->orderBy('title')->pluck('title', 'id');
        //     $organisations = Organisation::where('user_id', $userId)
        //     ->orderBy('title')
        //     ->select('id', 'title', 'full_address') 
        //     ->get(); 
        //     $categories = Category::orderBy('name')->get(); 
        //     return view('business.offers.create', compact('organisations', 'categories'));
        // }


public function create()
{
    $user = Auth::user();
    $userLevel = $user->level;
    $currentUserId = $user->id;
    $today = Carbon::today()->toDateString();
    $requiredRoleIds = ["6"]; // role id allowed to create offers

    // --------------------------------------------------------------
    // BUSINESS USER - show all their organisations (NO DATE CHECK)
    // --------------------------------------------------------------
    if ($userLevel === 'business') {

        $organisations = Organisation::where('user_id', $currentUserId)
            ->orderBy('title')
            ->select('id', 'title', 'full_address')
            ->get();
    }

    // --------------------------------------------------------------
    // SUB BUSINESS USER - MUST BE ASSIGNED & MUST HAVE PERMISSION
    // WITH DATE CHECK ALSO
    // --------------------------------------------------------------
    elseif ($userLevel === 'sub_business') {

        $organisations = Organisation::whereHas('ApproveassociatedUsers', function ($q) use ($currentUserId, $requiredRoleIds, $today) {

                // must be assigned to org
                $q->where('user_assign_id', $currentUserId)

                // must include role 6 (admin role permission)
                  ->where(function ($q2) use ($requiredRoleIds) {
                       foreach ($requiredRoleIds as $roleId) {
                           $q2->orWhereJsonContains('organisation_user.role', $roleId);
                       }
                   })

                // date validation
                  ->where(function ($q3) use ($today) {
                      $q3->whereNull('start_date')->orWhere('start_date', '<=', $today);
                  })
                  ->where(function ($q4) use ($today) {
                      $q4->whereNull('end_date')->orWhere('end_date', '>=', $today);
                  });
            })
            ->orderBy('title')
            ->select('id', 'title', 'full_address')
            ->get();
             if ($organisations->isEmpty()) {
        abort(403, "You don't have permission to create offer for any organisation.");
    }
    }

    $categories = Category::orderBy('name')->get();

    return view('business.offers.create', compact('organisations', 'categories'));
}





   /**
     * Store a newly created offer in storage.
     */


    public function store(Request $request)
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
        'organisation_id'             => 'required|exists:organisations,id',
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
        'start_date'                  => 'required|date',
        'no_end_date'                 => ['sometimes', 'boolean'], 
        'end_date'                    => 'nullable|date|after:start_date|required_without:no_end_date',
        'is_featured'                 => ['required', Rule::in(YesNo::all())],
        'is_paid_advertisement'       => [Rule::in(YesNo::all())],
        'banner_image'                => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'offer_url'                   => 'nullable|url|max:255',
        'category_ids'                => 'required|nullable|array', 
        'category_ids.*'              => 'exists:categories,id',
        'offer_ongoing_subscription'  => [ Rule::in(['yes', 'no'])],
    ];

    $validatedData = $request->validate($rules);

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


    

        // Handle banner image and offer URL based on is_featured status
    if ($request->input('is_featured') === 'yes') {
        if ($request->hasFile('banner_image')) {
            $uploadDirectory = 'offers/banners'; // Custom directory for banners
            $file = $request->file('banner_image');
            $originalFileName = $file->getClientOriginalName();
            $uniqueFileName = $this->getUniqueFileName($originalFileName, $uploadDirectory);
            $path = $file->storeAs($uploadDirectory, $uniqueFileName, 'public');
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
                $updatedImageFilePaths[] = $path; 
            }
        }

       // $validatedData['image_file'] = array_values($updatedImageFilePaths); 



    $validatedData['user_id'] = Auth::id(); 
    
    $user = Auth::user();
    $userLevel = $user->level;
    if ($userLevel === 'sub_business') {
        $validatedData['parent_id'] = $user->parent_id;
    }



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

    return redirect()->route('business.offers.index')->with('status', 'Offer created successfully!');
}


//    public function store_2(Request $request)
//    {
//         // Define base validation rules for all fields.
//     // $rules = [
//     //     'title'                       => 'required|string|max:255',
//     //     'custom_redemption_code'      => 'nullable|string|max:255',
//     //     'custom_redemption_description' => 'nullable|string',
//     //     'custom_redemption_url'       => 'nullable|url|max:255',
//     //     'redemption_limit'            => 'required|integer|min:0',
//     //     'full_price'                  => 'nullable|numeric|min:0|max:99999999.99',
//     //     'discount_price'              => 'nullable|numeric|min:0|max:99999999.99',
//     //     'percentage_off'              => 'nullable|numeric|min:0|max:100',
//     //     'custom_saving'               => 'nullable|string|max:255',
//     //     'description'                 => 'required|string',
//     //     'terms_conditions'            => 'required|string',
//     //     'organisation_id'             => 'required|exists:organisations,id',
//     //     'offer_address_type' => [
//     //         'required',
//     //         Rule::in(OfferAddressType::all())
//     //     ],
//     //     'full_address'                => 'max:255', 
//     //     'start_date'                  => 'required|date',
//     //     'end_date'                    => 'required|date|after:start_date',
//     //     'is_featured'                 => ['required', Rule::in(YesNo::all())],
//     //     'category_ids'                => 'nullable|array', 
//     //     'category_ids.*'              => 'exists:categories,id',
//     // ];


//     $rules = [
//         'title'                       => 'required|string|max:255',
//         'custom_redemption_code'      => 'nullable|string|max:255',
//         'custom_redemption_description' => 'nullable|string',
//         'custom_redemption_url'       => 'nullable|url|max:255',
//         'redemption_limit'            => 'integer|min:0',
//         'full_price'                  => 'nullable|numeric|min:0|max:99999999.99',
//         'discount_price'              => 'nullable|numeric|min:0|max:99999999.99',
//         'percentage_off'              => 'nullable|numeric|min:0|max:100',
//         'custom_saving'               => 'nullable|string|max:255',
//         'description'                 => 'nullable|string',
//         'terms_conditions'            => 'nullable|string',
//         'organisation_id'             => 'required|exists:organisations,id',
//         'offer_address_type' => [
//             'required',
//             Rule::in(OfferAddressType::all())
//         ],
//         'full_address' => 'nullable|max:255',
//         'latitude' => 'nullable|numeric|between:-90,90',
//         'longitude' => 'nullable|numeric|between:-180,180',
//         'street_number' => 'nullable|string|max:24',
//         'street_name' => 'nullable|string|max:128',
//         'city' => 'nullable|string|max:64',
//         'state' => 'nullable|string|max:3',
//         'postcode' => 'nullable|string|max:6',
//         'country' => 'nullable|string|max:24',
//         'start_date'                  => 'required|date',
//         'no_end_date'                 => ['sometimes', 'boolean'], 
//         'end_date'                    => 'nullable|date|after:start_date',
//         'is_featured'                 => [Rule::in(YesNo::all())],
//         'is_paid_advertisement'       => [Rule::in(YesNo::all())],
//         'banner_image'                => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//         'offer_url'                   => 'nullable|url|max:255',
//         'category_ids'                => 'required|nullable|array', 
//         'category_ids.*'              => 'exists:categories,id',
//         'offer_ongoing_subscription'  => [ Rule::in(['yes', 'no'])],
//     ];

//     $validatedData = $request->validate($rules);
//     $validatedData['user_id'] = Auth::id(); 

//     $organisation = Organisation::find($request->input('organisation_id'));
    
//     // if($request->input('offer_address_type') === OfferAddressType::USE_BUSSINESS_ADDRESS) {
//     //     $validatedData['full_address'] = $organisation->full_address;
//     // }elseif ($request->input('offer_address_type') === OfferAddressType::ONLINE) {
//     //     $validatedData['full_address'] = $request->input('offer_address_type');
//     // }
//     // $offer = Offer::create($validatedData);
//     // if (isset($validatedData['category_ids'])) {
//     //     $offer->categories()->sync($validatedData['category_ids']);
//     // } else {
//     //     $offer->categories()->sync([]);
//     // }


//     if($request->input('offer_address_type') === OfferAddressType::USE_BUSSINESS_ADDRESS) {
//         $validatedData['full_address'] = $organisation->full_address;
//         $validatedData['latitude'] =$organisation->latitude;
//         $validatedData['longitude'] =$organisation->longitude;
//         $validatedData['street_number'] =$organisation->street_number;
//         $validatedData['street_name'] =$organisation->street_name;
//         $validatedData['city'] =$organisation->city;
//         $validatedData['state'] =$organisation->state;
//         $validatedData['postcode'] =$organisation->postcode;
//         $validatedData['country'] =$organisation->country;

//     }elseif ($request->input('offer_address_type') === OfferAddressType::ONLINE) {
//         $validatedData['full_address'] = $request->input('offer_address_type');
//         $validatedData['latitude'] =null;
//         $validatedData['longitude'] =null;
//         $validatedData['street_number'] =null;
//         $validatedData['street_name'] =null;
//         $validatedData['city'] =null;
//         $validatedData['state'] =null;
//         $validatedData['postcode'] =null;
//         $validatedData['country'] =null;
//     }

      
//         // Handle banner image and offer URL based on is_featured status
//     if ($request->input('is_featured') === 'yes') {
//         if ($request->hasFile('banner_image')) {
//             $uploadDirectory = 'offers/banners'; // Custom directory for banners
//             $file = $request->file('banner_image');
//             $originalFileName = $file->getClientOriginalName();
//             $uniqueFileName = $this->getUniqueFileName($originalFileName, $uploadDirectory);
//             $path = $file->storeAs($uploadDirectory, $uniqueFileName, 'public');
//             $validatedData['banner_image'] = $path; // Store the relative path
//         }
//         // Save the offer URL
//         $validatedData['offer_url'] = $request->input('offer_url');
//     } else {
//         // If not featured, ensure these fields are null in the database
//         $validatedData['banner_image'] = null;
//         $validatedData['offer_url'] = null;
//     }



//      // Get existing image paths (which will be an array because of the cast)
//         $existingImageFilePaths =[];
//         $updatedImageFilePaths = $existingImageFilePaths;
//         $uploadDirectory = 'offers/image_files';
//         if ($request->hasFile('image_files')) {
//             foreach ($request->file('image_files') as $file) {
//                 $originalFileName = $file->getClientOriginalName();
//                 $uniqueFileName = $this->getUniqueFileName($originalFileName, $uploadDirectory);
//                 $path = $file->storeAs($uploadDirectory, $uniqueFileName, 'public');
//                 $updatedImageFilePaths[] = $path; 
//             }
//         }

//        // $validatedData['image_file'] = array_values($updatedImageFilePaths); 



//     $offer = Offer::create($validatedData);
//     if (isset($validatedData['category_ids'])) {
//         $offer->categories()->sync($validatedData['category_ids']);
//     } else {
//         $offer->categories()->sync([]);
//     }

//     // echo "<pre>";
//     // print_r($updatedImageFilePaths);
//     // exit();

//     foreach ($updatedImageFilePaths as $filePath) {
//         $offer->attachments()->create([ 
//             'file_image' => $filePath,
//         ]);
//     }

//     return redirect()->route('business.offers.index')->with('status', 'Offer created successfully!');
// }



    /**
     * Display the specified resource.
     */
    public function show(Offer $offers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
//    public function edit(Offer $offer)
// {
//     $user = Auth::user();
//     $userLevel = $user->level;
//     $currentUserId = $user->id;

//     // Determine user ID based on level
//     if ($userLevel === 'business') {
//         $userId = $currentUserId;
//     } elseif ($userLevel === 'sub_business') {
//         $userId = $user->parent_id;
//     }

//     // Fetch organisations belonging to this user
//     $organisations = Organisation::where('user_id', $userId)
//         ->orderBy('title')
//         ->select('id', 'title', 'full_address')
//         ->get();

//     // Fetch other necessary data for the edit form
//     $categories = Category::all();
//     $organisationStatuses = OrganisationStatus::labels();
//     $yesNoOptions = YesNo::labels();
//     $selectedCategoryIds = $offer->categories->pluck('id')->toArray();
//     $currentorganisation = Organisation::find($offer->organisation_id);

//     return view('business.offers.edit', compact(
//         'offer',
//         'organisations',
//         'categories',
//         'organisationStatuses',
//         'yesNoOptions',
//         'selectedCategoryIds',
//         'currentorganisation'
//     ));
// }



public function edit(Offer $offer)
{
    $user = Auth::user();
    $userLevel = $user->level;
    $currentUserId = $user->id;
    $today = Carbon::today()->toDateString();
    $requiredRoleIds = ["6"]; // only admin role can edit

    // Fetch organisation of this offer
    $currentorganisation = Organisation::find($offer->organisation_id);

    // --------------------------------------------------------------
    // BUSINESS PERMISSION CHECK
    // --------------------------------------------------------------
    if ($userLevel === 'business') {
        if ($currentorganisation->user_id !== $currentUserId) {
            abort(403, "You are not authorized to edit this offer.");
        }

        // organisations available for selection
        $organisations = Organisation::where('user_id', $currentUserId)
            ->orderBy('title')
            ->select('id', 'title', 'full_address')
            ->get();
    }

    // --------------------------------------------------------------
    // SUB BUSINESS PERMISSION CHECK WITH DATE VALIDATION
    // --------------------------------------------------------------
    elseif ($userLevel === 'sub_business') {

        // check assignment + role + date
        $assignment = $currentorganisation->ApproveassociatedUsers()
            ->where('user_assign_id', $currentUserId)
            ->where(function ($q2) use ($requiredRoleIds) {
                foreach ($requiredRoleIds as $roleId) {
                    $q2->orWhereJsonContains('organisation_user.role', $roleId);
                }
            })
            ->where(function ($q3) use ($today) {
                $q3->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q4) use ($today) {
                $q4->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->first();

        if (!$assignment) {
            abort(403, "You don't have permission to edit this offer.");
        }

        $organisations = Organisation::whereHas('ApproveassociatedUsers', function ($q) use ($currentUserId, $requiredRoleIds, $today) {
                $q->where('user_assign_id', $currentUserId)
                  ->where(function ($q2) use ($requiredRoleIds) {
                      foreach ($requiredRoleIds as $roleId) {
                          $q2->orWhereJsonContains('organisation_user.role', $roleId);
                      }
                  })
                  ->where(function ($q3) use ($today) {
                      $q3->whereNull('start_date')->orWhere('start_date', '<=', $today);
                  })
                  ->where(function ($q4) use ($today) {
                      $q4->whereNull('end_date')->orWhere('end_date', '>=', $today);
                  });
            })
            ->orderBy('title')
            ->select('id', 'title', 'full_address')
            ->get();
    }

    // fetch other data
    $categories = Category::all();
    $organisationStatuses = OrganisationStatus::labels();
    $yesNoOptions = YesNo::labels();
    $selectedCategoryIds = $offer->categories->pluck('id')->toArray();

    return view('business.offers.edit', compact(
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

        // $validatedData['no_end_date'] = $request->has('no_end_date') ? 1 : 0;
        // $offer->update($validatedData);



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
            'organisation_id'             => 'required|exists:organisations,id',
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
            'is_featured'                 => ['required', Rule::in(YesNo::all())],
            'is_paid_advertisement'       => [Rule::in(YesNo::all())],
            'banner_image'                => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'offer_url'                   => 'nullable|url|max:255',
            'category_ids'                => 'required|nullable|array', 
            'category_ids.*'              => 'exists:categories,id',
            'offer_ongoing_subscription'  => [ Rule::in(['yes', 'no'])],
        ];

        $validatedData = $request->validate($rules);

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

    // Handle banner image and offer URL based on is_featured status
    if ($request->input('is_featured') === 'yes') {
        if ($request->hasFile('banner_image')) {
            if ($offer->banner_image) {
                Storage::disk('public')->delete($offer->banner_image);
            }
            $uploadDirectory = 'offers/banners';
            $file = $request->file('banner_image');
            $originalFileName = $file->getClientOriginalName();
            $uniqueFileName = $this->getUniqueFileName($originalFileName, $uploadDirectory);
            $path = $file->storeAs($uploadDirectory, $uniqueFileName, 'public');
            $validatedData['banner_image'] = $path;
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
                    $originalFileName = $file->getClientOriginalName();
                    $uniqueFileName = $this->getUniqueFileName($originalFileName, $uploadDirectory);
                    $path = $file->storeAs($uploadDirectory, $uniqueFileName, 'public');
                    $offer->attachments()->create([
                        'file_image' => $path,
                    ]);
                }
            }



        //$validatedData['image_file'] = array_values($updatedImageFilePaths); 


       $validatedData['no_end_date'] = $request->has('no_end_date');

       $user = Auth::user();
       $userLevel = $user->level;
       if ($userLevel === 'sub_business') {
           $validatedData['parent_id'] = $user->parent_id;
        }


        $offer->update($validatedData);
        // Sync categories
        if (isset($validatedData['category_ids'])) {
            $offer->categories()->sync($validatedData['category_ids']);
        } else {
            $offer->categories()->sync([]);
        }

        return redirect()->route('business.offers.index')->with('status', 'Offer updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        if (!empty($offer->image_file)) {
            foreach ($offer->image_file as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }
        $offer->delete();

        return redirect()->route('business.offers.index')->with('status', 'Offer deleted successfully!');
    }

}
