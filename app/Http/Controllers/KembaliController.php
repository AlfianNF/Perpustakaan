<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Buku;
use App\Models\Pinjam;
use App\Models\Kembali;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KembaliController extends Controller
    {
    public function store(Request $request)
    {
        $request->validate([
            'id_buku' => 'required',
            'id_user' => 'required',
            'id_pinjam' => 'required',
            'tgl_kembali' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $pinjam = Pinjam::find($request->input('id_pinjam'));

            if (!$pinjam) {
                return response()->json(['message' => 'Data peminjaman tidak ditemukan'], 404);
            }

            $tgl_kembali_input = Carbon::parse($request->input('tgl_kembali'));
            $tgl_kembali_pinjam = Carbon::parse($pinjam->tgl_kembali);
            
            $denda = 0;

            if ($tgl_kembali_input->greaterThan($tgl_kembali_pinjam)) {
                $selisih_hari = $tgl_kembali_pinjam->diffInDays($tgl_kembali_input);
                $tarif_denda_per_hari = 1000;
                $denda = $selisih_hari * $tarif_denda_per_hari;
            }


            $kembali = new Kembali();
            $kembali->id_buku = $request->input('id_buku');
            $kembali->id_user = $request->input('id_user');
            $kembali->id_pinjam = $request->input('id_pinjam');
            $kembali->tgl_kembali = $request->input('tgl_kembali');
            $kembali->denda = $denda;
            $kembali->save();

            $buku = Buku::find($request->input('id_buku'));
            if ($buku) {
                $buku->is_pinjam = false;
                $buku->save();
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Buku tidak ditemukan'], 404);
            }

            DB::commit();

            return response()->json([
                'message' => 'Pengembalian berhasil',
                'data' => $kembali
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function denda(Request $request, $id)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:0',
        ], [
            'jumlah_bayar.required' => 'Jumlah bayar wajib diisi.',
            'jumlah_bayar.numeric' => 'Jumlah bayar harus berupa angka.',
            'jumlah_bayar.min' => 'Jumlah bayar tidak boleh kurang dari 0.'
        ]);

        $kembali = Kembali::find($id);

        if (!$kembali) {
            return response()->json(['message' => 'Data kembali tidak ditemukan.'], 404);
        }

        if ($kembali->denda == 0) {
            return response()->json(['message' => 'Tidak ada denda yang harus dibayarkan.'], 204);
        }

        $jumlahBayar = $request->input('jumlah_bayar');

        $sisaDenda =$kembali->denda - $jumlahBayar;

        $kembalian = 0;
        if ($sisaDenda < 0) {
            $kembalian = abs($sisaDenda);
            $sisaDenda = 0;
        }

        try {
            DB::transaction(function () use ($kembali, $sisaDenda) {
                $kembali->denda = $sisaDenda;
                $kembali->save();

                // Jika Anda ingin menyimpan history, gunakan ini:
                // DB::table('history_denda')->insert([
                //     'id_kembali' => $kembali->id, // Ganti id_pinjam dengan id_kembali
                //     'jumlah_bayar' => $jumlahBayar,
                //     'sisa_denda' => $kembali->denda,
                //     'denda_awal' => $dendaAwal,
                //     'created_at' => now(),
                // ]);
            });

            $response = [
                'message' => 'Pembayaran denda berhasil.',
                'sisa_denda' => $kembali->denda,
            ];

            if ($kembalian > 0) {
                $response['kembalian'] = $kembalian;
                $response['message'] = 'Pembayaran denda berhasil. Anda mendapatkan kembalian sebesar Rp.'.$kembalian;
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan saat pembayaran denda: ' . $e->getMessage()], 500);
        }
    }
}
