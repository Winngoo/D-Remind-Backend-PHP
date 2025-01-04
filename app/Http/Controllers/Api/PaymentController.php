<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Charge;
use Illuminate\Support\Facades\Validator;
use App\Models\MembershipDetails;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyUserMail;
use App\Mail\ReceiptMail;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function Membershippaymentdetails()
    {
        $membershipDetails = MembershipDetails::select('id', 'membership_name', 'membership_benefits', 'membership_fee', 'vat', 'total_cost', 'validity')->get();
        return response()->json(['success' => true, 'data' => $membershipDetails]);
    }

    public function Membershippayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stripeToken' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'membership_id' => 'required|exists:membership_details,id',
            'name_on_card' => 'required|string|max:255',
            'card_number' => 'required|numeric|digits:16',
            'exp_date' => 'required|expiration_date',
            'cvv' => 'required|numeric|digits:3',
            'plan_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $membership = MembershipDetails::find($request->membership_id);

        if (!$membership) {
            return response()->json([
                'status' => 'error',
                'message' => 'The membership is Invalid!',
            ], 401);
        }

        $existingSubscription = Subscription::where('user_id', $request->user_id)
            ->where('membership_id', $request->membership_id)
            ->where('end_date', '>', now())
            ->first();

        if ($existingSubscription) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active subscription for this membership. Please wait until the current subscription expires.',
            ], 403);
        }


        $amount = $membership->total_cost;
        $currentDate = now();

        $endDate = $membership->validity === 'year'
            ? $currentDate->copy()->addYear()
            : $currentDate->copy()->addMonth();

        //return $endDate;

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $charge = Charge::create([
                'amount' => $amount * 100,
                'currency' => 'eur',
                'source' => $request->input('stripeToken'),
                'description' => "{$request->plan_type} Plan for {$membership->membership_name}",
            ]);

            Subscription::create([
                'user_id' => $request->user_id,
                'membership_id' => $membership->id,
                'payment_date' => now(),
                'amount' => $membership->total_cost,
                'plan_type' => $request->plan_type,
                'end_date' => $endDate,
            ]);

            $user = User::find($request->user_id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            $viewLink = route('user.verify', ['user_id' => $request->user_id]);
            Mail::to($user->email)->send(new VerifyUserMail($viewLink));

            Mail::to($user->email)->send(new ReceiptMail($charge->receipt_url));

            return response()->json([
                'success' => true,
                'message' => 'Payment successful and we sent the verify link to your mail.',
                'user_id' => $request->user_id,
                'receipt' => $charge->receipt_url,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function TransactionHistory()
    {
        $transactions = Subscription::with(['membership', 'user'])
            ->orderBy('payment_date', 'desc')
            ->get();

        $formattedTransactions = $transactions->map(function ($transaction) {

            return [
                'id' => $transaction->id,
                'user_name' => $transaction->user->full_name,
                'membership_name' => $transaction->membership->membership_name,
                'payment_date' => \Carbon\Carbon::parse($transaction->payment_date)->format('Y-m-d'),
                'end_date' => \Carbon\Carbon::parse($transaction->end_date)->format('Y-m-d'),
                'amount' => $transaction->amount,
                'plan_type' => $transaction->plan_type,
                'status' => $transaction->status,
                'membership_fee' => $transaction->membership->membership_fee,
                'vat' => $transaction->membership->vat,
                'total_cost' => $transaction->membership->total_cost,
                'validity' => $transaction->membership->validity,
            ];
        });

        return response()->json([
            'success' => true,
            'transactions' => $formattedTransactions,
        ], 200);
    }

    public function SearchPayment(Request $request)
    {
        $query = Subscription::with(['membership', 'user']);

        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'transaction_id' => 'nullable|exists:subscriptions,id',
            'plan_name' => 'nullable|string|exists:membership_details,membership_name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('transaction_id')) {
            $query->where('id', $request->transaction_id);
        }
        if ($request->has('plan_name')) {
            $query->whereHas('membership', function ($q) use ($request) {
                $q->where('membership_name', 'like', '%' . $request->plan_name . '%');
            });
        }

        $transactions = $query->get();

        $formattedTransactions = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'user_name' => $transaction->user->full_name,
                'membership_name' => $transaction->membership->membership_name,
                'payment_date' => \Carbon\Carbon::parse($transaction->payment_date)->format('Y-m-d'),
                'end_date' => \Carbon\Carbon::parse($transaction->end_date)->format('Y-m-d'),
                'amount' => $transaction->amount,
                'plan_type' => $transaction->plan_type,
                'status' => $transaction->status,
                'membership_fee' => $transaction->membership->membership_fee,
                'vat' => $transaction->membership->vat,
                'total_cost' => $transaction->membership->total_cost,
                'validity' => $transaction->membership->validity,
            ];
        });

        return response()->json([
            'success' => true,
            'transactions' => $formattedTransactions,
        ], 200);
    }


    public function SearchFilter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:completed,failed,cancelled,expired',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'plan_name' => 'nullable|string|exists:membership_details,membership_name',
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

        $query = Subscription::with(['membership', 'user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
        }

        if ($request->has('plan_name')) {
            $query->whereHas('membership', function ($q) use ($request) {
                $q->where('membership_name', 'like', '%' . $request->plan_name . '%');
            });
        }


        $transactions = $query->orderBy('payment_date', 'asc')->get();

        $formattedTransactions = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'user_name' => $transaction->user->full_name,
                'membership_name' => $transaction->membership->membership_name,
                'payment_date' => \Carbon\Carbon::parse($transaction->payment_date)->format('Y-m-d'),
                'end_date' => \Carbon\Carbon::parse($transaction->end_date)->format('Y-m-d'),
                'amount' => $transaction->amount,
                'plan_type' => $transaction->plan_type,
                'status' => $transaction->status,
                'membership_fee' => $transaction->membership->membership_fee,
                'vat' => $transaction->membership->vat,
                'total_cost' => $transaction->membership->total_cost,
                'validity' => $transaction->membership->validity,
            ];
        });

        return response()->json([
            'success' => true,
            'transactions' => $formattedTransactions
        ], 200);
    }
}
