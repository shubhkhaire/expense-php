<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Load API routes under the /api prefix and use the 'api' middleware group.
// This prevents CSRF checks for API-style endpoints and keeps a clear separation
// between web pages and programmatic API calls.
Route::prefix('api')->middleware('api')->group(function () {
    require __DIR__ . '/api.php';
});
