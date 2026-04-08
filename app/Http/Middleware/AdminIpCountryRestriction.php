<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\HttpFoundation\Response;

class AdminIpCountryRestriction
{
    public function handle(Request $request, Closure $next): Response
    {
        $userIp = $request->ip();

        // 1. IP WHITELIST (High Priority)
        // Add your home, office, or developer IPs here. 
        // These IPs bypass the country check entirely.
        $whitelistedIps = explode(',', env('ADMIN_WHITELIST_IPS', '127.0.0.1'));

        if (in_array($userIp, $whitelistedIps)) {
            return $next($request);
        }

        // 2. COUNTRY VERIFICATION
        // Locate the user based on their IP address
        $position = Location::get($userIp);
        
        // Define which countries are allowed (ISO Codes)
        $allowedCountries = explode(',', env('ADMIN_ALLOWED_COUNTRIES', 'US'));

        if ($position) {
            // If the user's country is NOT in the allowed list
            if (!in_array($position->countryCode, $allowedCountries)) {
                abort(403, "Access Denied: Your country ({$position->countryName}) is not authorized.");
            }
        } else {
            // Optional: Block if the IP cannot be traced to a country (VPNs/Tor/Local)
            abort(403, "Access Denied: Location could not be verified.");
        }

        return $next($request);
    }
}