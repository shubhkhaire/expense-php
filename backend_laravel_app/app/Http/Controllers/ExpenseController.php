<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function store(Request $request)
    {
        // Authentication removed for local development — default to demo user id 1 when absent
        $user_id = $request->user->id ?? 1;

        // Accept any incoming fields without strict validation in dev
        $amount = $request->input('amount');

        $receipt_path = null;
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $name = time() . '-' . bin2hex(random_bytes(6)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $name);
            $receipt_path = '/uploads/' . $name;
        }

        $expense = Expense::create([
            'user_id' => $user_id,
            'category_id' => $request->input('category_id'),
            'amount' => $amount,
            'note' => $request->input('note'),
            'date' => $request->input('date'),
            'receipt_path' => $receipt_path,
        ]);

        return response()->json(['message' => 'Expense added', 'id' => $expense->id]);
    }

    public function update(Request $request, $id)
    {
        $user_id = $request->user->id ?? 1;

        $expense = Expense::where('id', $id)->where('user_id', $user_id)->first();
        if (!$expense) return response()->json(['message' => 'Expense not found'], 404);

        $receipt_path = null;
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $name = time() . '-' . bin2hex(random_bytes(6)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $name);
            $receipt_path = '/uploads/' . $name;
        }

        $fields = [];
        if ($request->has('amount')) $expense->amount = $request->input('amount');
        if ($request->has('category_id')) $expense->category_id = $request->input('category_id');
        if ($request->has('note')) $expense->note = $request->input('note');
        if ($request->has('date')) $expense->date = $request->input('date');
        if ($receipt_path) $expense->receipt_path = $receipt_path;

        // allow empty updates in dev

        $expense->save();
        return response()->json(['message' => 'Expense updated']);
    }

    public function destroy(Request $request, $id)
    {
        $user_id = $request->user->id ?? 1;

        $expense = Expense::where('id', $id)->where('user_id', $user_id)->first();
        if (!$expense) return response()->json(['message' => 'Expense not found'], 404);
        $expense->delete();
        return response()->json(['message' => 'Expense deleted']);
    }

    public function index(Request $request)
    {
        $user_id = $request->user->id ?? 1;

        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');
        $search = $request->query('search');
        $category = $request->query('category');
        $page = max(1, (int)$request->query('page', 1));
        $limit = (int)$request->query('limit', 20);
        $offset = ($page - 1) * $limit;

        $query = Expense::where('user_id', $user_id);
        if ($startDate) $query->where('date', '>=', $startDate);
        if ($endDate) $query->where('date', '<=', $endDate);
        if ($category) $query->where('category_id', $category);
        if ($search) $query->where(function($q) use ($search) {
            $q->where('note', 'like', "%$search%")
              ->orWhere('amount', 'like', "%$search%");
        });

        $total = $query->count();
        $rows = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->skip($offset)->take($limit)->get();

        return response()->json(['data' => $rows, 'total' => $total, 'page' => $page, 'limit' => $limit]);
    }
}
