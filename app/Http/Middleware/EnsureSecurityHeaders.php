<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = base64_encode(random_bytes(16));
        app()->instance('csp-nonce', $nonce);

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        $filesDomain = config('filesystems.disks.s3.endpoint', config('filesystems.disks.s3.url'));

        if (! $request->is('admin*') && ! $request->is('user*')) {
            if (app()->isLocal()) {
                $response->headers->set('Content-Security-Policy', implode('; ', [
                    "default-src 'self'",
                    "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5173",
                    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com http://localhost:5173",
                    "img-src 'self' data: blob: {$filesDomain}",
                    "font-src 'self' https://fonts.gstatic.com",
                    "connect-src 'self' ws://localhost:5173 http://localhost:5173",
                    "media-src 'self' {$filesDomain}",
                ]));
            } else {
                $response->headers->set('Content-Security-Policy', implode('; ', [
                    "default-src 'self'",
                    "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
                    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
                    "img-src 'self' data: blob: {$filesDomain} https://*.r2.cloudflarestorage.com",
                    "font-src 'self' https://fonts.gstatic.com",
                    "connect-src 'self'",
                    "media-src 'self' {$filesDomain} https://*.r2.cloudflarestorage.com",
                    "frame-ancestors 'self'",
                    "base-uri 'self'",
                    "form-action 'self'",
                ]));
            }
        }

        return $response;
    }
}
