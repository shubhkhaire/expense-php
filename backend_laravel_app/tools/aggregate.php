<?php

// Bootstrap Laravel and output JSON aggregates for charts
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Current month (Y-m)
$month = Carbon::now()->format('Y-m');

// Per-category spent vs budget for current month
$perCategory = DB::table('categories')
    ->leftJoin('budgets', function($j) use ($month) {
        $j->on('categories.id', 'budgets.category_id')
          ->where('budgets.month', $month);
    })
    ->leftJoin('expenses', function($j) {
        $j->on('categories.id', 'expenses.category_id')
        ->whereRaw('MONTH(expenses.date)=?', [date('m')])
        ->whereRaw('YEAR(expenses.date)=?', [date('Y')]);
    })
    ->select('categories.id', 'categories.name', DB::raw('COALESCE(SUM(expenses.amount),0) as spent'), DB::raw('COALESCE(MAX(budgets.allocated_amount),0) as budget'))
    ->groupBy('categories.id', 'categories.name')
    ->get();

// Daily totals for the last 7 days
    $daily = DB::select("SELECT DATE(`date`) as day, SUM(amount) as total FROM expenses WHERE `date` >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(`date`) ORDER BY DATE(`date`)");

// Output JSON
echo json_encode([
    'month' => $month,
    'per_category' => $perCategory,
    'daily_last_7' => $daily,
], JSON_PRETTY_PRINT);
