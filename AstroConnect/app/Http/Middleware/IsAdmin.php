<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict route access to users with admin role.
 */
class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('Admin middleware check', [
            'user_id' => $request->user()?->id,
            'email' => $request->user()?->email,
            'role' => $request->user()?->role,
            'session_id' => $request->session()->getId(),
            'path' => $request->path(),
        ]);

        if ($request->user()?->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
