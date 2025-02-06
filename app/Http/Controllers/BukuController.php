<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BukuController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|unique:bukus,isbn',
            'author' => 'required',
            'publish_date' => 'required|date',
            'category' => 'required'
        ]);

        $buku = new Buku;
        $buku->title = $request->input('title');
        $buku->isbn = $request->input('isbn');
        $buku->author = $request->input('author');
        $buku->publish_date = $request->input('publish_date');
        $buku->category = $request->input('category');
        $buku->save();

        return response()->json($buku,201);
    }

    public function update(Request $request,$id){
        $model = Buku::find($id);

        if (!$model) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $validatedData = $request->validate([
            'title' => 'nullable|string',
            'author' => 'nullable|string',
            'isbn' => 'nullable|string',
            'publish_date' => 'nullable|date',
            'category' => 'nullable|string',
        ]);

        $model->update([
            'title' => $validatedData['title'] ?? $model->title,
            'author' => $validatedData['author'] ?? $model->author,
            'isbn' => $validatedData['isbn'] ?? $model->isbn,
            'publish_date' => $validatedData['publish_date'] ?? $model->publish_date,
            'category' => $validatedData['category'] ?? $model->category,
        ]);

        return response()->json($model,200);
    }
}
