<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $category = new Category();
        $category->name = $request->input('name');
        $category->description = $request->input('description');
        $category->save();

        return response()->json($category,201);
    }

    public function update(Request $request,$id){
        $model = Category::find($id);

        if (!$model) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $model->update([
            'name' => $validatedData['name'] ?? $model->name,
            'description' => $validatedData['description'] ?? $model->description,
        ]);

        return response()->json($model,200);
    }
}
