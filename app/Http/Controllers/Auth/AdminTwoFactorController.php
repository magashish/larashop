<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Notifications\AdminTwoFactorCode; // Correct notification for admins


class AdminTwoFactorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', '2fa.admin']);
    }

    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $adminEmail = $admin ? $admin->email : 'your registered admin email address';
        return view('admin.auth.admintwofactor', compact('adminEmail'));
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'integer|required',
        ]);

        $admin = Auth::guard('admin')->user();

        if ($request->input('two_factor_code') == $admin->two_factor_code) {
            $admin->resetTwoFactorCode(); // Implement this method on your Admin model

            return redirect()->route('admin.dashboard'); // <--- CHANGE THIS TO YOUR ADMIN'S MAIN DASHBOARD ROUTE
        }

        return redirect()->back()->withErrors(['two_factor_code' => 'The two factor code you have entered does not match']);
    }

    public function resend()
    {
        $admin = Auth::guard('admin')->user();
        $admin->generateTwoFactorCode(); 
        $admin->notify(new AdminTwoFactorCode($admin)); 

        return redirect()->back()->with('message', 'The two factor code has been sent again');
    }
}