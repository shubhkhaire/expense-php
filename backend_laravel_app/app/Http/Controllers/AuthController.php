<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->only(['name','email','password']);
        if (!isset($data['email']) || !isset($data['password'])) {
            return response()->json(['message' => 'Email and password required'], 400);
        }

        if (User::where('email', $data['email'])->exists()) {
            return response()->json(['message' => 'Email already registered'], 400);
        }

        $user = User::create([
            'name' => $data['name'] ?? 'User',
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        return response()->json(['message' => 'Registered', 'id' => $user->id], 201);
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        if (!$email || !$password) return response()->json(['message' => 'Email and password required'], 400);

        $user = User::where('email', $email)->first();
        if (!$user) return response()->json(['message' => 'Invalid credentials'], 401);

        if (!\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $secret = env('JWT_SECRET');
        if (!$secret) {
            // create a dev secret and persist to .env
            $secret = bin2hex(random_bytes(16));
            file_put_contents(base_path('.env'), PHP_EOL . 'JWT_SECRET=' . $secret . PHP_EOL, FILE_APPEND | LOCK_EX);
        }

        $payload = [
            'id' => $user->id,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24 * 30), // 30 days
        ];

        $token = JWT::encode($payload, $secret, 'HS256');
        return response()->json(['token' => $token, 'user' => ['id' => $user->id, 'email' => $user->email, 'name' => $user->name]]);
    }

    public function me(Request $request)
    {
        // jwt middleware attaches user object
        if (!isset($request->user)) return response()->json(['message' => 'Not authenticated'], 401);
        return response()->json(['user' => $request->user]);
    }
}
