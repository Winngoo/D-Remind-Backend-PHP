<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\SubCategory;

class CategoriesController extends Controller
{

    public function index(Request $request, $userId)
    {
        $categories = Category::with(['subCategories' => function ($query) use ($userId) {
            $query->where(function ($query) use ($userId) {
                $query->whereNull('user_id') 
                    ->orWhere('user_id', $userId);
            })->select('id', 'category_id', 'name');
        }])->select('id', 'name', 'icon')->get();

        return response()->json($categories, 200);
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'subCategories' => 'required|array',
            'icon' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $existingCategory = Category::where('name', 'LIKE', $request->name)->first();
        if ($existingCategory) {
            return response()->json([
                'message' => 'Category name already exists.'
            ], 422);
        }

        $category = Category::create(['name' => $request->name, 'icon' => $request->icon]);

        foreach ($request->subCategories as $subCategory) {
            $category->subCategories()->create(['name' => $subCategory]);
        }

        return response()->json(['message' => 'Category created successfully'], 201);
    }


    public function show($id)
    {
        $category = Category::with(['subCategories' => function ($query) {
            $query->select('id', 'category_id', 'name');
        }])->where('id', $id)->select('id', 'name')->get();
        return response()->json($category, 200);
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'subCategories' => 'required|array',
            'icon' => 'nullable|string|max:255',
        ]);

        $existingCategory = Category::where('name', 'LIKE', $request->name)
            ->where('id', '!=', $id)
            ->first();

        if ($existingCategory) {
            return response()->json([
                'message' => 'Category name already exists.'
            ], 422);
        }

        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found.'
            ], 404);
        }

        $category->name = $request->name;
        $category->icon = $request->icon;
        $category->save();

        $category->subCategories()->delete();
        foreach ($request->subCategories as $subCategory) {
            $category->subCategories()->create(['name' => $subCategory]);
        }

        return response()->json([
            'message' => 'Category updated successfully.'
        ], 200);
    }


    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found.'
            ], 404);
        }

        $category->subCategories()->delete();
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.'
        ], 200);
    }
}
