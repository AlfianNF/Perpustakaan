<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Pinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BukuController extends Controller
{
    // public function store(Request $request){
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'isbn' => 'required|unique:bukus,isbn',
    //         'author' => 'required',
    //         'publish_date' => 'required|date',
    //         'category' => 'required'
    //     ]);

    //     $buku = new Buku;
    //     $buku->title = $request->input('title');
    //     $buku->isbn = $request->input('isbn');
    //     $buku->author = $request->input('author');
    //     $buku->publish_date = $request->input('publish_date');
    //     $buku->category = $request->input('category');
    //     $buku->save();

    //     return response()->json($buku,201);
    // }

    public function index(Request $request){
        $query = Buku::query();
        
        $filters = $request->only(Buku::getAllowedFields('filter'));

        if (!empty($filters)) {
            if (isset($filters['category'])) {
                $categoryValue = $filters['category'];
                if (!empty($categoryValue)) {
                    $query->whereHas('category', function ($q) use ($categoryValue) {
                        $q->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$categoryValue}%"]);
                    });
                }
                unset($filters['category']);
            }
        }
    
        $search = $request->input('search'); 

        if ($search) { 
            $allowedSearch = Buku::getAllowedFields('search');
            $query->where(function ($q) use ($search, $allowedSearch) {
                foreach ($allowedSearch as $field) { 
                    $q->orWhereRaw("LOWER($field) LIKE LOWER(?)", ["%{$search}%"]);
                }
            });
        }   
        $query->with((new Buku)->getRelations());
        $query->orderByRaw('LENGTH(title), title')->where('is_pinjam', false);

        return $query;
    }
    

    public function store(Request $request)
    {
        $allowedFields = Buku::getAllowedFields('add');
        $validated = $request->only($allowedFields);

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
    
            $validated['image'] = $request->file('image')->store('images/buku', 'public');    
        }
        $buku = Buku::create($validated);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data' => $buku
        ]);
    }


    public function update(Request $request, $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
        $allowedFields = Buku::getAllowedFields('edit');
        $validatedData = $request->only($allowedFields);

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048' // Maksimal 2MB
            ]);
            if ($buku->image) {
                Storage::disk('public')->delete($buku->image);
            }
            $validatedData['image'] = $request->file('image')->store('images/bukus', 'public');
        }

        if (empty($validatedData)) {
            return response()->json(['message' => 'Tidak ada field yang diperbarui'], 400);
        }
        $buku->update($validatedData);

        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'data' => [
                'id' => $buku->id,
                'title' => $buku->title,
                'isbn' => $buku->isbn,
                'author' => $buku->author,
                'category' => $buku->category,
                'publish_date' => $buku->publish_date,
                'is_pinjam' => $buku->is_pinjam,
                'image_url' => $buku->image ? asset('storage/' . $buku->image) : null 
            ]
        ], 200);
    }

    public function pinjam(){
        $user = auth()->user()->id;
        $pinjam = Pinjam::where('id_user', $user)
                        ->where('status', 'dipinjam')
                        ->with(['buku:id,title,isbn,author,category,image','buku.category:id,name'])
                        ->get();
        
        if($pinjam->isEmpty()){
            return response()->json(['message' => 'Anda belum meminjam buku'],200);
        }
        return response()->json($pinjam,200);
    }

    public function recentlyRead(){
        $user = auth()->user()->id;
        $pinjam = Pinjam::where('id_user', $user)
                        ->where('status', 'dikembalikan')
                        ->with(['buku:id,title,isbn,author,category,image','buku.category:id,name'])
                        ->get();
        
        if($pinjam->isEmpty()){
            return response()->json(['message' => 'Anda belum meminjam buku'],200);
        }
        return response()->json($pinjam,200);
    }


    // public function update(Request $request,$id){
    //     $model = Buku::find($id);

    //     if (!$model) {
    //         return response()->json(['message' => 'Data not found'], 404);
    //     }

    //     $validatedData = $request->validate([
    //         'title' => 'nullable|string',
    //         'author' => 'nullable|string',
    //         'isbn' => 'nullable|string',
    //         'publish_date' => 'nullable|date',
    //         'category' => 'nullable|string',
    //     ]);

    //     $model->update([
    //         'title' => $validatedData['title'] ?? $model->title,
    //         'author' => $validatedData['author'] ?? $model->author,
    //         'isbn' => $validatedData['isbn'] ?? $model->isbn,
    //         'publish_date' => $validatedData['publish_date'] ?? $model->publish_date,
    //         'category' => $validatedData['category'] ?? $model->category,
    //     ]);

    //     return response()->json($model,200);
    // }

    // public function update(Request $request, $id)
    // {
    //     $model = Buku::find($id);

    //     if (!$model) {
    //         return response()->json(['message' => 'Data not found'], 404);
    //     }

    //     // Ambil field yang boleh diedit dari model
    //     $allowedFields = Buku::getAllowedFields('edit');

    //     // Validasi hanya field yang boleh diedit
    //     $validatedData = $request->only($allowedFields);

    //     // Jika tidak ada data yang valid, kembalikan error
    //     if (empty($validatedData)) {
    //         return response()->json(['message' => 'Tidak ada field yang boleh diperbarui'], 400);
    //     }

    //     // Lakukan update
    //     $model->update($validatedData);

    //     return response()->json(['message' => 'Data berhasil diperbarui', 'data' => $model], 200);
    // }

}
