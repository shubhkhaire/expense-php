<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Authentication removed for development. If these methods are called,
    // respond with a small JSON message indicating the endpoints are disabled.

    public function register(Request $request)
    {
        return response()->json(['message' => 'Registration is disabled in this development build.'], 410);
    }

    public function login(Request $request)
    {
        return response()->json(['message' => 'Login is disabled in this development build.'], 410);
    }
}
