<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        header("X-Frame-Options: DENY");
        header("Content-Language: de-DE");
        header("X-Content-Type-Options: nosniff");
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
        return $next($request);
    }
}
