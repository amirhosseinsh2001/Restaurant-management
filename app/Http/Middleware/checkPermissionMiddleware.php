<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => 'failure',
                'message' => 'احراز هویت نشده'
            ], 401);
        }

        if (!$user->hasPermission($permission)) {
            return response()->json([
                'status' => 'failure',
                'message' => 'دسترسی ندارید'
            ], 403);
        }
        return $next($request);
    }
}
