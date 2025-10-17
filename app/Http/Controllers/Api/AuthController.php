<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-16

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use InvalidArgumentException;

/**
 * Class AuthController
 *
 * Handles API authentication using Laravel Sanctum.
 * Provides endpoints for user registration, login, and logout.
 *
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    protected AuthService $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Register a new user and return an API token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:buyer,seller,admin',
        ]);

        $result = $this->auth->register($validated);

        return response()->json([
            'user' => $result['user'],
            'token' => $result['token'],
        ], 201);
    }

    /**
     * Authenticate a user and issue a new API token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        try {
            $result = $this->auth->login($credentials);
            return response()->json([
                'user' => $result['user'],
                'token' => $result['token'],
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    /**
     * Log out the authenticated user by revoking the current access token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->auth->logout($request->user());

        return response()->json(['message' => 'Logged out successfully']);
    }
}
