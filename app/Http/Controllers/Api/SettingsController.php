<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Permission;

class SettingsController extends Controller
{
    public function ListOfPermission($id)
    {
        $userId = $id;

        $users = DB::table('users')->where('id', $userId)->select('id', 'role_id', 'role_name', 'full_name', 'email', 'profile_picture', 'phone_number', 'status')->get();

        $permissions = DB::table('permissions')
            ->select('id', 'menu_name', 'permission_name')
            ->get();

            $mappedPermissions = DB::table('users_permissions_map')
            ->where('user_id', $id)
            ->pluck('permission_id')
            ->toArray();
    
        $groupPermissions = [];
        foreach ($permissions as $permission) {
            if (!isset($groupPermissions[$permission->menu_name])) {
                $groupPermissions[$permission->menu_name] = [];
            }
    
            $groupPermissions[$permission->menu_name][] = [
                'id' => $permission->id,
                'permission_name' => $permission->permission_name,
                'assigned' => in_array($permission->id, $mappedPermissions),
            ];
        }
    
        return response()->json([
            'user' => $users,
            'permissions' => $groupPermissions,
        ]);
    }

    public function UpdatePermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $userId = $request->user_id;
        $permissionIds = $request->permission_ids;
    
        DB::table('users_permissions_map')->where('user_id', $userId)->delete(); 
        foreach ($permissionIds as $permissionId) {
            DB::table('users_permissions_map')->insert([
                'user_id' => $userId,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        return response()->json(['message' => 'Permissions mapped successfully'], 200);
    }

    
}
