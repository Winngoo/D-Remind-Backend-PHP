<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TermsAndCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class TermsAndConditionController extends Controller
{
    public function ViewTermsandCondition()
    {
        $terms = TermsAndCondition::select('id','content')->get();

        if ($terms->isEmpty()) {
            return response()->json(['message' => 'Terms & Conditions not found'], 404);
        }

        return response()->json(['data' => $terms], 200);
    }

    public function EditTermsandCondition(Request $request, $id)
    {
        $terms = TermsAndCondition::find($id);

        if (!$terms) {
            return response()->json(['message' => 'Terms & Conditions not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $terms->update($request->all());
        return response()->json(['message' => 'Terms & Conditions updated successfully', 'data' => $terms], 200);
    }
}
