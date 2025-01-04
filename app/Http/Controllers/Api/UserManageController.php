<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Reminder;
use App\Models\Roles;

class UserManageController extends Controller
{
    public function UserDetailsView()
    {
        $userdetails = User::select('id', 'role_name', 'full_name', 'email', 'phone_number', 'postcode', 'country', 'profile_picture', 'status')
            ->where('status', 'active')
            ->get()
            ->map(function ($user) {
                if ($user->profile_picture) {
                    $user->profile_picture = url($user->profile_picture);
                }
                return $user;
            });

        return response()->json(['success' => true, 'users_details' => $userdetails], 200);
    }


    public function UserRemindersView($userid)
    {
        $remindersdetails = Reminder::select('id', 'title', 'category', 'subcategory', 'due_date', 'time', 'description', 'provider', 'cost')->where('user_id', $userid)->get();

        return response()->json(['success' => true, 'reminders_details' => $remindersdetails], 200);
    }


    public function UserDetailsEdit(Request $request, $userid)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'nullable|string|max:5',
            'full_name' => 'nullable|string|max:255',
            'role_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:15',
            'postcode' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:4048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::find($userid);
        if (!$user) {
            // Log::error("User not found: ID {$userid}");
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        try {
            if ($request->hasFile('profile_picture')) {
                $picture = $request->file('profile_picture');
                $filename = time() . '.' . $picture->getClientOriginalExtension();

                $picture->move(public_path('userprofile'), $filename);
                $user->profile_picture = 'userprofile/' . $filename;
            }

            $fields = $request->only(['full_name', 'email', 'phone_number', 'postcode', 'country', 'role_id', 'role_name']);
            foreach ($fields as $key => $value) {
                if (!is_null($value)) {
                    $user->$key = $value;
                }
            }

            $user->save();

            Log::info('User details updated successfully', ['id' => $userid, 'fields' => $fields]);

            return response()->json([
                'success' => true,
                'message' => 'User details updated successfully.',
                'user' => $user->fresh()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating user', ['id' => $userid, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'An error occurred while updating the user.'], 500);
        }
    }


    public function UserDetailsDelete($userid)
    {
        $user = User::find($userid);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $user->status = 'inactive';
        $user->save();

        return response()->json(['success' => true, 'message' => 'User inactive successfully.'], 200);
    }


    public function UserSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'term' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $searchTerm = $request->term;

        if (is_numeric($searchTerm)) {
            $userdetails = User::where('id', $searchTerm)
                ->select('id', 'full_name', 'email', 'phone_number', 'postcode', 'country', 'profile_picture', 'status')
                ->get()
                ->map(function ($user) {
                    if ($user->profile_picture) {
                        $user->profile_picture = url($user->profile_picture);
                    }
                    return $user;
                });
        } else {
            $userdetails = User::where('full_name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                ->select('id', 'full_name', 'email', 'phone_number', 'postcode', 'country', 'profile_picture', 'status')
                ->get()
                ->map(function ($user) {
                    if ($user->profile_picture) {
                        $user->profile_picture = url($user->profile_picture);
                    }
                    return $user;
                });
        }

        if ($userdetails->isEmpty()) {
            return response()->json([
                'message' => 'No Users found'
            ], 404);
        }

        return response()->json([
            'users' => $userdetails
        ], 200);
    }


    public function UserFilter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:active,inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if (empty($request->all())) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide parameters for the search.',
            ], 402);
        }

        $query = User::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->input('start_date'), $request->input('end_date')]);
        }

        $filteredUsers = $query->select('id', 'full_name', 'email', 'phone_number', 'postcode', 'country', 'status', 'created_at')->get();

        if ($filteredUsers->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No users found for the given filters.'], 404);
        }

        return response()->json(['success' => true, 'users' => $filteredUsers], 200);
    }

    
    public function UserListCalendar()
    {
        $userslist = User::select('id', 'full_name')->get();

        return response()->json(['success' => true, 'users_list' => $userslist]);
    }


    public function UserRemindersCalendar($userid)
    {

        $reminderslist = Reminder::where('user_id', $userid)->select('id', 'title', 'category', 'subcategory', 'due_date', 'time', 'description', 'provider', 'cost', 'user_id')->get();

        // dd($reminderslist);

        if ($reminderslist->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Reminders not found for this User.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $userid,
                'reminders_details' => $reminderslist
            ],
        ], 200);
    }

    public function UserRolesList()
    {
        $roleslist = Roles::select('id', 'role_name')->get();

        return response()->json(['success' => true, 'roles_list' => $roleslist]);
    }
}
