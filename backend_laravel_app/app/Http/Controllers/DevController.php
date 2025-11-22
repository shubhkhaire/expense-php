<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DevController extends Controller
{
    protected function allowed(Request $request)
    {
        if (env('APP_ENV') !== 'production') return true;
        $key = env('DEV_SEED_KEY');
        if (!$key) return false;
        $got = $request->header('x-dev-seed-key') ?? $request->query('key');
        return $got === $key;
    }

    public function seed(Request $request)
    {
        if (!$this->allowed($request)) return response()->json(['message' => 'Seeding not allowed'], 403);

        $force = $request->input('force');
        try {
            if ($force) {
                Artisan::call('db:seed', ['--class' => 'DemoDataSeeder']);
                return response()->json(['message' => 'Database force-seeded']);
            }
            Artisan::call('db:seed', ['--class' => 'DemoDataSeeder']);
            return response()->json(['message' => 'Database seeded (if empty)']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Seed failed', 'error' => $e->getMessage()], 500);
        }
    }
}
