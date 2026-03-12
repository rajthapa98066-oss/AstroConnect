<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAstrologer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $astrologer = $request->user()?->astrologer;

        if (! $astrologer || $astrologer->verification_status !== 'approved') {
            abort(403, 'Astrologer access is restricted to approved astrologers.');
        }

        return $next($request);
    }
}
