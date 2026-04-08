<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Notifications\UserCreatedNotification; // <-- Make sure to import this
use App\Models\Plan;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/my-account';
    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function showRegistrationForm()
    {
        // Fetch all the plans from the database
        $plans = Plan::all();

        // Pass the fetched plans to the registration view
        return view('auth.register')->with('plans', $plans);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                ->max(32)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(),
            ],
        ], [
            'password.required' => 'New password is required',
            'password.confirmed' => 'Passwords do not match',
            'password.min' => 'Password must be at least 8 characters',
            'password.max' => 'Password cannot exceed 32 characters',
            'password.mixed_case' => 'Password must contain both uppercase and lowercase letters',
            'password.numbers' => 'Password must contain at least one number',
            'password.symbols' => 'Password must contain at least one special character',
            'password.uncompromised' => 'This password has appeared in a data leak. Please choose a different password.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // return User::create([
        //     'name' => $data['first_name'].' '.$data['last_name'],
        //     'first_name' => $data['first_name'],
        //     'last_name' => $data['last_name'],
        //     'email' => $data['email'],
        //     'password' => Hash::make($data['password']),
        // ]);

        $user = User::create([
            'name' => $data['first_name'].' '.$data['last_name'], // Your existing logic
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Send the notification to the newly created user
        $user->notify(new UserCreatedNotification($user)); // <-- Add this line

        return $user;
    }
}
