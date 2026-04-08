<?php
namespace App\Http\Controllers\Business;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organisation;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Validation\Rule;
use App\Enums\OrganisationAddressType; 
use App\Enums\OrganisationStatus;       
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Auth; 
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; 
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon; 
use App\Models\OrganisationUser; // Make sure this is imported

use Illuminate\Database\Eloquent\Builder;



use Illuminate\Support\Facades\Http; // For downloading images
use Illuminate\Support\Str;
use League\Csv\Reader;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Validator; // Don't forget to import this!

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class BusinessOrganisationController extends Controller
{


    public function index()
    {

       $user = Auth::user();
       $userId = $user->id;
       $userLevel = $user->level; 

        // Define the roles that grant a sub_business user access to the organisation list
        // Update these IDs to match your actual Role IDs (e.g., 'admin' role ID)
       $requiredRoleIds = ["6", "14", "15", "16"]; 
       $requiredRoleIds = ["6"]; 

        // $roleIdsFromInput = array_keys(available_roles());
        // $requiredRoleIds = array_map('strval', $roleIdsFromInput);


       $baseQuery = Organisation::query();

        // ----------------------------------------------------------------------
        // 1. Apply User Level Filtering
        // ----------------------------------------------------------------------
       if ($userLevel === 'business') {
            // CONDITION: BUSINESS user only sees organisations they created (the owner).
        $baseQuery->where('user_id', $userId);

      } elseif ($userLevel === 'sub_business') {
            // CONDITION: SUB_BUSINESS user only sees organisations where they are 
            // an approved, assigned member AND have the required role.
        $baseQuery->whereHas('ApproveassociatedUsers', function (Builder $q) use ($userId, $requiredRoleIds) {
                // 1. Must be the assigned user
            $q->where('user_assign_id', $userId);
                // 2. Must have at least ONE of the required Role IDs in the JSON 'role' field
            $q->where(function (Builder $qRoles) use ($requiredRoleIds) {
                foreach ($requiredRoleIds as $roleId) {
                         // Uses OR operator within this nested closure
                 $qRoles->orWhereJsonContains('organisation_user.role', $roleId);
             }
         });
                // 3. Check start_date and end_date
            $today = Carbon::today()->toDateString();
            $q->where(function (Builder $qDates) use ($today) {
                $qDates->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function (Builder $qDates) use ($today) {
                $qDates->whereNull('end_date')->orWhere('end_date', '>=', $today);
            });


        });
    }
        // If the user level is neither 'business' nor 'sub_business', the query returns nothing.
        // ----------------------------------------------------------------------


        // ----------------------------------------------------------------------
        // 2. Eager Load Counts (Applied to the filtered set)
        // ----------------------------------------------------------------------
    $baseQuery->withCount('AllassociatedUsers')
    ->withCount('allOffers')
    ->withCount('pendingOffers')
    ->withCount('expiredOffers')
    ->withCount('activeOffers');

        // ----------------------------------------------------------------------
        // 3. Execute Queries Grouped by Processing Status
        // ----------------------------------------------------------------------

        // Organisations with 'NEW' status
    $organisations_new = (clone $baseQuery)
    ->where('processing_status', OrganisationStatus::NEW)
    ->get();

        // Organisations with 'APPROVED' status
    $organisations_approved = (clone $baseQuery)
    ->where('processing_status', OrganisationStatus::APPROVED)
    ->get();

        // Organisations with 'REJECTED' status
    $organisations_rejected = (clone $baseQuery)
    ->where('processing_status', OrganisationStatus::REJECTED)
    ->get();







    return view('business.organisation.index', compact('organisations_new','organisations_approved','organisations_rejected'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $user = Auth::user();
    // Only business users can create organisation
    if ($user->level !== 'business') {
        abort(403, "You don't have permission to create organisation.");
    }

     $users = User::where('parent_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->withQueryString();

    return view('business.organisation.create', compact('users'));
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

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('organisations/images', 'public');
            $validatedData['image'] = $newImagePath;
        }
        $userOrganisationsData = $request->input('user', []);

        // Custom validation for each row
        foreach ($userOrganisationsData as $rowKey => $orgData) {
            if (!empty($orgData['start_date']) && !empty($orgData['end_date'])) {
                $start = \Carbon\Carbon::parse($orgData['start_date']);
                $end = \Carbon\Carbon::parse($orgData['end_date']);
                if ($end->lte($start)) {
                    return back()
                    ->withInput()
                    ->withErrors([
                        "user.$rowKey.end_date" => "End Date must be greater than Start Date"
                    ]);
                }
            }
        }

        $validatedData['user_id'] = Auth::id(); 
        $organisation=Organisation::create($validatedData);



         // 4️⃣ Update or insert users in pivot
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



        return to_route('business.organisations.index')->with('status', 'Organisation added successfully.');
    }


      /**
     * Display the specified resource.
     */

public function show(Organisation $organisation)
{
    $user = Auth::user();
    $currentUserId = $user->id;
    $userLevel = $user->level;
    $today = Carbon::today();
    $requiredRoleIds = ["6"]; 

    // ----------------------------------------------------------------------
    // Determine which user ID to fetch offers for
    // ----------------------------------------------------------------------
    $userIdForOffers = [];

    if ($userLevel === 'business') {

        // Owner check
        if ($organisation->user_id !== $currentUserId) {
            abort(403, 'You are not authorized to view offers for this organisation.');
        }

        // Owner is always allowed
        $userIdForOffers = [$currentUserId];

        // Get approved assigned users with valid dates and role
        $assignedUsers = $organisation->ApproveassociatedUsers()
            // ->whereJsonContains('organisation_user.role', $requiredRoleIds)
            // ->where(function ($q) use ($today) {
            //     $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
            // })
            // ->where(function ($q) use ($today) {
            //     $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            // })
            ->pluck('user_assign_id')
            ->toArray();

        $userIdForOffers = array_merge($userIdForOffers, $assignedUsers);
    }
    elseif ($userLevel === 'sub_business') {

        // Validate assignment with date and role
        $assignment = $organisation->ApproveassociatedUsers()
            ->where('user_assign_id', $currentUserId)
            ->whereJsonContains('organisation_user.role', $requiredRoleIds)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->first();

        if (!$assignment) {
            abort(403, 'You are not authorized to view offers for this organisation.');
        }

        // Owner id
        $ownerId = $organisation->user_id;

        // Get approved sub users with valid date & role
        $assignedUsers = $organisation->ApproveassociatedUsers()
            ->whereJsonContains('organisation_user.role', $requiredRoleIds)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->pluck('user_assign_id')
            ->toArray();

        $userIdForOffers = array_merge([$ownerId], $assignedUsers);
    }

    // ----------------------------------------------------------------------
    // Fetch offers for the organisation
    // ----------------------------------------------------------------------
    // $offer_new = Offer::with(['organisation', 'categories'])
    //     ->whereIn('user_id', $userIdForOffers)
    //     ->where('organisation_id', $organisation->id)
    //     ->where('processing_status', OrganisationStatus::NEW)
    //     ->get();

    // $offer_approved = Offer::with(['organisation', 'categories'])
    //     ->whereIn('user_id', $userIdForOffers)
    //     ->where('organisation_id', $organisation->id)
    //     ->where('processing_status', OrganisationStatus::APPROVED)
    //     ->get();

    // $offer_rejected = Offer::with(['organisation', 'categories'])
    //     ->whereIn('user_id', $userIdForOffers)
    //     ->where('organisation_id', $organisation->id)
    //     ->where('processing_status', OrganisationStatus::REJECTED)
    //     ->get();

    // $offer_expire = Offer::with(['organisation', 'categories'])
    //     ->whereIn('user_id', $userIdForOffers)
    //     ->where('organisation_id', $organisation->id)
    //     ->where('processing_status', OrganisationStatus::APPROVED)
    //     ->where('end_date', '<', $today)
    //     ->get();

    // $live_offers = Offer::with(['organisation', 'categories'])
    //     ->whereIn('user_id', $userIdForOffers)
    //     ->where('organisation_id', $organisation->id)
    //     ->where('start_date', '<=', $today)
    //     ->where('processing_status', OrganisationStatus::APPROVED)
    //     ->where(function ($query) use ($today) {
    //         $query->where('end_date', '>=', $today)
    //               ->orWhere('no_end_date', 1);
    //     })
    //     ->get();


    $offer_new = Offer::with(['organisation', 'categories'])
    ->when($user->level === 'sub_business', function ($query) use ($userIdForOffers) {
        return $query->whereIn('user_id', $userIdForOffers);
    })
    ->where('organisation_id', $organisation->id)
    ->where('processing_status', OrganisationStatus::NEW)
    ->get();

$offer_approved = Offer::with(['organisation', 'categories'])
    ->when($user->level === 'sub_business', function ($query) use ($userIdForOffers) {
        return $query->whereIn('user_id', $userIdForOffers);
    })
    ->where('organisation_id', $organisation->id)
    ->where('processing_status', OrganisationStatus::APPROVED)
    ->get();

$offer_rejected = Offer::with(['organisation', 'categories'])
    ->when($user->level === 'sub_business', function ($query) use ($userIdForOffers) {
        return $query->whereIn('user_id', $userIdForOffers);
    })
    ->where('organisation_id', $organisation->id)
    ->where('processing_status', OrganisationStatus::REJECTED)
    ->get();

$offer_expire = Offer::with(['organisation', 'categories'])
    ->when($user->level === 'sub_business', function ($query) use ($userIdForOffers) {
        return $query->whereIn('user_id', $userIdForOffers);
    })
    ->where('organisation_id', $organisation->id)
    ->where('processing_status', OrganisationStatus::APPROVED)
    ->where('end_date', '<', $today)
    ->get();

$live_offers = Offer::with(['organisation', 'categories'])
    ->when($user->level === 'sub_business', function ($query) use ($userIdForOffers) {
        return $query->whereIn('user_id', $userIdForOffers);
    })
    ->where('organisation_id', $organisation->id)
    ->where('start_date', '<=', $today)
    ->where('processing_status', OrganisationStatus::APPROVED)
    ->where(function ($query) use ($today) {
        $query->where('end_date', '>=', $today)
              ->orWhere('no_end_date', 1);
    })
    ->get();

    return view('business.organisation.show', compact(
        'organisation',
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
    // public function edit(Organisation $organisation)
    // {


    //     $organisation->load(['AllassociatedUsers' => function($query) {
    //         $query->withPivot('id', 'role', 'start_date', 'end_date');
    //     }]);

    //     $assignedUsers = $organisation->AllassociatedUsers;
    //     $assignedIds = $assignedUsers->pluck('id')->toArray();
    //     $createdUsers = User::where('parent_id', auth()->id())->get();
    //     $newUsersToAssign = $createdUsers->whereNotIn('id', $assignedIds);
    //     foreach ($newUsersToAssign as $user) {
    //         $organisation->AllassociatedUsers()->attach($user->id, [
    //             'role' => 'member',
    //             'start_date' => now(),
    //         ]);
    //     }

    //      // Eager load associated users with pivot data for the current organisation
    //     $organisation->load(['AllassociatedUsers' => function($query) {
    //         $query->withPivot('id', 'role', 'start_date', 'end_date');
    //     }]);

    //     // but if you add editing functionality, you'll need to fetch them.
    //     $availableRoles = Role::all()->pluck('name', 'id')->toArray();
    //     return view('business.organisation.edit', compact('organisation','availableRoles','newUsersToAssign'));

    // }


    public function edit(Organisation $organisation)
{
    $user = Auth::user();
    $currentUserId = $user->id;
    $userLevel = $user->level;

    // Required admin role id
    $requiredRoleIds = ['6'];

    // ---------------- PERMISSION CHECK ----------------
    if ($userLevel === 'business') {
        // Must be owner of the organisation
        if ($organisation->user_id !== $currentUserId) {
            abort(403, "You are not authorized to edit this organisation.");
        }
    } 
    elseif ($userLevel === 'sub_business') {

        // Must be assigned + must have admin role (6)
        $assignment = $organisation->ApproveassociatedUsers()
            ->where('user_assign_id', $currentUserId)
            ->where(function ($q) use ($requiredRoleIds) {
                foreach ($requiredRoleIds as $roleId) {
                    $q->orWhereJsonContains('organisation_user.role', $roleId);
                }
            })
            ->first();

        if (!$assignment) {
            abort(403, "You don't have permission to edit this organisation.");
        }
    } 
    else {
        abort(403, "Unauthorized access");
    }
    // ---------------------------------------------------


    // Continue with original logic
    $organisation->load(['AllassociatedUsers' => function($query) {
        $query->withPivot('id', 'role', 'start_date', 'end_date');
    }]);

    $assignedUsers = $organisation->AllassociatedUsers;
    $assignedIds = $assignedUsers->pluck('id')->toArray();

    $createdUsers = User::where('parent_id', auth()->id())->get();
    $newUsersToAssign = $createdUsers->whereNotIn('id', $assignedIds);

    foreach ($newUsersToAssign as $user) {
        $organisation->AllassociatedUsers()->attach($user->id, [
            'role' => 'member',
            'start_date' => now(),
        ]);
    }

    $organisation->load(['AllassociatedUsers' => function($query) {
        $query->withPivot('id', 'role', 'start_date', 'end_date');
    }]);

    $availableRoles = Role::all()->pluck('name', 'id')->toArray();

    return view('business.organisation.edit', compact('organisation','availableRoles','newUsersToAssign'));
}




    public function update(Request $request, Organisation $organisation)
    {
    // 1️⃣ Base validation for organisation fields
        // $validatedData = $request->validate([
        //     'title' => 'required|string|max:255',
        //     'abn' => 'required|string|max:255',
        //     'website' => 'nullable|url|max:255',
        //     'instagram' => 'nullable|max:255',
        //     'facebook' => 'nullable|max:255',
        //     'youtube' => 'nullable|max:255',
        //     'tiktok' => 'nullable|max:255',
        //     'twitter' => 'nullable|max:255',
        //     'organisation_address_type' => [
        //         'required',
        //         Rule::in(\App\Enums\OrganisationAddressType::all())
        //     ],
        //     'full_address'=> 'max:255',
        //     'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        // ]);


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

    // 2️⃣ Handle image upload/removal
        $currentImagePath = $organisation->image; 
        if ($request->hasFile('image')) {
            if ($currentImagePath && Storage::disk('public')->exists($currentImagePath)) {
                Storage::disk('public')->delete($currentImagePath);
            }
            $newImagePath = $request->file('image')->store('organisations/images', 'public');
            $validatedData['image'] = $newImagePath;
        } elseif ($request->has('remove_image') && $currentImagePath) {
            if (Storage::disk('public')->exists($currentImagePath)) {
                Storage::disk('public')->delete($currentImagePath);
            }
            $validatedData['image'] = null;
        } else {
            $validatedData['image'] = $currentImagePath;
        }

        $organisation->update($validatedData);

    // 3️⃣ Validate users table
        $userOrganisationsData = $request->input('user', []);

    // Custom validation for each row
        foreach ($userOrganisationsData as $rowKey => $orgData) {
            if (!empty($orgData['start_date']) && !empty($orgData['end_date'])) {
                $start = \Carbon\Carbon::parse($orgData['start_date']);
                $end = \Carbon\Carbon::parse($orgData['end_date']);
                if ($end->lte($start)) {
                    return back()
                    ->withInput()
                    ->withErrors([
                        "user.$rowKey.end_date" => "End Date must be greater than Start Date"
                    ]);
                }
            }
        }

    // 4️⃣ Update or insert users in pivot
        $submittedPivotIds = []; 
        foreach ($userOrganisationsData as $rowKey => $orgData) {
            $user_id = $orgData['id'];
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
                $pivotData['admin_id'] = null;
                $pivotData['user_id'] = Auth::id(); 
                $pivotData['organisation_assign_id'] = $organisation->id; 
                $pivotData['user_assign_id'] = $user_id; 
                $newOrganisationUser = OrganisationUser::create($pivotData);
                $newOrganisationUser->roles()->sync($assignedRoleIds);
            }
        }

        return to_route('business.organisations.index')->with('status', 'Organisation updated successfully.');
    }


}
