<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix("admin")->group(function () {
    //Route::get("/", [AdminController::class, "storeList"]);
    Route::get("/manage/store", [AdminController::class, "storeList"])->name("storeList");
    Route::get("/manage/store/{id}", [AdminController::class, "index"]);
    Route::post('/manage/store/updateStore', [AdminController::class, "updateStore"]);
});