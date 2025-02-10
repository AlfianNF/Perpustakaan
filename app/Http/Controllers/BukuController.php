<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
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

        // Mengambil daftar field yang bisa difilter
        $filters = $request->only((new Buku)->getAllowedFields('filter'));

        // Menerapkan filter dari request
        if (!empty($filters)) {
            $allowedFilters = Buku::getAllowedFields('filter');

            foreach ($filters as $key => $value) {
                if (in_array($key, $allowedFilters) && !empty($value)) {
                    $query->whereRaw("LOWER($key) LIKE LOWER(?)", ["%{$value}%"]);
                }
            }
        }

        // Mengambil relasi jika ada
        $query->with((new Buku)->getRelations());

        // Mengurutkan dan filter tambahan
        $query->orderBy('created_at', 'DESC')->where('is_pinjam', false);

        return $query;
    }

    public function store(Request $request)
    {
        // Ambil field yang boleh di-insert
        $allowedFields = Buku::getAllowedFields('add');

        // Validasi hanya field yang diperbolehkan
        $validated = $request->only($allowedFields);

        // Simpan data ke database
        $buku = Buku::create($validated);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data' => $buku
        ]);
    }

    public function update(Request $request, $id)
    {
        // Cari data berdasarkan ID
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Ambil field yang boleh diedit dari model
        $allowedFields = Buku::getAllowedFields('edit');

        // Validasi hanya field yang diperbolehkan
        $validatedData = $request->only($allowedFields);

        // Jika tidak ada data yang valid, kembalikan error
        if (empty($validatedData)) {
            return response()->json(['message' => 'Tidak ada field yang diperbarui'], 400);
        }

        // Lakukan update hanya dengan data yang valid
        $buku->update($validatedData);

        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'data' => $buku
        ], 200);
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
