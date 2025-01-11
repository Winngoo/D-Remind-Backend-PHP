<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Admin;
use App\Models\UserToken;
use App\Models\AdminToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;
use App\Mail\AdminResetPasswordMail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function UserLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'email' => $request->email,
            ], 404);
        }
    
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The given password is Invalid!',
                'email' => $request->email,
                'user_id' => $user->id ,
            ], 401);
        }
    
        $activeSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'completed')
            ->first();
    
        if ($user->status === 'inactive' && $activeSubscription) {
            return response()->json([
                'status' => 'error',
                'message' => 'You need to verify your account via email to activate your subscription.',
                'user_id' => $user->id ,
            ], 403);
        }
    
        if (!$activeSubscription) {
            return response()->json([
                'status' => 'error',
                'message' => 'Complete the payment first!',
                'user_id' => $user->id ,
            ], 401);
        }

        // if (!$user) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Now you are Inactive , Complete the payment!',
        //         'email' => $request->email,
        //         'user_id' => $user_id,
        //     ], 401);
        // }


        $today = now()->format('Y-m-d');
        //return $today;

        $endDate = Carbon::parse($activeSubscription->end_date);

        if ($endDate->isSameDay($today)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your subscription ends today. Please renew to continue using the service.',
                'user_id' => $user->id ,
            ], 403);
        }

        if ($endDate->isPast()) {
            $activeSubscription->update(['status' => 'expired']);
            $user->update(['status' => 'inactive']);

            return response()->json([
                'status' => 'error',
                'message' => 'Your subscription has expired. Please renew to reactivate your account.',
                'user_id' => $user->id ,
            ], 403);
        }

        // $fullToken = $user->createToken('Personal Access Token')->plainTextToken;
        // $tokenParts = explode('|', $fullToken);
        // $token = $tokenParts[1];


        // $user->api_token = $token;
        // $user->save();

        // return response()->json([
        //     'status' => 'success',
        //     'data' => [
        //         'id' => $user->id,
        //         'full_name' => $user->full_name,
        //         'email' => $user->email,
        //         'status' => $user->status,
        //         'token' => $token,
        //     ],
        // ], 200);

        $isPrimary = UserToken::where('user_id', $user->id)->exists() ? false : true;

        $fullToken = $user->createToken($request->device_name ?? 'Unknown Device')->plainTextToken;
        $tokenParts = explode('|', $fullToken);
        $token = $tokenParts[1];
    
        UserToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'device_name' => $request->device_name ?? 'Unknown Device',
            'is_primary' => $isPrimary,
        ]);
    
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'status' => $user->status,
                'token' => $token,
            ],
        ], 200);
    }


    public function UserLoginVerify($token)
    {
        $user = UserToken::where('token', $token)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'current User is logout',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'current User is login',
        ], 200);
    }

    public function VerifyUserMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email format',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not registered',
            ], 404);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $resetLink = "https://d-remind-winngoo.vercel.app/reset-password/{$token}";

        Mail::to($user->email)->send(new ResetPasswordMail($resetLink));

        return response()->json([
            'status' => 'success',
            'data' => ['email' => $user->email],
            'message' => 'Verification code sent to your email',
        ], 200);
    }


    public function UserResetPassword(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input!',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tokenData = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (!$tokenData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password token has expired!',
            ], 400);
        }

        $email = $tokenData->email;

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin Not Found',
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Password has been successfully reset!',
        ], 200);
    }


    public function UserLogout(Request $request)
    {
        // $token = $request->bearerToken();
        // //dd($token);

        // $user = User::where('api_token', $token)->first();

        // if ($user) {
        //     $user->api_token = null;
        //     $user->save();

        //     return response()->json(['message' => 'Successfully logged out'], 200);
        // }

        // return response()->json(['error' => 'Invalid token'], 401);

        $token = $request->bearerToken();

        $userToken = UserToken::where('token', $token)->first();
    
        if ($userToken) {
            if ($userToken->is_primary) {
                UserToken::where('user_id', $userToken->user_id)->delete();
    
                return response()->json(['message' => 'Successfully logged out from primary device'], 200);
            } else {
                $userToken->delete();
    
                return response()->json(['message' => 'Successfully logged out from this device'], 200);
            }
        }
    
        return response()->json(['error' => 'Invalid token'], 401);
    }


    public function AdminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $admin_verify = Admin::where('email', $request->email)->first();

        if (!$admin_verify) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin Not Found',
                'email' => $request->email,
            ], 404);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!Hash::check($request->password, $admin->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The given password is Invalid!',
                'email' => $request->email,
            ], 401);
        }

        // $fullToken = $admin->createToken('AdminAccessToken')->plainTextToken;
        // $tokenParts = explode('|', $fullToken);
        // $token = $tokenParts[1];

        // $admin->api_token = $token;
        // $admin->save();

        // return response()->json([
        //     'status' => 'success',
        //     'data' => [
        //         'message' => 'Login Successfully',
        //         'id' => $admin->id,
        //         'name' => $admin->name,
        //         'email' => $admin->email,
        //         'token' => $token,
        //     ],
        // ], 200);

        $isPrimary = AdminToken::where('admin_id', $admin->id)->exists() ? false : true;

        $fullToken = $admin->createToken($request->device_name ?? 'Unknown Device')->plainTextToken;
        $tokenParts = explode('|', $fullToken);
        $token = $tokenParts[1];
    
        AdminToken::create([
            'admin_id' => $admin->id,
            'token' => $token,
            'device_name' => $request->device_name ?? 'Unknown Device',
            'is_primary' => $isPrimary,
        ]);
    
        return response()->json([
            'status' => 'success',
            'data' => [
                'message' => 'Login Successfully',
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'token' => $token,
            ],
        ], 200);
    }


    public function VerifyAdminMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email format',
            ], 422);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin not Found',
            ], 404);
        }

        $token = Str::random(64);
        $admin_id = $admin->id;

        DB::table('adminpassword_reset_tokens')->updateOrInsert(
            ['email' => $admin->email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $resetLink = "https://d-remind-winngoo.vercel.app/admin-reset-password/{$token}";

        Mail::to($admin->email)->send(new AdminResetPasswordMail($resetLink));

        return response()->json([
            'status' => 'success',
            'message' => 'Verification code sent to your email',
        ], 200);
    }


    public function AdminResetPassword(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input!',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tokenData = DB::table('adminpassword_reset_tokens')
            ->where('token', $token)
            ->first();

        if (!$tokenData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password token has expired!',
            ], 400);
        }

        $email = $tokenData->email;

        $admin = Admin::where('email', $email)->first();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin Not Found',
            ], 404);
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        DB::table('adminpassword_reset_tokens')
            ->where('email', $email)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Password has been successfully reset!',
        ], 200);
    }


    public function AdminLogout(Request $request)
    {
        // $token = $request->bearerToken();
        // //dd($token);

        // $admin = Admin::where('api_token', $token)->first();

        // if ($admin) {
        //     $admin->api_token = null;
        //     $admin->save();

        //     return response()->json(['message' => 'Admin logged out Successfully'], 200);
        // }

        // return response()->json(['error' => 'Invalid token'], 401);

        $token = $request->bearerToken();

        $adminToken = AdminToken::where('token', $token)->first();
    
        if ($adminToken) {
            if ($adminToken->is_primary) {
                AdminToken::where('admin_id', $adminToken->admin_id)->delete();
    
                return response()->json(['message' => 'Successfully logged out from primary device'], 200);
            } else {
                $adminToken->delete();
    
                return response()->json(['message' => 'Successfully logged out from this device'], 200);
            }
        }
    
        return response()->json(['error' => 'Invalid token'], 401);
    }
}
