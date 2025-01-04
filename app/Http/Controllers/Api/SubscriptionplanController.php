<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\MembershipDetails;

class SubscriptionplanController extends Controller
{

    public function CreateSubscriptionPlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'membership_name' => 'required|string|max:255',
            'membership_benefits' => 'required|string',
            'membership_fee' => 'required|numeric|min:0',
            'vat' => 'required|numeric|min:0',
            'total_cost' => 'required|numeric|min:0',
            'validity' => 'required|in:year,month',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        $plan = MembershipDetails::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Subscription plan created successfully',
            'data' => $plan,
        ], 201);
    }

    public function ViewSubscriptionPlan()
    {
        $plans = MembershipDetails::select('id', 'membership_name', 'membership_benefits', 'membership_fee', 'vat', 'total_cost', 'validity')->get();

        return response()->json([
            'success' => true,
            'data' => $plans,
        ], 200);
    }

    public function EditSubscriptionPlan(Request $request, $id)
    {

        $plan = MembershipDetails::find($id);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription plan not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'membership_name' => 'string|max:255',
            'membership_benefits' => 'string',
            'membership_fee' => 'numeric|min:0',
            'vat' => 'numeric|min:0',
            'total_cost' => 'numeric|min:0',
            'validity' => 'required|in:year,month',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        $plan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Subscription plan updated successfully',
            'data' => $plan,
        ], 200);
    }

    public function DeleteSubscriptionPlan($id)
    {
        $plan = MembershipDetails::find($id);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription plan not found',
            ], 404);
        }

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscription plan deleted successfully',
        ], 200);
    }
}
