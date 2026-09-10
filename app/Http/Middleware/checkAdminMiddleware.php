<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class checkAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth('api')->user();

        if (!$user || $user->role?->name !== $role) {
            abort(403, 'شما اجازه دسترسی به این قسمت را ندارید');
        }
//        if (!Auth::user() && Auth::user()->role->name !== $role) {
//            abort(Response::HTTP_FORBIDDEN);
//        }
        return $next($request);
    }
}
