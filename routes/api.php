<?php

use App\Http\Controllers\ShortenerController;
use App\Http\Controllers\StatsController;
use App\Http\Middleware\DBTransactionMiddleware;
use App\Http\Middleware\MasterTokenAuth;
use App\Http\Middleware\OwnerMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/shortener', [ShortenerController::class, 'put'])->middleware(DBTransactionMiddleware::class);
    Route::get('/shortener/{shortener}', [ShortenerController::class, 'get'])->middleware(OwnerMiddleware::class);
    Route::delete('/shortener/{shortener}', [ShortenerController::class, 'delete'])->middleware([OwnerMiddleware::class, DBTransactionMiddleware::class]);
    Route::post('/shortener/search', [ShortenerController::class, 'search'])->middleware([DBTransactionMiddleware::class]);

    Route::get('/user/stats', [StatsController::class, 'getUserStats']);
});

Route::middleware(MasterTokenAuth::class)->group(function () {
    Route::get('/stats', [StatsController::class, 'getGlobalStats'])->middleware(DBTransactionMiddleware::class);
});
