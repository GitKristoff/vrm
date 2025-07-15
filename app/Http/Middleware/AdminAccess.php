<?php

namespace App\Http\Middleware;

use App\Models\Veterinarian;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Grant access to admin users
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Grant access to veterinarian admins
        if ($user->role === 'veterinarian') {
            $vet = Veterinarian::where('user_id', $user->id)->first();
            if ($vet && $vet->is_admin) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access');
    }
}
