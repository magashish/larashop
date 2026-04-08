<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Dynamic redirect path after password reset.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $user = auth()->user();
        // Example: redirect based on user role
        if ($user->level === 'member') {
            return '/subscriber-dashboard';
        }

        // Default redirect for regular users
        return '/my-account';
    }

    /**
     * Optional: override sendResetResponse for custom flash message
     */
    protected function sendResetResponse($request, $response)
    {
        return redirect($this->redirectPath())
                        ->with('status', trans($response));
    }
}
