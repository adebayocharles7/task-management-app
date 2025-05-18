<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\AuthController;
use App\Http\Controllers\api\v1\TaskController;
use App\Http\Controllers\api\v1\UserController;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::prefix('v1')-> group(function () {
    
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']); 
     
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/tasks/{id}', [TaskController::class, 'updateStatus']);
        Route::post('/tasks/{taskId}/update-status', [TaskController::class, 'updateStatus']);
   

        Route::middleware('role:admin')->group(function () {
            Route::post('/admin/tasks/create', [TaskController::class, 'create']);

            Route::get('/admin/tasks', [TaskController::class, 'index']);

            Route::get('/users', [UserController::class, 'index']);


            

            Route::patch('/users/{id}/activate', [UserController::class, 'activateUser']);

            Route::patch('/users/{id}/deactivate', [UserController::class, 'deactivateUser']);

            //Route::
        });
    });
});

