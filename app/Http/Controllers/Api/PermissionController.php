<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function CreatePermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu_name' => 'required|string|max:255',
            'permission_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $permissionscheck = Permission::where('permission_name',$request->permission_name)->first();

        if ($permissionscheck) {
            return response()->json(['success' => false, 'message' => 'Permissions already created!'], 404);
        }  

        $permissions = Permission::create($request->all());

        return response()->json(['message' => 'Permissions created successfully', 'data' => $permissions], 201);
    }

    public function ViewPermission()
    {
        $roles = Permission::select('id', 'menu_name', 'permission_name')->get();

        return response()->json(['message' => 'Permissions fetched successfully', 'data' => $roles], 201);
    }

    public function EditPermission(Request $request, $id)
    {
        $permissions = Permission::find($id);

        if (!$permissions) {
            return response()->json(['message' => 'Permissions not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'menu_name' => 'required|string|max:255',
            'permission_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $permissionscheck = Permission::where('permission_name',$request->permission_name)->first();

        if ($permissionscheck) {
            return response()->json(['success' => false, 'message' => 'Permissions already exists!'], 404);
        }

        $permissions->update($request->all());

        return response()->json(['message' => 'Permissions updated successfully', 'data' => $permissions], 200);
    }

    public function DeletePermission($id)
    {
        $permissions = Permission::find($id);

        if (!$permissions) {
            return response()->json(['message' => 'Permissions not found'], 404);
        }

        $permissions->delete();

        return response()->json(['message' => 'Permissions deleted successfully'], 200);
    }

   
}
