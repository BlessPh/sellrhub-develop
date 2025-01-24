<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (\Auth::user()->hasRole('super_admin')) {
            return $next($request);
        }

        return response([
            'message' => 'Only Super Admin is authorized to access this page'
        ], 401);
    }
}
