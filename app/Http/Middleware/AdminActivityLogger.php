<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminLog; // Make sure to import your AdminLog model
use App\Models\UserLog;  // IMPORTANT: Import your UserLog model here

class AdminActivityLogger // You might consider renaming this middleware to something more general like 'ActivityLogger'
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        // IMPORTANT: Call $next($request) first to let the request proceed and authentication
        // be resolved. This ensures Auth::id() or Auth::guard('admin')->id() is available.
        $response = $next($request);

        // --- Logging for Admin Users ---
        if (Auth::guard('admin')->check()) {
            $adminId = Auth::guard('admin')->id();

            $module = null; // Changed from adminModule for consistency
            $action = null; // Changed from adminModuleAction for consistency

            if ($request->route()) {
                $routeName = $request->route()->getName();
                if ($routeName) {
                    // Example: 'admin.users.index' -> module: 'users', action: 'index'
                    $nameParts = explode('.', $routeName);
                    if (count($nameParts) > 1 && $nameParts[0] === 'admin') { // Ensure it's an admin route name
                        $module = $nameParts[1]; // e.g., 'users'
                        $action = end($nameParts); // e.g., 'index'
                    } else {
                        // Fallback if route name doesn't follow expected admin.*.action pattern strictly
                        $module = $routeName;
                    }
                }

                $controllerAction = $request->route()->getActionName();
                if (str_contains($controllerAction, '@')) {
                    list($controller, $method) = explode('@', $controllerAction);
                    if (!$action || strlen($method) < strlen($action)) { // Prioritize shorter/more direct method names
                        $action = $method;
                    }
                    // Use controller name as module if not already set
                    if (!$module) {
                        $module = str_replace('Controller', '', class_basename($controller));
                    }
                }
            }

            // Fallback values if nothing could be determined
            if (!$module) {
                $module = 'admin_access';
            }
            if (!$action) {
                $action = 'unspecified';
            }

            // Log the admin action
            AdminLog::create([
                'admin_id' => $adminId,
                'module' => 'ping', 
                'module_action' => substr($action, 0, 30),
                'url' => substr($request->fullUrl(), 0, 255),
            ]);
        }

//         dd([
//     'web'   => auth('web')->check(),
//     'admin' => auth('admin')->check(),
//     'admin_id' => Auth::guard('admin')->id(),
//     'web_id' => Auth::guard('web')->id(),
// ]);


// exit();

        // --- Logging for Regular Web Users (business/member) ---
        // Log only if not an admin (to avoid double logging if an admin also matches 'web' guard)
        // AND a 'web' guard user is authenticated.
        if (Auth::guard('web')->check()) {
            $userId = Auth::guard('web')->id();

            $module = null;
            $moduleAction = null;
            $url = $request->fullUrl();

            if ($request->route()) {
                $routeName = $request->route()->getName();
                if ($routeName) {
                    // For user logs, you might have route names like 'member.dashboard', 'business.settings'
                    $nameParts = explode('.', $routeName);
                    if (count($nameParts) > 0) {
                        $module = $nameParts[0]; // e.g., 'member', 'business', 'dashboard'
                        if (count($nameParts) > 1) {
                            $moduleAction = $nameParts[1]; // e.g., 'dashboard', 'settings', 'profile'
                        } else {
                            // If only one part (e.g., 'dashboard'), use it as action too
                            $moduleAction = $nameParts[0];
                        }
                    }
                }

                $controllerAction = $request->route()->getActionName();
                if (str_contains($controllerAction, '@')) {
                    list($controller, $method) = explode('@', $controllerAction);
                    if (!$moduleAction) { // If action not derived from route name
                        $moduleAction = $method;
                    }
                    if (!$module) { // If module not derived from route name
                        $module = str_replace('Controller', '', class_basename($controller));
                    }
                }
            }

            // Fallback values if nothing could be determined
            if (!$module) {
                $module = 'web_access';
            }
            if (!$moduleAction) {
                $moduleAction = 'unspecified';
            }

            // Log the user action
            UserLog::create([
                'user_id' => $userId,
                //'module' => substr($module, 0, 30),         // Matches your 'module' varchar(30)
                'module' => 'ping', 
                'module_action' => substr($moduleAction, 0, 30), // Matches your 'module_action' varchar(30)
                'url' => substr($url, 0, 255),               // Matches your 'url' varchar(255)
            ]);
        }

        return $response;
    }
}