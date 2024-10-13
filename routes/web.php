<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NumberController;
use App\Http\Controllers\ReplyChatController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
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
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

Route::get('/number', [NumberController::class, 'index'])->name('number.index');
Route::post('/store/number', [NumberController::class, 'store'])->name('number.store');

Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
Route::post('/store/message', [MessageController::class, 'store'])->name('message.store');

Route::get('/reply-chat', [ReplyChatController::class, 'store'])->name('reply-chat.store');
Route::get('/reply-chat/{from}', [ReplyChatController::class, 'detail'])->name('reply-chat.detail');

Route::get('/report', [ReportController::class, 'index'])->name('report.index');

Route::get('/execute-curl', [NumberController::class, 'executeCurl'])->name('execute-curl');
Route::get('/report', [ReportController::class, 'index'])->name('report.index');
Route::get('/report/print', [ReportController::class, 'printReport'])->name('report.print');

Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
Route::post('/store/server', [SettingController::class, 'store'])->name('server.store');
Route::post('/server/toggle-server/{id}', [SettingController::class, 'toggleServer'])->name('setting.toggle-server');
Route::delete('/server/destroy/{id}', [SettingController::class, 'destroy'])->name('server.destroy');
