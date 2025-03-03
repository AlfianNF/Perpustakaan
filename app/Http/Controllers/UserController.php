<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

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
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            $allowedFields = User::getAllowedFields('edit');
            $validatedData = $request->only($allowedFields);

            if (!$request->filled('no_induk')) {
                $validatedData['no_induk'] = $user->no_induk;
            } else {
                $rules = User::getValidationRules('edit', $id);
                $request->validate(['no_induk' => $rules['no_induk']]);
            }

            if ($request->hasFile('image')) {
                $request->validate(['image' => 'image|mimes:jpeg,png,jpg,gif|max:2048']);
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }
                $validatedData['image'] = $request->file('image')->store('images/user', 'public');
            }

            // Handle password update
            if (isset($validatedData['password'])) {
                if (empty($validatedData['password'])) {
                    unset($validatedData['password']); //hapus dari array agar tidak null
                } else {
                    $validatedData['password'] = Hash::make($validatedData['password']);
                }
            }

            if (empty($validatedData)) {
                return response()->json(['message' => 'Tidak ada field yang diperbarui'], 400);
            }

            $user->update($validatedData);

            return response()->json([
                'message' => 'Data berhasil diperbarui',
                'data' => $user
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada database.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
