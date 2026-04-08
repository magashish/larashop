<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserTwoFactor
{
    // /**
    //  * Handle an incoming request.
    //  *
    //  * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
    //  */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

         // Check if the user just completed a social login
        if (session('social_login_completed')) {
           $user->resetTwoFactorCode();
           // Clear the flag to prevent future bypasses
            session()->forget('social_login_completed');
            return $next($request);
        }


        if(auth()->check() && $user->two_factor_code)
        {
            if($user->two_factor_expires_at<now()) //expired
            {
                $user->resetTwoFactorCode();
                auth()->logout();

                return redirect()->route('login')
                ->withMessage('The two factor code has expired. Please login again.');
            }

            if(!$request->is('verify*')) //prevent enless loop, otherwise send to verify
            {
                return redirect()->route('verify.index');
            }
        }

        return $next($request);
    }


}
