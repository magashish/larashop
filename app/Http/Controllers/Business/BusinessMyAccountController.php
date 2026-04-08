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

class BusinessMyAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $webroles = Role::where('guard_name', 'web')->get();
        $user->load(['associatedOrganisations' => function($query) {
            $query->withPivot('role', 'start_date', 'end_date');
        }]);
        $availableRoles = Role::all()->pluck('name', 'id')->toArray();
        return view('business.user.businesslist ', compact('user','webroles','availableRoles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        $user = Auth::user();
        $webroles = Role::where('guard_name', 'web')->get();
        $user->load(['associatedOrganisations' => function($query) {
            $query->withPivot('role', 'start_date', 'end_date');
        }]);
        $availableRoles = Role::all()->pluck('name', 'id')->toArray();
        return view('business.user.edit', compact('user','webroles','availableRoles'));
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

     public function update(Request $request, string $id)
    {
        $userToUpdate = Auth::user();
        if (!$userToUpdate) {
            return redirect()->back()->withErrors('User not found or not authenticated.');
        }
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userToUpdate->id),
            ],
             'mobile' => ['required', 'string', 'min:10', 'max:15', 'regex:/^\+?\d{10,15}$/'],
        ];

         $messages = [
            'mobile.required' => 'Please enter your mobile number.',
            'mobile.regex' => 'Please enter a valid 10-digit  mobile number (e.g., 9876543210).',
            'mobile.max' => 'Your mobile number cannot be longer than :max characters.',
        ];
        $validatedData = $request->validate($rules, $messages);
        $userToUpdate->update($validatedData);
        return redirect()->route('business.myaccount.index')->with('status', 'User updated successfully!');
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
