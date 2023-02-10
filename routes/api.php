<?php

use App\Http\Controllers\Auth\LoginController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/', static function() {
    dd('Hello from API');
});
Route::post('/login', [LoginController::class, ]);
Route::prefix('files')->group(static function() {
    Route::get('/', [FileController::class, 'index'])->name('file.index');
    Route::post('/', [FileController::class, 'upload'])->name('file.upload');
});

//Route::middleware('auth')->group(static function() {
//   Route::get('/', static function() {
//       dd('Hello from API');
//   });
//});