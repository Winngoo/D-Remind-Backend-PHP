<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function Profileshow($id)
    {
        $User = User::select('id', 'full_name', 'email', 'phone_number', 'postcode', 'country', 'profile_picture')->where('id', $id)->first();

        if (!$User) {
            return response()->json(['success' => false, 'message' => 'Profile not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $User->id,
                'full_name' => $User->full_name,
                'email' => $User->email,
                'phone_number' => $User->phone_number,
                'postcode' => $User->postcode,
                'country' => $User->country,
                'profile_picture' => asset($User->profile_picture),
            ],
        ]);
    }


    public function Profileupdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            // 'email' => 'required|string|email|max:255',
            'phone_number' => 'required|string|min:10|max:15',
            'postcode' => [
                'required',
                'string',
                'max:10',
                'min:5',
                'regex:/^([A-Z]{1,2}[0-9][A-Z0-9]? ?[0-9][A-Z]{2}|GIR ?0AA)$/i',
            ],
            'country' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $User = User::select('id', 'full_name', 'phone_number', 'postcode', 'country')->where('id', $id)->first();

        if (!$User) {
            return response()->json(['success' => false, 'message' => 'Profile not found'], 404);
        }

        // $user = User::where('email', $request->email)->first();

        // if ($user) {
        //     return response()->json(['success' => false, 'message' => 'The email has already taken!'], 404);
        // }

        $User->update($request->only([
            'full_name',
            // 'email',
            'phone_number',
            'postcode',
            'country',
        ]));

        return response()->json(['success' => true, 'message' => 'Profile updated successfully ', 'data' => $User], 200);
    }


    public function Userpasswordupdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'new_password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $User = User::find($id);

        if (!$User) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        if (!Hash::check($request->current_password, $User->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect.'], 401);
        }

        $User->password = Hash::make($request->new_password);
        $User->save();

        return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
    }


    public function Useremailupdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $User = User::select('id', 'email')->where('id', $id)->first();

        if (!$User) {
            return response()->json(['success' => false, 'message' => 'Profile not found'], 404);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response()->json(['success' => false, 'message' => 'The email has already taken!'], 404);
        }

        $User->update($request->only([
            'email',
        ]));

        return response()->json(['success' => true, 'message' => 'Email updated successfully ', 'data' => $User], 200);
    }


    public function Userpictureupload(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:4048'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $User = User::find($id);

        if (!$User) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        if ($User) {
            if ($request->hasFile('profile_picture')) {
                $picture = $request->file('profile_picture');
                $filename = time() . '.' . $picture->getClientOriginalExtension();

                $path = $picture->move(public_path('userprofile'), $filename);
                $User->profile_picture = 'userprofile/' . $filename;
            }

            $User->save();

            return response()->json(['success' => 'Picture updated successfully']);
        } else {
            return response()->json(['error' => 'Picture not updated'], 404);
        }
    }
}
