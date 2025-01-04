<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivacyAndPolicy;
use Illuminate\Support\Facades\Validator;


class PrivacyAndPolicyController extends Controller
{
    public function ViewPrivacyandPolicy()
    {
        $privacypolicy = PrivacyAndPolicy::select('id','content')->get();

        if ($privacypolicy->isEmpty()) {
            return response()->json(['message' => 'Pricay & Policy not found'], 404);
        }

        return response()->json(['data' => $privacypolicy], 200);
    }

    public function EditPrivacyandPolicy(Request $request, $id)
    {
        $privacypolicy = PrivacyAndPolicy::find($id);

        if (!$privacypolicy) {
            return response()->json(['message' => 'Pricay & Policy not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $privacypolicy->update($request->all());
        return response()->json(['message' => 'Pricay & Policy updated successfully', 'data' => $privacypolicy], 200);
    }
}
