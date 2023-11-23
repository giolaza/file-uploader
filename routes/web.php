<?php

use App\Http\Controllers\FileUploadController;
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
    return redirect('/file-upload');
});

Route::group([
    'prefix' => '/file-upload',
], function () {
    Route::get('/', [FileUploadController::class, 'index']);
    Route::post('/', [FileUploadController::class, 'uploadChunk']);
});
