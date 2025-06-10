<?php

use App\Http\Controllers\ShortenerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [];
});

Route::get('/status', function () {
    return ['ok' => true];
});

Route::get('/{shortener:handle}', [ShortenerController::class, 'redirect'])->name('shortener');
