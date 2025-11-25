<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Notification;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 1; // assumes Test User exists with id 1

        // Create categories
        $categories = [
            ['name' => 'Food', 'icon' => 'utensils', 'color' => '#f97316'],
            ['name' => 'Transport', 'icon' => 'car', 'color' => '#06b6d4'],
            ['name' => 'Bills', 'icon' => 'file-invoice', 'color' => '#ef4444'],
            ['name' => 'Entertainment', 'icon' => 'gamepad', 'color' => '#8b5cf6'],
            ['name' => 'Groceries', 'icon' => 'shopping-cart', 'color' => '#10b981'],
        ];

        $categoryIds = [];
        foreach ($categories as $c) {
            $cat = Category::create(array_merge($c, ['user_id' => $userId]));
            $categoryIds[$cat->name] = $cat->id;
        }

        // Create budgets for current month for each category
        $month = Carbon::now()->format('Y-m');
        foreach ($categoryIds as $name => $id) {
            Budget::create([
                'user_id' => $userId,
                'month' => $month,
                'category_id' => $id,
                'allocated_amount' => rand(100, 1000),
            ]);
        }

        // Create sample expenses across categories
        $dates = [Carbon::now()->subDays(1), Carbon::now()->subDays(3), Carbon::now()->subDays(7), Carbon::now()->subDays(10), Carbon::now()->subDays(15)];
        $notes = ['Lunch at cafe','Uber to office','Electricity bill','Movie night','Weekly groceries'];

        for ($i = 0; $i < 10; $i++) {
            $catNames = array_keys($categoryIds);
            $pick = $catNames[array_rand($catNames)];
            Expense::create([
                'user_id' => $userId,
                'category_id' => $categoryIds[$pick],
                'amount' => rand(50, 1500) / 10 * 10,
                'note' => $notes[array_rand($notes)],
                'date' => $dates[array_rand($dates)]->format('Y-m-d'),
                'receipt_path' => null,
            ]);
        }

        // Notifications
        Notification::create(['user_id' => $userId, 'title' => 'Budget Updated', 'body' => 'Your budget for Food was updated.', 'read_flag' => 0]);
        Notification::create(['user_id' => $userId, 'title' => 'Bill Due', 'body' => 'Electricity bill due in 3 days.', 'read_flag' => 0]);
    }
}
