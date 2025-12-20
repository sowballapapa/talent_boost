<?php

use App\Http\Controllers\auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wallet/balance', [App\Http\Controllers\WalletController::class, 'balance']);
    Route::post('/transactions/transfer', [App\Http\Controllers\TransactionController::class, 'transfer']);
    Route::get('/transactions/history', [App\Http\Controllers\TransactionController::class, 'index']);
    Route::get('/transactions/{transaction}', [App\Http\Controllers\TransactionController::class, 'show']);
    Route::get('/transaction-types', [App\Http\Controllers\TransactionTypeController::class, 'index']);
    Route::get('/transaction-statuses', [App\Http\Controllers\TransactionStatusController::class, 'index']);

    // User Profile Routes
    Route::get('/user/profile', [App\Http\Controllers\UserController::class, 'profile']);
    Route::post('/user/update', [App\Http\Controllers\UserController::class, 'update']);
    Route::get('/user/search', [App\Http\Controllers\UserController::class, 'search']);
});

// Documentation technique (public)
Route::get('/doc-tech', function () {
    return response()->file(public_path('doc-tech/index.html'));
});

// Documentation technique (public)
Route::get('/docs', function () {
    return response()->file(public_path('docs/index.html'));
});