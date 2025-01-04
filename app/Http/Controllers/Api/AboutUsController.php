<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\AboutUs;

class AboutUsController extends Controller
{
    public function ViewAboutUs()
    {
        $about = AboutUs::select('id', 'content')->get();

        if ($about->isEmpty()) {
            return response()->json(['message' => 'AboutUs not found'], 404);
        }
        
        return response()->json(['message' => 'AboutUs fetched successfully', 'data' => $about], 201);
    }

    public function EditAboutUs(Request $request, $id)
    {
        $about = AboutUs::find($id);

        if (!$about) {
            return response()->json(['message' => 'AboutUs not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $about->update($request->all());
        return response()->json(['message' => 'AboutUs updated successfully', 'data' => $about], 200);
    }
}
