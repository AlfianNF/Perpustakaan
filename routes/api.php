<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaseController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('{model}/list',[BaseController::class,'index']);
    Route::get('{model}/{id}/show',[BaseController::class,'show']);
    
    //admin
    Route::middleware('is_admin')->group(function(){
        Route::post('{model}/create',[BaseController::class,'store']);
        Route::post('{model}/{id}/denda',[BaseController::class,'denda']);
        Route::put('{model}/{id}/update',[BaseController::class,'update']);
        Route::delete('{model}/{id}/delete',[BaseController::class,'destroy']);
    });
});

//create user dan login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Route::get('/data',[BaseController::class, 'index2']);