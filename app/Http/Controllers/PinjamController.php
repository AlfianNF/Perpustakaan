<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Pinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PinjamController extends Controller
{

    public function index(Request $request)
    {
        $query = Pinjam::query();

        // Ambil daftar field yang bisa difilter
        $filters = $request->only((new Pinjam)->getAllowedFields('filter'));

        // Terapkan filter
        if (!empty($filters)) {
            $allowedFilters = Pinjam::getAllowedFields('filter');

            foreach ($filters as $key => $value) {
                if (in_array($key, $allowedFilters) && !empty($value)) {
                    if (in_array($key, ['tgl_pinjam', 'tgl_kembali'])) {
                        $query->whereDate($key, $value);
                    } else {
                        $query->where($key, 'LIKE', "%{$value}%");
                    }
                }
            }
        }

        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('userPinjam', function ($userQuery) use ($search) {
                    $userQuery->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$search}%"]); // Search in user name
                })->orWhereHas('buku', function ($bukuQuery) use ($search) {
                    $bukuQuery->whereRaw("LOWER(title) LIKE LOWER(?)", ["%{$search}%"]); // Search in book title
                });
            });
        }

        // Ambil relasi jika ada
        $query->with((new Pinjam)->getRelations());

        // Urutkan berdasarkan tanggal dibuat
        $query->orderBy('created_at', 'DESC');

        return $query; // Kembalikan sebagai JSON
    }

    

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'id_buku' => 'required',
    //         'id_user' => 'required',
    //         'tgl_pinjam' => 'required|date',
    //         'tgl_kembali' => 'required|date',
    //     ]);

    //     // Mulai transaksi database
    //     DB::beginTransaction();

    //     try {
    //         $pinjam = new Pinjam;
    //         $pinjam->id_buku = $request->input('id_buku');
    //         $pinjam->id_user = $request->input('id_user');
    //         $pinjam->tgl_pinjam = $request->input('tgl_pinjam');
    //         $pinjam->tgl_kembali = $request->input('tgl_kembali');
    //         $pinjam->save();

    //         // Update is_pinjam pada tabel books
    //         $buku = Buku::find($request->input('id_buku'));
    //         if ($buku) {
    //             $buku->is_pinjam = true;
    //             $buku->save();
    //         } else {
    //             // Jika buku tidak ditemukan, batalkan transaksi dan kembalikan error
    //             DB::rollBack();
    //             return response()->json(['message' => 'Buku tidak ditemukan'], 404);
    //         }

    //         // Commit transaksi jika semua operasi berhasil
    //         DB::commit();

    //         return response()->json($pinjam, 201);

    //     } catch (\Exception $e) {
    //         // Batalkan transaksi jika terjadi kesalahan
    //         DB::rollBack();
    //         return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    //     }
    // }

    public function store(Request $request)
    {
        // Ambil field yang diperbolehkan untuk input ('add')
        $allowedFields = Pinjam::getAllowedFields('add');

        // Validasi hanya untuk field yang diperbolehkan
        $validated = $request->only($allowedFields);

        // Lakukan validasi tambahan sebelum penyimpanan
        $request->validate([
            'id_buku' => 'required',
            'id_user' => 'required',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali' => 'required|date',
        ]);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Simpan data peminjaman
            $pinjam = Pinjam::create($validated);

            // Update status buku agar is_pinjam menjadi true
            if ($buku = Buku::find($validated['id_buku'])) {
                $buku->update(['is_pinjam' => true]);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Buku tidak ditemukan'], 404);
            }

            // Commit transaksi
            DB::commit();

            return response()->json([
                'message' => 'Peminjaman berhasil dicatat',
                'data' => $pinjam
            ], 201);

        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Cari data berdasarkan ID
        $pinjam = Pinjam::find($id);

        if (!$pinjam) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Ambil field yang boleh diedit dari model
        $allowedFields = Pinjam::getAllowedFields('edit');

        // Validasi hanya field yang diperbolehkan
        $validatedData = $request->only($allowedFields);

        // Jika tidak ada data yang valid, kembalikan error
        if (empty($validatedData)) {
            return response()->json(['message' => 'Tidak ada field yang diperbarui'], 400);
        }

        // Lakukan update hanya dengan data yang valid
        $pinjam->update($validatedData);

        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'data' => $pinjam
        ], 200);
    }

}
