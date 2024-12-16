<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

Route::middleware('auth:api')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);  // Mendapatkan daftar tugas
    Route::put('/tasks/{id}', [TaskController::class, 'update']);  // Memperbarui status tugas
    Route::post('/tasks', [TaskController::class, 'store']); //menambahkan tugas
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
});
// Route::middleware('auth:api')->get('/tasks', [TaskController::class, 'index']);
// Route::middleware('auth:api')->post('/tasks', [TaskController::class, 'store']);
// Route::middleware('auth:api')->put('/tasks/{task}', [TaskController::class, 'update']);
// Route::middleware('auth:api')->patch('/tasks/{task}', [TaskController::class, 'update']);
// Route::middleware('auth:api')->delete('/tasks/{task}', [TaskController::class, 'destroy']);
// Route::middleware('auth:api')->get('/tasks/{task}', [TaskController::class, 'show']);