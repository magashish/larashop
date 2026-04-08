<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Ensure the user is authenticated with the 'web' guard
        if (!Auth::guard('web')->check()) {
            return redirect('/login'); // Or wherever your web login page is
        }

        $user = Auth::guard('web')->user();
        // Check if the user's 'level' column is 'business'
        // if ($user->level !== 'business') {
        //     abort(403, 'Unauthorized. This area is for business users only.');
        //     // return redirect()->route('unauthorized'); // Make sure to define this route
        // }

        // Check if the user's 'level' column is either 'business' or 'sub_business'
        if (!in_array($user->level, ['business', 'sub_business'])) {
            abort(403, 'Unauthorized. This area is only for business and sub-business users.');
             // OR
            // return redirect()->route('unauthorized');
        }

        return $next($request);
    }
}