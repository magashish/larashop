<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use App\Models\PrizeDrawEntry; 
use App\Models\PrizeDrawEntryBackup;
use App\Models\UserActivityLog; // <-- Import the model here
use App\Models\UserPackageSubscription;




use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Symfony\Component\HttpFoundation\StreamedResponse; 
use App\Models\OrganisationUser; // Make sure this is imported


class UserController extends Controller
{

   public function __construct()
   {
    $this->middleware(['permission:view users'])->only('index');
    $this->middleware(['permission:create users'])->only(['create', 'store']);
    $this->middleware(['permission:edit users'])->only(['edit', 'update']);
    $this->middleware(['permission:delete users'])->only('destroy');
}

    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $query = User::query();
    //     if ($request->has('email') && !empty($request->email)) {
    //         $query->where('email', 'LIKE', '%' . $request->email . '%');
    //     }
    //      $users = $query->get();
    //     return view('user.index', compact('users'));
    // }

  public function index(Request $request)
{
    $perPage = 50; 

    // Start query
    $users = User::withCount(['mainEntries', 'backupEntries']);

    // Search filter
    if ($request->filled('email')) {
        //$users->where('email', 'like', '%' . $request->input('email') . '%');
        $search=$request->input('email');
        $users->where(function ($query) use ($search) {
            $query->where('email', 'like', '%' . $search . '%')
            ->orWhere('name', 'like', '%' . $search . '%')
            ->orWhere('mobile', 'like', '%' . $search . '%');
        });

    }

    // Status filter
    $sortBy = $request->input('sort_by');
    if ($sortBy == 'active') {
        $users->where('status', 'active');  
    } elseif ($sortBy == 'deactivated') {
        $users->where('status', 'deactive'); 
    }

    // User level filter
    $level = $request->input('level');
    if ($level == 'member') {
        $users->where('level', 'member');
    } elseif ($level == 'business') {
        $users->where('level', 'business');
    }

    // Live filter (active users with active subscriptions)
    if ($request->input('live') == 'live') {
        $users->whereHas('userPackageSubscriptions', function ($query) {
            $query->where('status', 'active');
        });
    }

    // Live filter
    $live = $request->input('live');

    if ($live == 'live') {
        // Users with at least one active subscription
        $users->whereHas('userPackageSubscriptions', function ($query) {
            $query->where('status', 'active');
        });
    } elseif ($live == 'expire') {
        $users->whereHas('userPackageSubscriptions') // at least 1 subscription exists
      ->whereDoesntHave('userPackageSubscriptions', function ($query) {
          $query->where('status', 'active'); // none active
      });
    } elseif ($live == 'inactive') {
      $users->doesntHave('userPackageSubscriptions');
    } elseif ($live == 'smssubscribe') {
      $users->where('sms_unsubscribed', '0'); 
    }

    // Sorting
    $users->orderBy('created_at', 'desc');

    // Pagination with query strings preserved
    $users = $users->paginate($perPage)->appends($request->query());

    return view('user.index', compact('users'));
    // return view('user.index', compact('users'));
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    $rules = [
    'first_name' => ['required', 'string', 'max:255'],
    'last_name'  => ['required', 'string', 'max:255'],
    'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
    'password'   => ['required', 'string', 'min:8', 'confirmed'],
    'mobile'     => [
        'required',
        'string',
        'min:10',
        'max:15',
        'regex:/^\+?\d{10,15}$/',
        'unique:users,mobile', // ensures mobile is unique
    ],
];

// Custom validation messages
$messages = [
    'first_name.required' => 'Please enter your first name.',
    'last_name.required'  => 'Please enter your last name.',
    'email.required'      => 'Please enter your email address.',
    'email.email'         => 'Please enter a valid email address.',
    'email.unique'        => 'This email address is already registered. Please use a different one.',
    'password.required'   => 'Please enter your password.',
    'password.min'        => 'Your password must be at least :min characters long.',
    'password.confirmed'  => 'The password confirmation does not match.',
    'mobile.required'     => 'Please enter your mobile number.',
    'mobile.regex'        => 'Please enter a valid mobile number (10-15 digits, optional + prefix).',
    'mobile.min'          => 'Your mobile number must be at least :min characters long.',
    'mobile.max'          => 'Your mobile number cannot be longer than :max characters.',
    'mobile.unique'       => 'This mobile number is already in use.',
];

         // Validate the request data using the separate rules and messages arrays
        $validatedData = $request->validate($rules, $messages);

        // Combine first_name and last_name into the 'name' field
        $validatedData['name'] = $validatedData['first_name'] . ' ' . $validatedData['last_name'];

        // Hash the password before creating the user (CRUCIAL FOR SECURITY!)
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Create the user in the database
        User::create($validatedData);

        return redirect()->route('users.index')->with('status', 'User created successfully!');   
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(User $user)
    {   

       // $webroles = Role::where('guard_name', 'web')->get();

        $webroles = Role::where('guard_name', 'web')
                ->whereNotIn('id', [14, 15, 16])
                ->get();

        $user->load(['associatedOrganisations' => function($query) {
            $query->withPivot('role', 'start_date', 'end_date');
        }]);
        $availableRoles = Role::all()->pluck('name', 'id')->toArray();
        return view('user.edit', compact('user','webroles','availableRoles'));
    }


    public function update(Request $request, User $user)
    {

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'level' => ['required'],
            'status' => ['required', Rule::in(['active', 'deactive'])],
            'mobile'     => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^\+?\d{10,15}$/',
                Rule::unique('users')->ignore($user->id) 
            ],
            'exclude_from_prize_draw' => ['sometimes', 'boolean'], 
            'stripe_id' => [
                'nullable',    
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id) 
            ],
        ];
        $validatedData = $request->validate($rules);
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']); 
        } else {
            unset($validatedData['password']);
        }

        $validatedData['name'] = $validatedData['first_name'] . ' ' . $validatedData['last_name'];

        $validatedData['exclude_from_prize_draw'] = $request->has('exclude_from_prize_draw');
        $validatedData['sms_unsubscribed'] = $request->has('sms_unsubscribed');


        // Update the admin user with the prepared data
        $user->update($validatedData);
        $userOrganisationsData = $request->input('organisation', []);
        $submittedPivotIds = []; 


        foreach ($userOrganisationsData as $rowKey => $orgData) {
            $organisationId = $orgData['id'];
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
                ->where('user_assign_id', $user->id) 
                ->update($pivotData);
                $submittedPivotIds[] = $pivotId;
                $organisationUserInstance = OrganisationUser::find($pivotId);
                if ($organisationUserInstance) {
                    $organisationUserInstance->roles()->sync($assignedRoleIds);
                }

            } else {
                $pivotData['admin_id'] = Auth::id(); 
                $pivotData['user_id'] = null;
                $pivotData['organisation_assign_id'] = $organisationId; 
                $pivotData['user_assign_id'] = $user->id; 
                $newOrganisationUser = OrganisationUser::create($pivotData);
                $newOrganisationUser->roles()->sync($assignedRoleIds);

            }
        }




        return redirect()->route('users.index')->with('status', 'User updated successfully!');
    }





    public function user_entries(string $userID){
        $user=User::find($userID);
        $entries = PrizeDrawEntry::with('user', 'packageSubscription')->where('user_id', $userID)->latest()->paginate(20);
        
         return view('user.entries', compact('user','entries'));
    }

    public function userActivity($userId)
    {
        $user=User::find($userId);
        $logs = UserActivityLog::where('user_id', $userId)->orderBy('created_at', 'asc')->paginate(10);
        return view('user.activity', compact('user','logs'));
    }


    // public function userpackagessubscriptions($userId)
    // {
    //     $user=User::find($userId);
    //     $subscriptions = UserPackageSubscription::where('user_id', $user->id)->orderBy('starts_at', 'desc')->get();

    //     $refundableSubscriptions = $user->subscriptions()->where(function ($query) {
    //         $query->where('stripe_status', 'canceled')
    //         ->orWhereNotNull('ends_at'); 
    //     })->get();

    //     return view('user.packagessubscriptions', compact('user', 'subscriptions', 'refundableSubscriptions'));
    // }


    public function userpackagessubscriptions($userId)
{
    $user = User::find($userId);

    // All packages/subscriptions from custom table
    $subscriptions = UserPackageSubscription::where('user_id', $user->id)
                        ->orderBy('starts_at', 'desc')
                        ->get();

    // Refundable Stripe subscriptions (Cashier)
    $refundableSubscriptions = $user->subscriptions()->where(function ($query) {
        $query->where('stripe_status', 'canceled')
              ->orWhereNotNull('ends_at');
    })->get();

    // Merge both collections
    $allRecords = $subscriptions->concat($refundableSubscriptions)
                    ->sortByDesc(function ($item) {
                        return $item->starts_at ?? $item->created_at;
                    });

    return view('user.packagessubscriptions', compact('user', 'subscriptions', 'refundableSubscriptions', 'allRecords'));
}



    // public function userpackagessubscriptions($userId)
    // {
    //     $user=User::find($userId);
    //     $logs = UserActivityLog::where('user_id', $userId)->orderBy('created_at', 'asc')->paginate(10);
    //     return view('user.activity', compact('user','logs'));
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // $user->delete();
        $user->forceDelete();
        return redirect()->route('users.index')->with('status', 'User deleted successfully.');
    }


    // public function exportCsv(Request $request): StreamedResponse
    // {
    //     $users = User::orderBy('created_at', 'desc')->get(); // Adjust this query as needed
    //     $filename = 'users_' . now()->format('Ymd_His') . '.csv';
    //     $headers = [
    //         'Content-Type' => 'text/csv',
    //         'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    //         'Pragma' => 'no-cache',
    //         'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
    //         'Expires' => '0',
    //     ];
    //     $callback = function() use ($users) {
    //         $file = fopen('php://output', 'w');
    //         // Define CSV Headers (matching your table headers)
    //         fputcsv($file, [
    //             'First Name',
    //             'Last Name',
    //             'Email',
    //             'Mobile',
    //             'Created At',
    //             'User Status',
    //             'Stripe Subscription Status',
    //             'App Last Activity',
    //             'Web Last Activity'
    //         ]);
    //         foreach ($users as $user) {
    //             fputcsv($file, [
    //                 $user->first_name,
    //                 $user->last_name,
    //                 $user->email,
    //                 $user->mobile,
    //                 $user->created_at?->format('Y-m-d H:i:s'), 
    //                 $user->status,
    //                 $user->stripe_subscription_status ?? '---', 
    //                 $user->app_last_activity ?? '---',           
    //                 $user->web_last_activity ?? '---',           
    //             ]);
    //         }
    //         fclose($file);
    //     };

    //     return new StreamedResponse($callback, 200, $headers);
    // }

        public function exportCsv(Request $request): StreamedResponse
{
    $query = User::orderBy('created_at', 'desc');

    // Search filter
    if ($request->filled('email')) {
        $search = $request->input('email');
        $query->where(function ($q) use ($search) {
            $q->where('email', 'like', '%' . $search . '%')
              ->orWhere('name', 'like', '%' . $search . '%');
        });
    }

    // Live filter
    $live = $request->input('live');
    if ($live == 'live') {
        $query->whereHas('userPackageSubscriptions', function ($q) {
            $q->where('status', 'active');
        });
    } elseif ($live == 'expire') {
        $query->whereHas('userPackageSubscriptions')
              ->whereDoesntHave('userPackageSubscriptions', function ($q) {
                  $q->where('status', 'active');
              });
    } elseif ($live == 'inactive') {
        $query->doesntHave('userPackageSubscriptions');
    }

    $users = $query->get();

    $filename = $live.'_users_' . now()->format('Ymd_His') . '.csv';

    $headers = [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Pragma'              => 'no-cache',
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        'Expires'             => '0',
    ];

    $callback = function () use ($users) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['Name', 'First Name', 'Last Name', 'Email', 'Mobile', 'Created At', 'User Status']);
        foreach ($users as $user) {
            fputcsv($file, [
                $user->name,
                $user->first_name,
                $user->last_name,
                $user->email,
                $user->mobile,
                $user->created_at?->format('Y-m-d H:i:s'),
                $user->status,
            ]);
        }
        fclose($file);
    };

    return new StreamedResponse($callback, 200, $headers);
}

}
