<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeOptionController;
use App\Http\Controllers\ProductStoreController;
use App\Http\Controllers\ProductVariantController;

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

Route::apiResource('products', ProductController::class);
Route::apiResource('attributes', AttributeController::class);
Route::apiResource('attribute-options', AttributeOptionController::class);
Route::apiResource('product-variants', ProductVariantController::class);

Route::post('/csv-to-db-data-store', [ProductStoreController::class, 'csvToDbProductStore']);
