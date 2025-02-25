<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\BukuController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('{model}/list',[BaseController::class,'index']);
    Route::get('{model}/{id}/show',[BaseController::class,'show']);
    Route::get('{model}/is-list', [BaseController::class, 'isList']);

    Route::get('profil/pinjam',[BukuController::class,'pinjam']);
    Route::get('buku/recently-read',[BukuController::class,'recentlyRead']);
    
    //nanti pindahkan ke middleware is admin
    Route::post('{model}/create',[BaseController::class,'store']);
        Route::post('{model}/{id}/denda',[BaseController::class,'denda']);
        Route::put('{model}/{id}/update',[BaseController::class,'update']);
        Route::delete('{model}/{id}/delete',[BaseController::class,'destroy']);
    //admin
    Route::middleware('is_admin')->group(function(){
        
    });
});

//create user dan login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Route::get('/data',[BaseController::class, 'index2']);