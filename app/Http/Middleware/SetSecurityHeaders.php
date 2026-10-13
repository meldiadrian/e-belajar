<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetSecurityHeaders
{
    /**
     * Handle an incoming request and apply comprehensive security headers to the response.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Remove PHP version header to prevent technology fingerprinting
        if (function_exists('header_remove')) {
            @header_remove('X-Powered-By');
        }

        $response = $next($request);

        // Remove from Symfony headers bag if injected
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // 1. Anti-Clickjacking: X-Frame-Options
        if (!$response->headers->has('X-Frame-Options')) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }

        // 2. Anti-MIME-sniffing: X-Content-Type-Options
        if (!$response->headers->has('X-Content-Type-Options')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }

        // 3. Legacy XSS filter protection
        if (!$response->headers->has('X-XSS-Protection')) {
            $response->headers->set('X-XSS-Protection', '1; mode=block');
        }

        // 4. Referrer privacy & security
        if (!$response->headers->has('Referrer-Policy')) {
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        // 5. Restrict sensitive hardware features
        if (!$response->headers->has('Permissions-Policy')) {
            $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        }

        // 6. Restrict cross-domain Adobe Flash and PDF policy
        if (!$response->headers->has('X-Permitted-Cross-Domain-Policies')) {
            $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        }

        // 7. Content-Security-Policy: ensure frame-ancestors 'self'
        $csp = $response->headers->get('Content-Security-Policy');
        if ($csp) {
            if (!str_contains($csp, 'frame-ancestors')) {
                $response->headers->set('Content-Security-Policy', rtrim($csp, '; ') . "; frame-ancestors 'self'");
            }
        } else {
            $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");
        }

        // 8. Strict-Transport-Security (HSTS) when on HTTPS
        if ($request->isSecure() || $request->header('X-Forwarded-Proto') === 'https') {
            if (!$response->headers->has('Strict-Transport-Security')) {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            }
        }

        // 9. Cross-Origin-Opener-Policy
        if (!$response->headers->has('Cross-Origin-Opener-Policy')) {
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        }

        return $response;
    }
}
