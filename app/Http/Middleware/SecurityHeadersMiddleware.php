<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and enforce enterprise security headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Enforce anti-framing and clickjacking protection
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent legacy XSS in older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Restrict referrer information to origin on cross-origin requests
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict sensitive browser APIs
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Enforce HSTS when running on HTTPS or in production
        if ($request->isSecure() || app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
