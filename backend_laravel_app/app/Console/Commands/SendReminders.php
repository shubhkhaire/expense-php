<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendReminders extends Command
{
    protected $signature = 'reminders:daily';
    protected $description = 'Send daily expense reminders and budget warnings';

    public function handle()
    {
        $users = DB::table('users')->select('id','email','name')->get();
        foreach ($users as $u) {
            $today = date('Y-m-d');
            $cntRes = DB::select('SELECT COUNT(*) as cnt, IFNULL(SUM(amount),0) as total FROM expenses WHERE user_id = ? AND date = ?', [$u->id, $today]);
            $cnt = $cntRes[0]->cnt ?? 0;
            $total = $cntRes[0]->total ?? 0;
            $text = "Hello {$u->name}, you have {$cnt} expenses today totaling {$total}";
            try {
                Mail::raw($text, function($msg) use ($u) {
                    $msg->from(env('EMAIL_USER'));
                    $msg->to($u->email);
                    $msg->subject('Daily expenses summary');
                });
                $this->info("Reminder sent to {$u->email}");
            } catch (\Exception $e) {
                $this->error('Mail error: ' . $e->getMessage());
            }
        }

        // Budget warnings
        $rows = DB::select('SELECT b.id, b.user_id, b.month, b.allocated_amount as amount, u.email, u.name FROM budgets b JOIN users u ON b.user_id = u.id');
        foreach ($rows as $b) {
            $start = $b->month . '-01';
            $end = $b->month . '-31';
            $spentRes = DB::select('SELECT IFNULL(SUM(amount),0) as spent FROM expenses WHERE user_id = ? AND date BETWEEN ? AND ?', [$b->user_id, $start, $end]);
            $spent = (float)($spentRes[0]->spent ?? 0);
            $pct = ($b->amount > 0) ? ($spent / $b->amount) * 100 : 0;
            if ($pct >= 90) {
                $text = "Hi {$b->name}, you have used {$spent} of {$b->amount} (".round($pct)."%) for {$b->month}";
                try {
                    Mail::raw($text, function($msg) use ($b) {
                        $msg->from(env('EMAIL_USER'));
                        $msg->to($b->email);
                        $msg->subject("Budget warning for {$b->month}");
                    });
                    $this->info("Budget warning sent to {$b->email}");
                } catch (\Exception $e) {
                    $this->error('Mail error: ' . $e->getMessage());
                }
            }
        }

        return 0;
    }
}
