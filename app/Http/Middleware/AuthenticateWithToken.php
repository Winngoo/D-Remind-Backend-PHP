<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\UserToken;

class AuthenticateWithToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // $token = $request->bearerToken();

        // if ($token && User::where('api_token', $token)->exists()) {
        //     return $next($request);
        // }

        // return response()->json(['error' => 'Unauthenticated'], 401);

        $token = $request->bearerToken();

        if ($token && UserToken::where('token', $token)->exists()) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthenticated'], 401);
    }
}
