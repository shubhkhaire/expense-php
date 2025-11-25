<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URIs that should be excluded from CSRF verification.
     * We exclude the frontend API-style routes that the SPA calls directly.
     *
     * @var array
     */
    protected $except = [
        'auth/*',
        'categories',
        'categories/*',
        'expenses',
        'expenses/*',
        'budgets',
        'budgets/*',
        'dashboard/*',
        'dev/*',
    ];
}
