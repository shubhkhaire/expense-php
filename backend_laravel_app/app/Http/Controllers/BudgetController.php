<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Expense;

class BudgetController extends Controller
{
    public function store(Request $request)
    {
        // Authenticated user
        $user_id = $request->user->id ?? null;

        $month = $request->input('month');
        $category_id = $request->input('category_id');
            $allocated_amount = $request->input('allocated_amount') ?? 0;
            $month = $request->input('month') ?? (date('Y') . '-' . str_pad(date('n'), 2, '0', STR_PAD_LEFT));

        $b = Budget::create([
            'user_id' => $user_id,
            'month' => $month,
            'category_id' => $category_id,
            'allocated_amount' => $allocated_amount,
        ]);

        return response()->json(['message' => 'Budget created', 'id' => $b->id]);
    }

    public function update(Request $request, $id)
    {
        $user_id = $request->user->id ?? null;

            $allocated_amount = $request->input('allocated_amount') ?? 0;

        $b = Budget::where('id', $id)->where('user_id', $user_id)->first();
        if (!$b) return response()->json(['message' => 'Budget not found'], 404);
        $b->allocated_amount = $allocated_amount;
        $b->save();
            return response()->json(['message' => 'Budget updated (dev)']);
    }

    public function index(Request $request)
    {
        $user_id = $request->user->id ?? null;

        $rows = Budget::where('user_id', $user_id)->orderBy('month', 'desc')->get();
        $data = $rows->map(function($b) use ($user_id){
            $start = $b->month . '-01';
            $end = $b->month . '-31';
            $spent = Expense::where('user_id', $user_id)->whereBetween('date', [$start, $end])->sum('amount');
            return array_merge($b->toArray(), ['spent' => (float)$spent]);
        });

        return response()->json(['data' => $data]);
    }

    public function destroy(Request $request, $id)
    {
        $user_id = $request->user->id ?? null;

        $b = Budget::where('id', $id)->where('user_id', $user_id)->first();
        if (!$b) return response()->json(['message' => 'Budget not found'], 404);
        $b->delete();
            return response()->json(['message' => 'Budget deleted (dev)']);
    }
}
