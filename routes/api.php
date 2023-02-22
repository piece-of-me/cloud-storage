<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

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

Route::prefix('auth')->group(static function () {
    Route::post('/login', LoginController::class)->name('auth.login');
    Route::post('/reset', ResetPasswordController::class)->name('auth.login');
    Route::post('/register', RegisterController::class)->name('auth.register');
});

Route::middleware('auth:sanctum')->group(static function () {
    Route::post('auth/logout', LogoutController::class)->name('auth.logout');

    Route::prefix('files')->group(static function () {
        Route::get('/', [FileController::class, 'index'])->name('file.index');
        Route::post('/upload', [FileController::class, 'upload'])->name('file.upload');
        Route::post('/create', [FileController::class, 'create'])->name('file.create');
        Route::post('/{file}/rename', [FileController::class, 'rename'])->name('file.rename');
        Route::post('/{file}/delete', [FileController::class, 'delete'])->name('file.delete');
    });
});
