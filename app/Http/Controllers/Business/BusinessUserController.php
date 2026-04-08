<?php
namespace App\Http\Controllers\Business;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Validation\Rules\Password; 

class BusinessUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::where('parent_id', auth()->id())
        ->when($request->filled('email'), function ($query) use ($request) {
            $query->where('email', 'like', '%' . $request->email . '%');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->withQueryString(); // keeps filter values during pagination

        return view('business.user.userslist', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      return view('business.user.create');
  }

    /**
     * Store a newly created resource in storage.
     */

   public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|email|unique:users,email',
        'password'   => 'required|min:8|confirmed',
        'mobile'     => 'required|regex:/^\+?\d{10,15}$/|unique:users,mobile',
    ]);

    // ❌ Validation failed
    if ($validator->fails()) {

        // AJAX request
        if ($request->expectsJson()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Normal form submit
        return redirect()
            ->back()
            ->withErrors($validator)
            ->withInput();
    }

    $user = User::create([
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'name'       => $request->first_name.' '.$request->last_name,
        'email'      => $request->email,
        'mobile'     => $request->mobile,
        'password'   => Hash::make($request->password),
        'parent_id'  => auth()->id(),
        'level'      => 'sub_business',
    ]);

    // ✅ AJAX response
    if ($request->expectsJson()) {
        $rowHtml = view('business.organisation.user-row', [
            'association' => $user,
            'rowKey' => uniqid(),
        ])->render();

        return response()->json([
            'status'  => true,
            'message' => 'User created successfully!',
            'row'     => $rowHtml
        ]);
    }

    // ✅ Normal redirect response
    return redirect()
        ->route('business.users.index')
        ->with('status', 'User created successfully!');
}


    public function store_old(Request $request)
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
                'unique:users,mobile', 
            ],
        ];

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

        $validatedData = $request->validate($rules, $messages);
        $validatedData['parent_id'] = auth()->id();
        $validatedData['level'] = 'sub_business';
        $validatedData['name'] = $validatedData['first_name'] . ' ' . $validatedData['last_name'];
        $validatedData['password'] = Hash::make($validatedData['password']);
        User::create($validatedData);

        return redirect()->route('business.users.index')->with('status', 'User created successfully!');   
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

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
        return view('business.user.sub_business _user_edit', compact('user','webroles','availableRoles'));
    }





    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //    $user = Auth::user();
    //    $rules = [
    //         'name' => ['required', 'string', 'max:255'],
    //         'first_name' => ['required', 'string', 'max:255'],
    //         'last_name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
    //         'password' => ['nullable', 'string', 'min:8', 'confirmed'],
    //     ];
    //     $validatedData = $request->validate($rules);
    //     if ($request->filled('password')) {
    //         $validatedData['password'] = Hash::make($validatedData['password']); 
    //     } else {
    //         unset($validatedData['password']);
    //     }
    //     $user->update($validatedData);
    //     return redirect()->route('business.user.index')->with('status', 'User updated successfully!');
    // }

    // public function update(Request $request, string $id)
    // {
    //     $userToUpdate = Auth::user();
    //     if (!$userToUpdate) {
    //         return redirect()->back()->withErrors('User not found or not authenticated.');
    //     }
    //     $rules = [
    //         'first_name' => ['required', 'string', 'max:255'],
    //         'last_name' => ['required', 'string', 'max:255'],
    //         'email' => [
    //             'required',
    //             'string',
    //             'email',
    //             'max:255',
    //             Rule::unique('users')->ignore($userToUpdate->id),
    //         ],
    //         'mobile' => ['required', 'string', 'min:10', 'max:15', 'regex:/^\+?\d{10,15}$/'],
    //     ];

    //     $messages = [
    //         'mobile.required' => 'Please enter your mobile number.',
    //         'mobile.regex' => 'Please enter a valid 10-digit  mobile number (e.g., 9876543210).',
    //         'mobile.max' => 'Your mobile number cannot be longer than :max characters.',
    //     ];
    //     $validatedData = $request->validate($rules, $messages);
    //     $userToUpdate->update($validatedData);
    //     return redirect()->route('business.user.index')->with('status', 'User updated successfully!');
    // }


      public function update(Request $request, User $user)
    {

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(['active', 'deactive'])],
            'mobile'     => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^\+?\d{10,15}$/',
                Rule::unique('users')->ignore($user->id) 
            ]
        ];
        $validatedData = $request->validate($rules);
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']); 
        } else {
            unset($validatedData['password']);
        }

        $validatedData['name'] = $validatedData['first_name'] . ' ' . $validatedData['last_name'];
        $user->update($validatedData);

        return redirect()->route('business.users.index')->with('status', 'User updated successfully!');
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->forceDelete();
        return redirect()->route('business.users.index')->with('status', 'User deleted successfully.');
    }
}
