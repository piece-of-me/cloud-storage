<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
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
    Route::post('/logout', LogoutController::class)->name('auth.logout');
});

Route::middleware('auth:sanctum')->group(static function () {
    Route::prefix('files')->group(static function () {
        Route::get('/', [FileController::class, 'index'])->name('file.index');
        Route::post('/upload', [FileController::class, 'upload'])->name('file.upload');
        Route::post('/create', [FileController::class, 'create'])->name('file.create');
        Route::post('/{file}/download', [FileController::class, 'download'])->name('file.download');
        Route::patch('/{file}/rename', [FileController::class, 'rename'])->name('file.rename');
        Route::patch('/{file}/move/{newParent?}', [FileController::class, 'move'])->name('file.move');
        Route::patch('/{file}/copy/{newParent?}', [FileController::class, 'copy'])->name('file.copy');
        Route::post('/{file}/share', [FileController::class, 'share'])->name('file.share');
        Route::delete('/{file}/delete', [FileController::class, 'delete'])->name('file.delete');
    });
});

Route::prefix('/public/files/')->group(static function () {
    Route::post('/{hash}', [FileController::class, 'publicIndex'])->name('public.file.index');
    Route::post('/{file}/download', [FileController::class, 'publicDownload'])->name('public.file.download');
    Route::middleware('auth:sanctum')->group(static function () {
        Route::post('/{file}/save', [FileController::class, 'save'])->name('public.file.save');
    });
});
