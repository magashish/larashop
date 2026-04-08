<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {




        $guards = empty($guards) ? [null] : $guards;

        // foreach ($guards as $guard) {
        //     if (Auth::guard($guard)->check()) {
        //         // If authenticated, check for special conditions before redirecting
        //         if ($guard === 'admin') {
        //             // Admins redirect to the admin dashboard
        //             return redirect()->route('admin.dashboard');
        //         }
        //         // *** CRITICAL 2FA CHECK FOR REGULAR USERS (DEFAULT GUARD) ***
        //         $user = Auth::user();
        //         if ($user  && $user->two_factor_code) {
        //             return redirect()->route('verify.index');
        //         }
        //         // For all other authenticated users (who passed 2FA or don't require it), 
        //         // redirect to the default application home.
        //         return redirect(RouteServiceProvider::HOME);
        //     }
        // }

        foreach ($guards as $guard) {

            if (Auth::guard($guard)->check()) {


                // ----------------------------------------------------------------
                // Admin users
                // ----------------------------------------------------------------
                if ($guard === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                // ----------------------------------------------------------------
                // Web users
                // ----------------------------------------------------------------
                if ($guard === 'web' || is_null($guard)) {
                    $user = Auth::guard($guard)->user();

                    if ($user) {
                        // 2FA check
                        if ($user->two_factor_code) {
                            return redirect()->route('verify.index');
                        }

                        // Business / Sub-business users
                        if ($user->level === 'business' || $user->level === 'sub_business') {
                            return redirect()->route('business.myaccount.index');
                        }

                        // Add other user levels here if needed
                        // e.g., Subscriber dashboard
                        if ($user->level === 'member') {
                            return redirect()->route('subscriber.dashboard.index');
                        }

                        // Fallback for all other users
                       // return redirect(RouteServiceProvider::HOME);
                    }
                }
            }
        }

        return $next($request);
    }
}