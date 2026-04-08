<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <--- This is the correct import for the Auth facade!
use Symfony\Component\HttpFoundation\Response;

class AdminTwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the authenticated admin user using the 'admin' guard
        $admin = Auth::guard('admin')->user();

        // Check if an admin is logged in AND they have a two_factor_code set
        if (Auth::guard('admin')->check() && $admin->two_factor_code) {

            // If the two-factor code has expired
            if ($admin->two_factor_expires_at < now()) {
                $admin->resetTwoFactorCode(); // Implement this method on your Admin model
                Auth::guard('admin')->logout(); // Log out the admin

                // Redirect to the admin login page with a message
                return redirect()->route('admin.login') // <-- CHANGE THIS TO YOUR ADMIN LOGIN ROUTE NAME
                                 ->with('message', 'The two-factor code has expired. Please login again.');
            }

            // If the request is NOT to an admin 2FA verification page, redirect to it
            // This prevents an endless loop if the middleware is applied globally.
            // Adjust 'admin.verify.index' to your actual admin 2FA verification route prefix/name
            if (!$request->is('admin/verify*') && !$request->routeIs('admin.two-factor.verify.form') && !$request->routeIs('admin.two-factor.verify.code') && !$request->routeIs('admin.two-factor.resend')) {
                return redirect()->route('admin.verify.index'); // <-- CHANGE THIS TO YOUR ADMIN 2FA VERIFICATION FORM ROUTE NAME
            }
        }

        // If no pending 2FA or already on verification page, proceed with the request
        return $next($request);
    }
}