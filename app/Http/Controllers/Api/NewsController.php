<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\News;

class NewsController extends Controller
{
    public function ViewNews()
    {
        $news = News::select('id', 'title', 'description', 'date', 'time')->get();

        if ($news->isEmpty()) {
            return response()->json(['message' => 'News not found'], 404);
        }

        return response()->json(['data' => $news], 200);
    }

    public function EditNews(Request $request, $id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $news->update($request->all());
        return response()->json(['message' => 'News updated successfully', 'data' => $news], 200);
    }
}
