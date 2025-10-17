<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Check if user has the required role
        if ($request->user()->role !== $role) {
            return response()->json([
                'message' => 'Access denied. This action requires ' . $role . ' role.',
                'required_role' => $role,
                'user_role' => $request->user()->role
            ], 403);
        }

        return $next($request);
    }
}
