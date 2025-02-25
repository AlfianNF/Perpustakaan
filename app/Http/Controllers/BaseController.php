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


    public function show($model, $id){
        $baseModel = $this->getModel($model);
        if(!class_exists($baseModel)){
            return response()->json(['message' => 'Model not found'], 404);
        }
        
        $relations = (new $baseModel)->getRelations();

        $data = $baseModel::with($relations)->find($id);

        if($data == null){
            return response()->json(['message' => 'Data not found'], 404);
        }
        return response()->json($data,200);
    }

    public function store($model, Request $request)
    {
        return app(CoreService::class)->handleRequest($model, $request, null, 'store');
    }

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

    public function destroy($model, $id)
    {
        $this->is_admin();
        $baseModel = $this->getModel($model);
    
        if (!class_exists($baseModel)) {
            return response()->json(['message' => 'Model not found'], 404);
        }
    
        $book = $baseModel::find($id);
    
        if (!$book) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
    
        try {
            DB::beginTransaction(); // Start a database transaction for safety
    
            // 1. Delete related pinjams records:
            $book->peminjaman()->delete(); // Use the relationship method
    
            // 2. Now delete the book:
            $book->delete();
    
            DB::commit(); // Commit the transaction
            return response()->json(['message' => 'Data berhasil dihapus'], 200);
    
        } catch (QueryException $e) {
            DB::rollBack(); 
            return response()->json(['message' => 'Error deleting data. Related loans may exist.'], 500); // More user-friendly message
        }
    }


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
