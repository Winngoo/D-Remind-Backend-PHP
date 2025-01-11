<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\UserToken;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = UserToken::where('token', $token)->first();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        //dd($user);

        $hasPermission = DB::table('users_permissions_map')
            ->where('user_id', $user->user_id)
            ->where('permission_id', function ($query) use ($permission) {
                $query->select('id')
                    ->from('permissions')
                    ->where('permission_name', $permission)
                    ->limit(1);
            })
            ->exists();

        if (!$hasPermission) {
            return response()->json(['error' => 'Forbidden: You do not have the required permission'], 403);
        }

        return $next($request);
    }
}
