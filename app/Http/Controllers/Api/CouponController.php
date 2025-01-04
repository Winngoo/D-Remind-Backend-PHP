<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function CreateCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string|unique:coupons|regex:/^[A-Za-z0-9\s\-]+$/',
            'description' => 'nullable|string|max:255',
            'discount' => 'required|numeric|min:0|max:100',
            'discount_type' => 'required|in:percentage,fixed',
            'applicable_plans' => 'nullable|array',
            'applicable_plans.*' => 'string|max:100',
            'expiration_date' => 'required|date|after:today',
            'status' => 'required|boolean',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
    
        $coupon =    Coupon::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Coupon created successfully',
            'data' => $coupon,
        ], 201);
    }

    public function CreateView()
    {
        $coupondetails = Coupon::select('id', 'coupon_code', 'description', 'discount', 'discount_type', 'applicable_plans', 'expiration_date', 'status')->get();

        if ($coupondetails->isEmpty()) {
            return response()->json([
                'message' => 'No Coupons found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Coupon fetched successfully',
            'data' => $coupondetails,
        ], 201);
    }

    public function CreateEdit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => "required|string|unique:coupons,coupon_code,$id",
            'description' => 'nullable|string|max:255',
            'discount' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'applicable_plans' => 'required|array',
            'applicable_plans.*' => 'string',
            'expiration_date' => 'required|date|after:today',
            'status' => 'required|boolean',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $coupon = Coupon::find($id);
    
        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }
    
        $coupon->update($request->all());
    
        return response()->json(['message' => 'Coupon updated successfully', 'data' => $coupon], 200);
        
    }

    public function CreateDelete($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return response()->json(['message' => 'Coupon deleted successfully']);
    }
}
