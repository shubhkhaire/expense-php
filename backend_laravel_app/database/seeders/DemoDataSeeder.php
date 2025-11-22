<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        $user = User::first() ?: User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
        ]);

        if (!Category::where('user_id', $user->id)->count()) {
            $cats = [
                ['user_id' => $user->id, 'name' => 'Groceries', 'icon' => 'shopping-cart', 'color' => '#34d399'],
                ['user_id' => $user->id, 'name' => 'Transport', 'icon' => 'car', 'color' => '#60a5fa'],
                ['user_id' => $user->id, 'name' => 'Bills', 'icon' => 'bill', 'color' => '#f97316'],
                ['user_id' => $user->id, 'name' => 'Eating Out', 'icon' => 'utensils', 'color' => '#a78bfa'],
            ];
            foreach ($cats as $c) Category::create($c);
        }

        if (!Expense::where('user_id', $user->id)->count()) {
            $today = date('Y-m-d');
            Expense::create(['user_id' => $user->id, 'category_id' => null, 'amount' => 12.5, 'note' => 'Coffee and snack', 'date' => $today, 'receipt_path' => '/uploads/expense-sample-1.svg']);
            Expense::create(['user_id' => $user->id, 'category_id' => null, 'amount' => 45.0, 'note' => 'Grocery run', 'date' => $today, 'receipt_path' => null]);
            Expense::create(['user_id' => $user->id, 'category_id' => null, 'amount' => 120.0, 'note' => 'Electricity bill', 'date' => $today, 'receipt_path' => '/uploads/expense-sample-2.svg']);
        }

        if (!Budget::where('user_id', $user->id)->count()) {
            $ym = date('Y') . '-' . str_pad(date('n'), 2, '0', STR_PAD_LEFT);
            Budget::create(['user_id' => $user->id, 'month' => $ym, 'category_id' => null, 'allocated_amount' => 500.0]);
        }

        if (!Notification::where('user_id', $user->id)->count()) {
            Notification::create(['user_id' => $user->id, 'title' => 'Welcome', 'body' => 'This is sample data to help you get started.', 'read_flag' => 0]);
        }
    }
}
