<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        $authHeader = $request->header('Authorization') ?? $request->header('authorization');
        if (!$authHeader) {
            return response()->json(['message' => 'Access denied. No token provided.'], 401);
        }

        $token = null;
        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
        } else {
            $token = $authHeader;
        }

        if (!$token) return response()->json(['message' => 'Token missing.'], 401);

        try {
            $secret = env('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            // Attach user info similar to Express: {id, email}
            $request->user = (object) ['id' => $decoded->id, 'email' => $decoded->email];
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token.'], 401);
        }

        return $next($request);
    }
}
