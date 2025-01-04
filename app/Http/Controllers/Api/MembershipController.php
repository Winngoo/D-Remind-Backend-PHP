<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Illuminate\Support\Facades\Validator;
use App\Models\MembershipDetails;
use App\Models\Subscription;
use App\Models\User;


class MembershipController extends Controller
{
    public function UserMembershipDetails($id)
    {
        $subscription = Subscription::where('user_id', $id)->where('status', 'completed')->first();

        if (!$subscription) {
            return response()->json([
                'status' => 'error',
                'message' => 'No subscription found for the user.'
            ], 404);
        }

        $membershipDetails = MembershipDetails::where('id', $subscription->membership_id)
            ->select('id', 'membership_name', 'membership_benefits', 'membership_fee', 'vat', 'total_cost', 'validity')
            ->first();

        if (!$membershipDetails) {
            return response()->json([
                'status' => 'error',
                'message' => 'Membership details not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'membership_details' => $membershipDetails,
            'start_date'=> $subscription->payment_date,
            'end_date' => $subscription->end_date
        ], 200);
    }


    public function UserMembershipHistory($id)
    {

        $subscriptionDetails = Subscription::where('user_id', $id)
            ->select('user_id', 'membership_id', 'payment_date', 'end_date', 'amount', 'plan_type', 'status')
            ->get();

        if (!$subscriptionDetails) {
            return response()->json([
                'status' => 'error',
                'message' => 'Subscription history not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'subscription_history' => $subscriptionDetails
        ], 200);
    }
}
