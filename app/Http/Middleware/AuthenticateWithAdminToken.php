<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin;
use App\Models\AdminToken;

class AuthenticateWithAdminToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // $token = $request->bearerToken();

        // if ($token && Admin::where('api_token', $token)->exists()) {
        //     return $next($request);
        // }

        // return response()->json(['error' => 'Unauthenticated'], 401);

        $token = $request->bearerToken();

        if ($token && AdminToken::where('token', $token)->exists()) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthenticated'], 401);
    }
}
