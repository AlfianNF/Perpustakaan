<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Pinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PinjamController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_buku' => 'required',
            'id_user' => 'required',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali' => 'required|date',
        ]);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            $pinjam = new Pinjam;
            $pinjam->id_buku = $request->input('id_buku');
            $pinjam->id_user = $request->input('id_user');
            $pinjam->tgl_pinjam = $request->input('tgl_pinjam');
            $pinjam->tgl_kembali = $request->input('tgl_kembali');
            $pinjam->save();

            // Update is_pinjam pada tabel books
            $buku = Buku::find($request->input('id_buku'));
            if ($buku) {
                $buku->is_pinjam = true;
                $buku->save();
            } else {
                // Jika buku tidak ditemukan, batalkan transaksi dan kembalikan error
                DB::rollBack();
                return response()->json(['message' => 'Buku tidak ditemukan'], 404);
            }

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            return response()->json($pinjam, 201);

        } catch (\Exception $e) {
            // Batalkan transaksi jika terjadi kesalahan
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
