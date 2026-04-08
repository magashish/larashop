<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters,
     * and other route configuration.
     */
    public function boot(): void
    {
        /**
         * API rate limit
         * Example: 60 requests per minute per user/IP
         * with custom JSON 429 message.
         */
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too many API requests. Please try again in 1 minute.',
                    ], 429, $headers);
                });
        });

        /**
         * Login rate limit
         * Example: 5 attempts per 5 minutes per IP
         * with custom JSON 429 message (good for AJAX login).
         */
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(5, 5) // 5 attempts in 5 minutes per IP
                ->by($request->ip())
                // or per email:
                // ->by($request->input('email') ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too many login attempts. Please try again in 5 minutes.',
                    ], 429, $headers);
                });
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
