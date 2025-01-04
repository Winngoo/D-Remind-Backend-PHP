<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\HowItWorks;

class HowItWorksController extends Controller
{
    public function CreateHowItWorks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $workflow = HowItWorks::create($request->all());
        return response()->json(['message' => 'HowItWorks created successfully', 'data' => $workflow], 201);
    }

    public function ViewHowItWorks()
    {
        $workflow = HowItWorks::select('id','title','description','status')->get();

        if ($workflow->isEmpty()) {
            return response()->json(['message' => 'HowItWorks not found'], 404);
        }

        return response()->json(['message' => 'HowItWorks fetched successfully', 'data' => $workflow], 201);
    }

    public function EditHowItWorks(Request $request , $id)
    {
        $workflow = HowItWorks::find($id);

        if (!$workflow) {
            return response()->json(['message' => 'HowItWorks not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'status' => 'in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $workflow->update($request->all());
        return response()->json(['message' => 'HowItWorks updated successfully', 'data' => $workflow], 200);
    }

    public function DeleteHowItWorks($id)
    {
        $workflow = HowItWorks::find($id);

        if (!$workflow) {
            return response()->json(['message' => 'HowItWorks not found'], 404);
        }

        $workflow->delete();
        return response()->json(['message' => 'HowItWorks deleted successfully'], 200);
    }

}
