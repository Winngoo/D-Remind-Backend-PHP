<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\UserFAQ;

class FaqController extends Controller
{
    public function FaqviewUsers()
    {
        $faqs = DB::table('user_faqs')
            ->select('id', 'question', 'answer')
            ->get();

        if ($faqs->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No FAQs available',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'FAQs retrieved successfully.',
            'data' => $faqs,
        ], 200);
    }

    public function CreateFAQs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $faqs = UserFAQ::create($request->all());

        return response()->json(['message' => 'FAQ created successfully', 'data' => $faqs], 201);
    }

    public function ViewFAQs()
    {
        $faqs = UserFAQ::select('id','question','answer')->get();

        if ($faqs->isEmpty()) {
            return response()->json(['message' => 'FAQ not found'], 404);
        }

        return response()->json(['message' => 'FAQ fetched successfully', 'data' => $faqs], 201);
    }

    public function EditFAQs(Request $request, $id)
    {
        $faqs = UserFAQ::find($id);

        if (!$faqs) {
            return response()->json(['message' => 'FAQ not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $faqs->update($request->all());
        return response()->json(['message' => 'FAQ updated successfully', 'data' => $faqs], 200);
    }

    public function DeleteFAQs($id)
    {
        $faqs = UserFAQ::find($id);

        if (!$faqs) {
            return response()->json(['message' => 'FAQ not found'], 404);
        }

        $faqs->delete();
        return response()->json(['message' => 'FAQ deleted successfully'], 200);
    }

}
