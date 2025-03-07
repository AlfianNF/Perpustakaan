<?php

namespace App\Http\Services;

use Carbon\Carbon;
use App\Models\Buku;
use App\Models\Pinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
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

        $requestData = $request->only($allowedFields);

        $validator = Validator::make($requestData, $rules); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            DB::beginTransaction();

            if ($model === 'pinjam' && $action === 'store') {
                $buku = Buku::find($requestData['id_buku']);

                if (!$buku) {
                    return response()->json(['message' => 'Buku tidak ditemukan'], 404);
                }
                if ($buku->is_pinjam) {
                    return response()->json(['message' => 'Buku sedang dipinjam'], 400);
                }
            }

            if ($model === 'kembali' && $action === 'store') {
                $pinjam = Pinjam::where('id', $requestData['id_pinjam'])
                    ->where('status', 'dipinjam')
                    ->first();

                if (!$pinjam) {
                    $pinjamCheck = Pinjam::find($requestData['id_pinjam']);
                    if ($pinjamCheck && $pinjamCheck->status == 'dikembalikan') {
                        throw new \Exception("Peminjaman sudah dikembalikan sebelumnya.");
                    }
                    return response()->json(['message' => 'Data peminjaman tidak ditemukan atau sudah dikembalikan'], 404);
                }

                $tgl_kembali_input = Carbon::parse($requestData['tgl_kembali']);
                $tgl_kembali_pinjam = Carbon::parse($pinjam->tgl_kembali);
                $denda = $tgl_kembali_input->greaterThan($tgl_kembali_pinjam)
                    ? $tgl_kembali_pinjam->diffInDays($tgl_kembali_input) * 1000
                    : 0;

                $requestData['denda'] = $denda;
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images/buku', $imageName, 'public');

                $requestData['image'] = $imagePath; 
            }

            if ($action === 'store') {
                $modelInstance = new $baseModel();
                $modelInstance->fill($requestData);
                $modelInstance->save();

                if ($model === 'pinjam') {
                    $buku = Buku::find($requestData['id_buku']); 
                    $buku->update(['is_pinjam' => true]);
                }

                if ($model === 'kembali') {
                    $buku = Buku::find($requestData['id_buku']);
                    if ($buku) {
                        $buku->update(['is_pinjam' => false]);
                    }
                    $pinjam->update(['status' => 'dikembalikan']);
                }

                $statusCode = 201;
            } else {
                $modelInstance = $baseModel::find($id);
                if (!$modelInstance) {
                    return response()->json(['message' => 'Data not found'], 404);
                }
                $modelInstance->fill($requestData);
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

    public function destroy($model, $id)
    {
        $baseModel = $this->getModel($model);

        if (!class_exists($baseModel)) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $allowedFields = (new $baseModel)->getAllowedFields('delete');
        $data = $baseModel::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $filteredData = $data->only($allowedFields);
        if (empty($filteredData)) {
            return response()->json(['message' => 'Tidak ada field yang dapat dihapus'], 400);
        }

        try {
            $data->delete();
            return response()->json(['message' => 'Data berhasil dihapus'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error deleting data. Related data may exist.'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan server: ' . $e->getMessage()], 500);
        }
    }
}