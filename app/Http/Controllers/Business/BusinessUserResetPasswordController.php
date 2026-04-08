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
use Illuminate\Validation\ValidationException;

class BusinessUserResetPasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        return view('business.user.password', compact('user','webroles','availableRoles'));
    }

    /**
     * Update the specified resource in storage.
     */


    public function update(Request $request, string $id)
    {
        $userToUpdate = Auth::user();
        if (!$userToUpdate || $userToUpdate->id != $id) {
            return redirect()->back()->withErrors('Unauthorized action.');
        }
        $rules = [];
        $rules['current_password'] = [
            'required',
            'string',
            function ($attribute, $value, $fail) use ($userToUpdate) {
                if (!Hash::check($value, $userToUpdate->password)) {
                    $fail('The provided current password does not match your actual password.');
                }
            },
        ];
        $rules['password'] = [
            'required', 
            'string',
            'confirmed',
            Password::min(8)
            ->max(32)
            ->mixedCase()
            ->numbers()
            ->symbols(),
        ];

        try {
            $validatedData = $request->validate($rules,
                [
                    'current_password.required' => 'Please enter your current password.',
                    'password.required' => 'The new password field is required.',
                    'password.min' => 'The new password must be at least 8 characters long.',
                    'password.max' => 'The new password may not be greater than 32 characters.',
                    'password.mixed_case' => 'The new password must contain at least one uppercase and one lowercase letter.',
                    'password.numbers' => 'The new password must contain at least one number.',
                    'password.symbols' => 'The new password must contain at least one special character.',
                    'password.confirmed' => 'The new password confirmation does not match.',
                ]
            );
            $userToUpdate->update([
                'password' => Hash::make($validatedData['password']),
            ]);
            return redirect()->route('business.user.index')->with('status', 'Password updated successfully!');
            
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
