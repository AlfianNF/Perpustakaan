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
 * description="Langkah-langkah untuk melakukan tes API
 * 1. Register 
 * 2. Login
 * 3. Setelah login,copy token yang di dapat lalu masukkan ke dalam bearer yang ada pada logo gembok
 * 4. setelahnya bisa melakukan tes API
 *
 * REKOMENDASI
 * 1. Create Category
 * 2. Create Buku
 * 3. Setelahnya bisa dilakukan testing fitur-fitur yang lain",
 * )
 * @OA\SecurityScheme(
 * type="http",
 * scheme="bearer",
 * securityScheme="bearerAuth"
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
     * tags={"API Documentation Dinamis"},
     * summary="Mendapatkan daftar data dari model dinamis",
     * description="Model yang tersedia adalah 
     * 1. Buku
     * 2. Category
     * 3. Pinjam
     * 4. Kembali
     * 5. User",
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
     
         if (is_string($query)) {
             return response()->json([
                 "message" => $query, 
             ], 400);
         }
     
         $results = $query->paginate(20);
     
         return response()->json([
             "message" => "Data Ditemukan",
             "data" => $results
         ], 200);
     }

     public function user(){
        return response()->json(auth()->user());
     }

    /**
     * @OA\Get(
     * path="/api/{model}/{id}/show",
     * operationId="getShow",
     * tags={"API Documentation Dinamis"},
     * summary="Mendapatkan detail data dari model dinamis",
     * description="Model yang tersedia adalah 
     * 1. Buku
     * 2. Category
     * 3. Pinjam
     * 4. Kembali
     * 5. User",
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
 * tags={"API Documentation Dinamis"},
 * summary="Membuat data baru pada model dinamis",
 * description="Model yang tersedia adalah 
 * 1. Buku
 * 2. Category
 * 3. Pinjam
 * 4. Kembali
 * 5. User",
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
 * oneOf={
 * @OA\Schema(
 * title="Buku",
 * type="object",
 * description="Skema untuk membuat buku baru.",
 * @OA\Property(property="title", type="string", example="Judul Buku"),
 * @OA\Property(property="isbn", type="string", example="B-001"),
 * @OA\Property(property="author", type="string", example="Nama Pengarang"),
 * @OA\Property(property="publish_date", type="string", format="date", example="2024-01-01"),
 * @OA\Property(property="category", type="integer", example=1),
 * @OA\Property(property="image", type="string", format="binary")
 * ),
 * @OA\Schema(
 * title="Category",
 * type="object",
 * description="Skema untuk membuat category baru.",
 * @OA\Property(property="name", type="string", example="Action"),
 * @OA\Property(property="description", type="string", example="Deskripsi kategori Action")
 * ),
 * @OA\Schema(
 * title="Pinjam",
 * type="object",
 * description="Skema untuk membuat peminjaman baru.",
 * @OA\Property(property="id_buku", type="integer", example=1),
 * @OA\Property(property="id_user", type="integer", example=5),
 * @OA\Property(property="tgl_pinjam", type="string", format="date", example="2024-03-13"),
 * @OA\Property(property="tgl_kembali", type="string", format="date", example="2024-03-20"),
 * @OA\Property(property="status", type="string", example="Dipinjam")
 * ),
 * @OA\Schema(
 * title="Kembali",
 * type="object",
 * description="Skema untuk mencatat pengembalian buku.",
 * @OA\Property(property="id_buku", type="integer", example=1),
 * @OA\Property(property="id_user", type="integer", example=5),
 * @OA\Property(property="id_pinjam", type="integer", example=10),
 * @OA\Property(property="tgl_kembali", type="string", format="date", example="2024-03-20")
 * ),
 * @OA\Schema(
 * title="User",
 * type="object",
 * description="Skema untuk membuat pengguna baru.",
 * @OA\Property(property="name", type="string", example="John Doe"),
 * @OA\Property(property="no_induk", type="string", example="123456"),
 * @OA\Property(property="email", type="string", example="johndoe@example.com"),
 * @OA\Property(property="password", type="string", example="password123"),
 * @OA\Property(property="is_admin", type="boolean", example=false),
 * @OA\Property(property="image", type="string", format="binary")
 * )
 * },
 * examples={
 * @OA\Examples(example="Buku", summary="Contoh request untuk Buku", value={"title": "Judul Buku", "isbn": "B-001", "author": "Nama Pengarang", "publish_date": "2024-01-01", "category": 1, "image": "string"}),
 * @OA\Examples(example="Category", summary="Contoh request untuk Category", value={"name": "Action", "description": "Deskripsi kategori action"}),
 * @OA\Examples(example="Pinjam", summary="Contoh request untuk Pinjam", value={"id_buku": 1, "id_user": 5, "tgl_pinjam": "2024-03-13", "tgl_kembali": "2024-03-20"}),
 * @OA\Examples(example="Kembali", summary="Contoh request untuk Kembali", value={"id_buku": 1, "id_user": 5, "id_pinjam": 10, "tgl_kembali": "2024-03-20"}),
 * @OA\Examples(example="User", summary="Contoh request untuk User", value={"name": "John Doe", "no_induk": "123456", "email": "johndoe@gmail.com", "password": "password123", "is_admin": false, "image": "string"})
 * }
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
 * tags={"API Documentation Dinamis"},
 * summary="Memperbarui data pada model dinamis",
 * description="Model yang tersedia adalah 
 * 1. Buku
 * 2. Category
 * 3. Pinjam
 * 4. Kembali
 * 5. User", * security={{"bearerAuth":{}}},
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
 * oneOf={
 * @OA\Schema(
 * title="Buku",
 * type="object",
 * description="Skema untuk memperbarui buku.",
 * @OA\Property(property="title", type="string", example="Judul Buku Baru"),
 * @OA\Property(property="isbn", type="string", example="B-002"),
 * @OA\Property(property="author", type="string", example="Nama Pengarang Baru"),
 * @OA\Property(property="publish_date", type="string", format="date", example="2024-02-01"),
 * @OA\Property(property="category", type="integer", example=2),
 * @OA\Property(property="image", type="string", format="binary")
 * ),
 * @OA\Schema(
 * title="Category",
 * type="object",
 * description="Skema untuk memperbarui kategori.",
 * @OA\Property(property="name", type="string", example="Adventure"),
 * @OA\Property(property="description", type="string", example="Deskripsi kategori Adventure")
 * ),
 * @OA\Schema(
 * title="Pinjam",
 * type="object",
 * description="Skema untuk memperbarui peminjaman.",
 * @OA\Property(property="id_buku", type="integer", example=2),
 * @OA\Property(property="id_user", type="integer", example=6),
 * @OA\Property(property="tgl_pinjam", type="string", format="date", example="2024-03-15"),
 * @OA\Property(property="tgl_kembali", type="string", format="date", example="2024-03-22"),
 * @OA\Property(property="status", type="string", example="Diperpanjang")
 * ),
 * @OA\Schema(
 * title="Kembali",
 * type="object",
 * description="Skema untuk memperbarui pengembalian buku.",
 * @OA\Property(property="id_buku", type="integer", example=2),
 * @OA\Property(property="id_user", type="integer", example=6),
 * @OA\Property(property="id_pinjam", type="integer", example=11),
 * @OA\Property(property="tgl_kembali", type="string", format="date", example="2024-03-22")
 * ),
 * @OA\Schema(
 * title="User",
 * type="object",
 * description="Skema untuk memperbarui pengguna.",
 * @OA\Property(property="name", type="string", example="Jane Doe"),
 * @OA\Property(property="no_induk", type="string", example="654321"),
 * @OA\Property(property="email", type="string", example="janedoe@example.com"),
 * @OA\Property(property="password", type="string", example="newpassword123"),
 * @OA\Property(property="is_admin", type="boolean", example=true),
 * @OA\Property(property="image", type="string", format="binary")
 * )
 * },
 * examples={
 * @OA\Examples(example="Buku", summary="Contoh request untuk Buku", value={"title": "Judul Buku Baru", "isbn": "B-002", "author": "Nama Pengarang Baru", "publish_date": "2024-02-01", "category": 2, "image": "string"}),
 * @OA\Examples(example="Category", summary="Contoh request untuk Category", value={"name": "Adventure", "description": "Deskripsi kategori Adventure"}),
 * @OA\Examples(example="Pinjam", summary="Contoh request untuk Pinjam", value={"id_buku": 2, "id_user": 6, "tgl_pinjam": "2024-03-15", "tgl_kembali": "2024-03-22", "status": "Diperpanjang"}),
 * @OA\Examples(example="Kembali", summary="Contoh request untuk Kembali", value={"id_buku": 2, "id_user": 6, "id_pinjam": 11, "tgl_kembali": "2024-03-22"}),
 * @OA\Examples(example="User", summary="Contoh request untuk User", value={"name": "Jane Doe", "no_induk": "654321", "email": "janedoe@example.com", "password": "newpassword123", "is_admin": true, "image": "string"})
 * }
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
     * tags={"API Documentation Dinamis"},
     * summary="Menghapus data dari model dinamis",
     * description="Model yang tersedia adalah 
     * 1. Buku
     * 2. Category
     * 3. Pinjam
     * 4. Kembali
     * 5. User",
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
     * tags={"API Documentation Denda Buku"},
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
