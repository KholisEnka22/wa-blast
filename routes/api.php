<?php

use App\Http\Controllers\Api\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route untuk mengambil data yang belum terkirim
Route::get('/message/pending', [MessageController::class, 'getPendingNumber']);

// Route untuk mengubah status menjadi terkirim berdasarkan id
Route::get('/message/update-status/{id}', [MessageController::class, 'updateNumberStatus']);
