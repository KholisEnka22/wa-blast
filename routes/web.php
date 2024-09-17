<?php

use App\Http\Controllers\InboxController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NumberController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/number',[NumberController::class, 'index'])->name('number.index');
Route::get('/inbox',[InboxController::class, 'index'])->name('inbox.index');
Route::post('/store/number',[NumberController::class, 'store'])->name('number.store');
Route::post('/store/message',[MessageController::class, 'store'])->name('message.store');

Route::get('/execute-curl',[NumberController::class, 'executeCurl'])->name('execute-curl');
