<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request){
        $query = User::query();

        // Mengambil daftar field yang bisa difilter
        $filters = $request->only((new User)->getAllowedFields('filter'));

        // Menerapkan filter dari request
        if (!empty($filters)) {
            $allowedFilters = User::getAllowedFields('filter');

            foreach ($filters as $key => $value) {
                if (in_array($key, $allowedFilters) && !empty($value)) {
                    $query->whereRaw("LOWER($key) LIKE LOWER(?)", ["%{$value}%"]);
                }
            }
        }

        $query->with((new User)->getRelations());

        $query->orderBy('created_at', 'DESC');

        return $query;
    }

    public function update(Request $request, $id)
    {
        // Cari data berdasarkan ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);

        }

        // Ambil field yang boleh diedit dari model
        $allowedFields = User::getAllowedFields('edit');

        // Validasi hanya field yang diperbolehkan
        $validatedData = $request->only($allowedFields);

        // Jika tidak ada data yang valid, kembalikan error
        if (empty($validatedData)) {
            return response()->json(['message' => 'Tidak ada field yang diperbarui'], 400);
        }

        // Lakukan update hanya dengan data yang valid
        $user->update($validatedData);

        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'data' => $user
        ], 200);
    }
}
