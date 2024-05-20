<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GoogleController;


Route::get('/', function () {
    return response()->json([
        "Message" => "Uts Pemrograman Berbasis Framework",
        "Nim" => "2112012009",
        "Kelas" => "IF22E",
    ]);
});


Route::get("/userlist", function () {
    $user = User::all();
    return response()->json([
        "status" => true,
        "data" => $user
    ], 200);
})->middleware('auth:api', 'admin');



Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:api');


// register with google 
Route::get("oauth/register", [AuthController::class, 'registerWithGoogle']);

//Routes protected by sanctum auth middleware
Route::middleware('auth:api')->group(function () {

    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);

    //Admin routes
    Route::middleware('admin')->group(function () {
        Route::get('categories', [CategoryController::class, 'index']);
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{id}', [CategoryController::class, 'update']);
        Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    });
});
