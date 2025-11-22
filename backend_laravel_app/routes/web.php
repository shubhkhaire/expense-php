<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Also load API routes directly so the frontend can call endpoints at root (e.g. /auth/login)
// This keeps the original Express-style routes intact for development.
require __DIR__ . '/api.php';
