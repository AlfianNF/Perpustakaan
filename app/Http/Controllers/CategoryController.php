<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // public function store(Request $request){
    //     $request->validate([
    //         'name' => 'required|string|max:50',
    //         'description' => 'nullable|string',
    //     ]);

    //     $category = new Category();
    //     $category->name = $request->input('name');
    //     $category->description = $request->input('description');
    //     $category->save();

    //     return response()->json($category,201);
    // }

    public function index(Request $request)
    {
        $query = Category::select('name', 'description');
        $query = Category::query();

        $search = $request->input('search');

        if (!empty($search)) {
            $query->where('name', 'ILIKE', "%{$search}%"); 
        }

        $query->orderBy('name', 'asc');

        return $query;
    }


    public function store(Request $request)
    {
        // Ambil field yang boleh di-insert
        $allowedFields = Category::getAllowedFields('add');

        // Validasi hanya field yang diperbolehkan
        $validated = $request->only($allowedFields);

        // Simpan data ke database
        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category berhasil ditambahkan',
            'data' => $category
        ]);
    }

    // public function update(Request $request,$id){
    //     $model = Category::find($id);

    //     if (!$model) {
    //         return response()->json(['message' => 'Data not found'], 404);
    //     }

    //     $validatedData = $request->validate([
    //         'name' => 'nullable|string',
    //         'description' => 'nullable|string',
    //     ]);

    //     $model->update([
    //         'name' => $validatedData['name'] ?? $model->name,
    //         'description' => $validatedData['description'] ?? $model->description,
    //     ]);

    //     return response()->json($model,200);
    // }

    public function update(Request $request, $id)
    {
        $model = Category::find($id);

        if (!$model) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        // Ambil field yang boleh diedit dari model
        $allowedFields = Category::getAllowedFields('edit');

        // Validasi hanya field yang boleh diedit
        $validatedData = $request->only($allowedFields);

        // Jika tidak ada data yang valid, kembalikan error
        if (empty($validatedData)) {
            return response()->json(['message' => 'Tidak ada field yang boleh diperbarui'], 400);
        }

        // Lakukan update
        $model->update($validatedData);

        return response()->json(['message' => 'Data berhasil diperbarui', 'data' => $model], 200);
    }
}
