<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OperationController;
use App\Models\Stone;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // Inventory
    Route::get('/items', [InventoryController::class, 'index']);
    Route::post('/items', [InventoryController::class, 'store']);
    Route::get('/items/{id}', [InventoryController::class, 'show']);
    Route::get('/items/barcode/{barcode}', [InventoryController::class, 'getByBarcode']);

    // Operations
    Route::post('/items/{item}/cut', [OperationController::class, 'cut']);
    Route::post('/items/{item}/reserve', [OperationController::class, 'reserve']);
    Route::post('/items/{item}/sell', [OperationController::class, 'sell']);

    // Stones (Catalog)
    Route::get('/stones', function () {
        return Stone::all();
    });
});
