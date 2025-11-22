<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;

class DashboardController extends Controller
{
    public function monthlyTotals(Request $request)
    {
        $user_id = $request->user->id ?? 1;

        $year = $request->query('year', date('Y'));
        $rows = Expense::selectRaw('MONTH(date) as month, IFNULL(SUM(amount),0) as total')
            ->where('user_id', $user_id)
            ->whereRaw('YEAR(date) = ?', [$year])
            ->groupByRaw('MONTH(date)')
            ->get();

        $totals = [];
        for ($i = 1; $i <= 12; $i++) {
            $found = $rows->firstWhere('month', $i);
            $totals[] = $found ? (float)$found->total : 0;
        }

        return response()->json(['data' => $totals]);
    }

    public function categorySpending(Request $request)
    {
        $user_id = $request->user->id ?? 1;

        $start = $request->query('startDate', '1970-01-01');
        $end = $request->query('endDate', '9999-12-31');

        $rows = Category::leftJoin('expenses as e', function($join) use ($user_id, $start, $end) {
            $join->on('categories.id', '=', 'e.category_id')
                 ->where('e.user_id', $user_id)
                 ->whereBetween('e.date', [$start, $end]);
        })
        ->where('categories.user_id', $user_id)
        ->selectRaw('categories.name as category, IFNULL(SUM(e.amount),0) as total')
        ->groupBy('categories.id')
        ->orderByRaw('total desc')
        ->get();

        return response()->json(['data' => $rows]);
    }

    public function chartData(Request $request)
    {
        $user_id = $request->user->id ?? 1;

        $year = $request->query('year', date('Y'));

        $monthlyRows = Expense::selectRaw('MONTH(date) as month, IFNULL(SUM(amount),0) as total')
            ->where('user_id', $user_id)
            ->whereRaw('YEAR(date) = ?', [$year])
            ->groupByRaw('MONTH(date)')
            ->get();

        $monthly = [];
        for ($i = 1; $i <= 12; $i++) {
            $found = $monthlyRows->firstWhere('month', $i);
            $monthly[] = $found ? (float)$found->total : 0;
        }

        $catRows = Category::leftJoin('expenses as e', function($join) use ($user_id, $year) {
            $join->on('categories.id', '=', 'e.category_id')
                 ->where('e.user_id', $user_id)
                 ->whereRaw('YEAR(e.date) = ?', [$year]);
        })
        ->where('categories.user_id', $user_id)
        ->selectRaw('categories.name as category, IFNULL(SUM(e.amount),0) as total')
        ->groupBy('categories.id')
        ->orderByRaw('total desc')
        ->get();

        $categories = $catRows->pluck('category')->all();
        $catTotals = $catRows->pluck('total')->map(function($v){return (float)$v;})->all();

        return response()->json(['data' => ['monthly' => $monthly, 'categories' => $categories, 'catTotals' => $catTotals]]);
    }
}
