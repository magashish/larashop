<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberUserAccess
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

        // Check if the user's 'level' column is 'member'
        if ($user->level !== 'member') {
            // Abort with 403 Forbidden, or redirect to an unauthorized page
            abort(403, 'Unauthorized. This area is for member users only.');
            // Alternatively, redirect:
            // return redirect()->route('unauthorized');
        }

        return $next($request);
    }
}