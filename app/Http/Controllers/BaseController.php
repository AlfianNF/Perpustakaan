<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Services\CoreService;
use App\Http\Services\QueryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

/**
 * @OA\Info(
 * title="API Documentation",
 * version="1.0.0",
 * description="Dokumentasi API untuk aplikasi Anda",
 * )
 */
class BaseController extends Controller
{
    // protected $coreService;
    // protected $queryService;

    // public function __construct(CoreService $coreService,QueryService $queryService){
    //     $this->coreService = $coreService;
    //     $this->queryService = $queryService;
    // }
    public function is_admin(){
        $user = auth()->user();

        if (!$user || !$user->is_admin) { 
            abort(403, 'Anda bukan admin');        
        }    
    }
    public function getModel($model){
        $classModel = "\\App\\Models\\" . Str::ucfirst(Str::camel($model));
        return $classModel;
    }

    public function getController($model){
        $classController = "\\App\\Http\\Controllers\\" . Str::ucfirst(Str::camel($model)) . "Controller";    
        return $classController;
    }

    // public function index($model, Request $request)
    // {
    //     $baseModel = $this->getModel($model);

    //     if (!class_exists($baseModel)) {
    //         return response()->json(['message' => 'Model not found'], 404);
    //     }

    //     $query = $baseModel::query();

    //     $allowedFilters = (new $baseModel)->getAllowedFields('filter');
    //     $filters = $request->only($allowedFilters);
    //     $query->isFilter($filters); 

    //     $relations = (new $baseModel)->getRelations();
    //     $query->with($relations);

    //     if ($search = $request->query('search')) {
    //         $query->search($search); 
    //     }

    //     $data = $query->paginate(10);

    //     return response()->json($data, 200);
    // }

    // public function isList($model)
    // {
    //     $table = Str::snake(Str::plural($model)); 
    
    //     if (!Schema::hasTable($table)) {
    //         return response()->json(['message' => 'Table not found'], 404);
    //     }
    
    //     $columns = Schema::getColumnListing($table);
    
    //     return response()->json([
    //         'model' => $model,
    //         'table' => $table,
    //         'columns' => $columns
    //     ], 200);
    // }
    

    // public function index($model, Request $request)
    // {
    //     $baseModel = $this->getModel($model);
    //     $baseController = $this->getController($model);

    //     if (!class_exists($baseModel)) {
    //         return response()->json(['message' => 'Model not found'], 404);
    //     }

    //     $controller = app()->make($baseController);

    //     $response = app()->call([$controller, 'index'], ['request' => $request]);

    //     // Paginate hasil query
    //     $data = $response->paginate(21);

    //     return response()->json($data, 200);
    // }

    /**
     * @OA\Get(
     * path="/api/{model}/list",
     * operationId="getList",
     * tags={"Dynamic Routes"},
     * summary="Mendapatkan daftar data dari model dinamis",
     * description="Endpoint untuk mendapatkan daftar data dari model dinamis dengan paginasi.",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="model",
     * in="path",
     * required=true,
     * description="Nama model (misalnya: buku, user)",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Data ditemukan",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="Data Ditemukan"),
     * @OA\Property(property="data", type="object")
     * )
     * ),
     * @OA\Response(response=404, description="Data tidak ditemukan")
     * )
     */
     
    public function index($model, Request $request)
    {
        $query = app(QueryService::class)->getQuery($model, $request);
        $results = $query->paginate(21); 
        return response()->json([
            "message" =>"Data Ditemukan",
            "data" => $results
        ], 200); 

        // $query = $this->queryService->getQuery($model, $request);
        // return app(QueryService::class)->getQuery($model, $request);

        // return response()->json($query->paginate(21), 200);
    }


    /**
     * @OA\Get(
     * path="/api/{model}/{id}/show",
     * operationId="getShow",
     * tags={"Dynamic Routes"},
     * summary="Mendapatkan detail data dari model dinamis",
     * description="Endpoint untuk mendapatkan detail data berdasarkan ID dari model dinamis.",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="model",
     * in="path",
     * required=true,
     * description="Nama model (misalnya: buku, user)",
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID data",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(response=200, description="Data ditemukan"),
     * @OA\Response(response=404, description="Data tidak ditemukan")
     * )
     */
    public function show($model, $id){
        $baseModel = $this->getModel($model); 
        $data = app(QueryService::class)->getShow($baseModel, $id);

        if ($data === null) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        return response()->json($data, 200);
    }

    /**
     * @OA\Post(
     * path="/api/{model}/create",
     * operationId="createData",
     * tags={"Dynamic Routes"},
     * summary="Membuat data baru pada model dinamis",
     * description="Endpoint untuk membuat data baru pada model dinamis.",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="model",
     * in="path",
     * required=true,
     * description="Nama model (misalnya: buku, user)",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="field1", type="string", example="value1"),
     * @OA\Property(property="field2", type="integer", example=123)
     * )
     * ),
     * @OA\Response(response=201, description="Data berhasil dibuat"),
     * @OA\Response(response=400, description="Validasi gagal")
     * )
     */
    public function store($model, Request $request)
    {
        return app(CoreService::class)->handleRequest($model, $request, null, 'store');
    }

    /**
     * @OA\Put(
     * path="/api/{model}/{id}/update",
     * operationId="updateData",
     * tags={"Dynamic Routes"},
     * summary="Memperbarui data pada model dinamis",
     * description="Endpoint untuk memperbarui data pada model dinamis berdasarkan ID.",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="model",
     * in="path",
     * required=true,
     * description="Nama model (misalnya: buku, user)",
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID data",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="field1", type="string", example="updatedValue1"),
     * @OA\Property(property="field2", type="integer", example=456)
     * )
     * ),
     * @OA\Response(response=200, description="Data berhasil diperbarui"),
     * @OA\Response(response=400, description="Validasi gagal"),
     * @OA\Response(response=404, description="Data tidak ditemukan")
     * )
     */
    public function update($model, Request $request, $id)
    {
        return app(CoreService::class)->handleRequest($model, $request, $id, 'update');
    }
    

    // public function store($model, Request $request){
    //     $this->is_admin();

    //     $baseModel = $this->getModel($model);
    //     $baseController = $this->getController($model);

    //     if(!class_exists($baseModel)){
    //         return response()->json(['message' => 'Model not found'], 404);
    //     }
    //     if(!class_exists($baseController)){
    //         return response()->json(['message' => 'Controller not found'], 404);
    //     }
        
    //     $controller = app()->make($baseController);

    //     $response = app()->call([$controller, 'store'], ['request' => $request]);

    //     return response()->json($response,201);
    // }

    // public function update($model, Request $request,$id){
    //     $this->is_admin();

    //     $baseModel = $this->getModel($model);
    //     $baseController = $this->getController($model);

    //     if(!class_exists($baseModel)){
    //         return response()->json(['message' => 'Model not found'], 404);
    //     }

    //     if(!class_exists($baseController)){
    //         return response()->json(['message' => 'Controller not found'], 404);
    //     }

    //     $controller = app()->make($baseController);

    //     $response = app()->call([$controller, 'update'], ['request' => $request,'id' => $id]);

    //     return response()->json($response,200);
    // }

    // public function destroy($model, $id)
    // {
    //     $this->is_admin(); 
    //     $baseModel = $this->getModel($model);

    //     if (!class_exists($baseModel)) {
    //         return response()->json(['message' => 'Model not found'], 404);
    //     }

    //     $allowedFields = $baseModel::getAllowedFields('delete');

    //     $data = $baseModel::find($id);

    //     if (!$data) {
    //         return response()->json(['message' => 'Data tidak ditemukan'], 404);
    //     }

    //     $filteredData = $data->only($allowedFields);

    //     if (empty($filteredData)) {
    //         return response()->json(['message' => 'Tidak ada field yang dapat dihapus'], 400);
    //     }

    //     $data->delete();

    //     return response()->json(['message' => 'Data berhasil dihapus'], 200);
    // }

    /**
     * @OA\Delete(
     * path="/api/{model}/{id}/delete",
     * operationId="deleteData",
     * tags={"Dynamic Routes"},
     * summary="Menghapus data dari model dinamis",
     * description="Endpoint untuk menghapus data dari model dinamis berdasarkan ID.",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="model",
     * in="path",
     * required=true,
     * description="Nama model (misalnya: buku, user)",
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID data",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(response=200, description="Data berhasil dihapus."),
     * @OA\Response(response=404, description="Data tidak ditemukan."),
     * @OA\Response(response=500, description="Terjadi kesalahan server.")
     * )
     */
    public function destroy($model, $id)
    {
        $this->is_admin();
        return app(CoreService::class)->destroy($model, $id);
    }

    /**
     * @OA\Post(
     * path="/api/{model}/{id}/denda",
     * operationId="bayarDenda",
     * tags={"Dynamic Routes"},
     * summary="Membayar denda pengembalian",
     * description="Endpoint untuk membayar denda pengembalian buku.",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="model",
     * in="path",
     * required=true,
     * description="Nama model (harus 'kembali')",
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID pengembalian",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="jumlah_bayar", type="number", format="float", example=5000)
     * )
     * ),
     * @OA\Response(response=200, description="Pembayaran denda berhasil."),
     * @OA\Response(response=400, description="Validasi gagal."),
     * @OA\Response(response=404, description="Data tidak ditemukan."),
     * @OA\Response(response=500, description="Terjadi kesalahan server.")
     * )
     */
    public function denda($model,Request $request,$id){
        $this->is_admin();

        if($model != 'kembali'){
            return response()->json(['message' => 'Tidak terssedia untuk route '.$model],404);
        }

        $baseModel = $this->getModel($model);
        $baseController = $this->getController($model);

        $data=$baseModel::find($id);

        if($data->denda == 0){
            return response()->json(['message' => 'Tidak ditemukan adanya denda'],404);
        }

        $controller = app()->make($baseController);

        $response = app()->call([$controller, 'denda'], ['request' => $request,'id' => $id]);

        return response()->json($response,200);
    }

    // public function index2()
    // {
    //     $data = $this->coreService->index(); // Akses service
    //     return response()->json($data); // Lebih baik return response()->json()
    // }
}
