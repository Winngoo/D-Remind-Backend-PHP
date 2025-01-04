<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Disclaimer;
use Illuminate\Support\Facades\Validator;


class DisclaimerController extends Controller
{
    public function ViewDisclaimer()
    {
        $disclaimer = Disclaimer::select('id','content')->get();

        if ($disclaimer->isEmpty()) {
            return response()->json(['message' => 'Disclaimer not found'], 404);
        }

        return response()->json(['data' => $disclaimer], 200);
    }

    public function EditDisclaimer(Request $request, $id)
    {
        $disclaimer = Disclaimer::find($id);

        if (!$disclaimer) {
            return response()->json(['message' => 'Disclaimer not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $disclaimer->update($request->all());
        return response()->json(['message' => 'Disclaimer updated successfully', 'data' => $disclaimer], 200);
    }
    
}
