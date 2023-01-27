<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/ping', function (){
   return response()->json(['message' => 'pong']);
});

Route::controller(UserController::class)
    ->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::post('/logout', 'logout')->middleware(['auth:sanctum']);
        Route::get('/self-user', 'getUser')->middleware(['auth:sanctum']);

        Route::get('/users', 'list')->middleware(['auth:sanctum']);
        Route::post('/users', 'createOrUpdate')->middleware(['auth:sanctum']);
        Route::get('/users/{id}', 'show')->middleware(['auth:sanctum']);
        Route::delete('/users', 'delete')->middleware(['auth:sanctum']);
        Route::get('/users/{id}/payments', 'payments')->middleware(['auth:sanctum']);
    });
