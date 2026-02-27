<?php

use App\Http\Controllers\Api\ProductsController;

Route::get('/health', function() {
    return ['status' => 'ok'];
});

Route::get('/products', [ProductsController::class, 'index']);
Route::get('/products/{id}', [ProductsController::class, 'show']);
Route::patch('/products/{id}', [ProductsController::class, 'update']);
Route::delete('/products/{id}', [ProductsController::class, 'destroy']);
