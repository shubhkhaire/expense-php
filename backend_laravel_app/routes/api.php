<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevController;

// Authentication routes removed for development — app proceeds to dashboard without login/register

// Dev seed (unguarded depending on env)
Route::post('/dev/seed', [DevController::class, 'seed']);

// Authentication endpoints (public)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/me', [AuthController::class, 'me'])->middleware('jwt.auth');
// Protected API routes: require JWT auth so each user sees their own data
Route::middleware('jwt.auth')->group(function () {
    Route::apiResource('categories', CategoryController::class)->only([
        'index', 'store', 'show', 'update', 'destroy'
    ]);

    Route::get('/dashboard/monthly-totals', [DashboardController::class, 'monthlyTotals']);
    Route::get('/dashboard/category-spending', [DashboardController::class, 'categorySpending']);
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData']);

    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
    Route::get('/expenses', [ExpenseController::class, 'index']);

    Route::post('/budgets', [BudgetController::class, 'store']);
    Route::put('/budgets/{id}', [BudgetController::class, 'update']);
    Route::get('/budgets', [BudgetController::class, 'index']);
    Route::delete('/budgets/{id}', [BudgetController::class, 'destroy']);
});
