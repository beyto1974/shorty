<?php

use App\Http\Controllers\ShortenerController;
use App\Http\Controllers\StatsController;
use App\Http\Middleware\DBTransactionMiddleware;
use App\Http\Middleware\MasterTokenAuth;
use App\Http\Middleware\OwnerMiddleware;
use App\Mcp\Servers\ShortenerServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Mcp\Facades\Mcp;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // More restrictive for search (might be expensive)
    Route::post('/shortener/search', [ShortenerController::class, 'search'])
        ->middleware('throttle:30,1');

    // Regular limits for other endpoints
    Route::put('/shortener', [ShortenerController::class, 'put'])
        ->middleware([DBTransactionMiddleware::class, 'throttle:60,1']);

    Route::get('/shortener/{shortener}', [ShortenerController::class, 'get'])
        ->middleware([OwnerMiddleware::class, 'throttle:60,1']);

    Route::delete('/shortener/{shortener}', [ShortenerController::class, 'delete'])
        ->middleware([OwnerMiddleware::class, DBTransactionMiddleware::class, 'throttle:60,1']);

    // Stats endpoint - moderate limit
    Route::get('/user/stats', [StatsController::class, 'getUserStats'])
        ->middleware('throttle:120,1');

    Mcp::web('/mcp/shortener', ShortenerServer::class)
        ->middleware(['throttle:60,1']);
});

Route::middleware(MasterTokenAuth::class)->group(function () {
    Route::get('/stats', [StatsController::class, 'getGlobalStats']);
});
