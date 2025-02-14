<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CoreService
{
    private function getModel($model)
    {
        $namespace = 'App\\Models\\';
        return $namespace . ucfirst($model);
    }

    public function handleRequest($model, Request $request, $id = null, $action = 'store')
    {
        $baseModel = $this->getModel($model);

        if (!class_exists($baseModel)) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $type = ($action === 'store') ? 'add' : 'edit';
        $allowedFields = $baseModel::getAllowedFields($type);
        $rules = $baseModel::getValidationRules($type);

        // Filter request data to only include allowed fields *before* validation
        $requestData = $request->only($allowedFields);

        $validator = Validator::make($requestData, $rules); // Validate the *filtered* data

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            DB::beginTransaction();

            if ($model === 'pinjam' && $action === 'store') {
                $buku = \App\Models\Buku::find($requestData['id_buku']);

                if (!$buku) {
                    return response()->json(['message' => 'Buku tidak ditemukan'], 404);
                }
                if ($buku->is_pinjam) {
                    return response()->json(['message' => 'Buku sedang dipinjam'], 400);
                }
            }

            if ($model === 'kembali' && $action === 'store') {
                $pinjam = \App\Models\Pinjam::find($requestData['id_pinjam']);
    
                if (!$pinjam) {
                    return response()->json(['message' => 'Data peminjaman tidak ditemukan'], 404);
                }
    
                // Hitung denda jika terlambat
                $tgl_kembali_input = \Carbon\Carbon::parse($requestData['tgl_kembali']);
                $tgl_kembali_pinjam = \Carbon\Carbon::parse($pinjam->tgl_kembali);
                $denda = $tgl_kembali_input->greaterThan($tgl_kembali_pinjam)
                    ? $tgl_kembali_pinjam->diffInDays($tgl_kembali_input) * 1000
                    : 0;
    
                // Simpan data pengembalian
                $requestData['denda'] = $denda;
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images/buku', $imageName, 'public');

                $requestData['image'] = $imagePath; // Store the *path* in the database
            }


            if ($action === 'store') {
                $modelInstance = new $baseModel();
                $modelInstance->fill($requestData); // Use the *filtered* $requestData
                $modelInstance->save();

                if ($model === 'pinjam') {
                    $buku = \App\Models\Buku::find($requestData['id_buku']); // Re-fetch Buku
                    $buku->update(['is_pinjam' => true]); // Use update() with array
                }

                if ($model === 'kembali') {
                    $buku = \App\Models\Buku::find($requestData['id_buku']);
                    if ($buku) {
                        $buku->update(['is_pinjam' => false]);
                    }
                    $pinjam->update(['status' => 'dikembalikan']);
                }

                $statusCode = 201;
            } else { // update
                $modelInstance = $baseModel::find($id);
                if (!$modelInstance) {
                    return response()->json(['message' => 'Data not found'], 404);
                }
                $modelInstance->fill($requestData); // Use the *filtered* $requestData
                $modelInstance->save();
                $statusCode = 200;
            }

            DB::commit();
            return response()->json($modelInstance, $statusCode);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to process request: ' . $e->getMessage()], 500);
        }
    }
}