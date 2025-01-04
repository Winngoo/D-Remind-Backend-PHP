<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Roles;

class RolesController extends Controller
{
    public function CreateRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $rolescheck = Roles::where('role_name',$request->role_name)->first();

        if ($rolescheck) {
            return response()->json(['success' => false, 'message' => 'Roles already created!'], 404);
        }

        $roles = Roles::create($request->all());

        return response()->json(['message' => 'Roles created successfully', 'data' => $roles], 201);
    }

    public function ViewRoles()
    {
        $roles = Roles::select('id', 'role_name')->get();

        return response()->json(['message' => 'Roles fetched successfully', 'data' => $roles], 201);
    }

    public function EditRoles(Request $request, $id)
    {
        $roles = Roles::find($id);

        if (!$roles) {
            return response()->json(['message' => 'Roles not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $rolescheck = Roles::where('role_name',$request->role_name)->first();

        if ($rolescheck) {
            return response()->json(['success' => false, 'message' => 'Roles already exists!'], 404);
        }

        $roles->update($request->all());

        return response()->json(['message' => 'Roles updated successfully', 'data' => $roles], 200);
    }


    public function DeleteRoles($id)
    {
        $roles = Roles::find($id);

        if (!$roles) {
            return response()->json(['message' => 'Roles not found'], 404);
        }

        $roles->delete();

        return response()->json(['message' => 'Roles deleted successfully'], 200);
    }
}
