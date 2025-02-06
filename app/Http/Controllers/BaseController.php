<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Services\CoreService;

class BaseController extends Controller
{
    protected $coreService;

    public function __construct(CoreService $coreService){
        $this->coreService = $coreService;
    }
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

    public function index($model, Request $request)
    {
        $baseModel = $this->getModel($model);

        if (!class_exists($baseModel)) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $query = $baseModel::query();

        $allowedFilters = (new $baseModel)->getAllowedFilters();
        $filters = $request->only($allowedFilters);
        $query->isFilter($filters); 

        $relations = (new $baseModel)->getRelations();
        $query->with($relations);

        if ($search = $request->query('search')) {
            $query->search($search); 
        }

        $data = $query->paginate(10);

        return response()->json($data, 200);
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

    public function store($model, Request $request){
        $this->is_admin();

        $baseModel = $this->getModel($model);
        $baseController = $this->getController($model);

        if(!class_exists($baseModel)){
            return response()->json(['message' => 'Model not found'], 404);
        }
        if(!class_exists($baseController)){
            return response()->json(['message' => 'Controller not found'], 404);
        }
        
        $controller = app()->make($baseController);

        $response = app()->call([$controller, 'store'], ['request' => $request]);

        return response()->json($response,201);
    }

    public function update($model, Request $request,$id){
        $this->is_admin();

        $baseModel = $this->getModel($model);
        $baseController = $this->getController($model);

        if(!class_exists($baseModel)){
            return response()->json(['message' => 'Model not found'], 404);
        }

        if(!class_exists($baseController)){
            return response()->json(['message' => 'Controller not found'], 404);
        }

        $controller = app()->make($baseController);

        $response = app()->call([$controller, 'update'], ['request' => $request,'id' => $id]);

        return response()->json($response,200);
    }

    public function destroy($model,$id){
        $this->is_admin();

        $baseModel = $this->getModel($model);

        if(!class_exists($baseModel)){
            return response()->json(['message' => 'Model not found'], 404);
        }

        $data = $baseModel::find($id);
        $data->delete();
        return response()->json(['message' => 'Data berhasil di delete'], 200);
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

    public function index2()
    {
        $data = $this->coreService->index(); // Akses service
        return response()->json($data); // Lebih baik return response()->json()
    }
}
