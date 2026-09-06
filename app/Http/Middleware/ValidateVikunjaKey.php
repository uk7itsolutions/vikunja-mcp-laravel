<?php

namespace App\Http\Middleware;

use App\Services\VikunjaClient;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ValidateVikunjaKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $baseUrl = config('vikunja.base_url');

        if (empty($baseUrl)) {
            Log::error('VIKUNJA_BASE_URL is not set. Configure it in .env');

            return response()->json([
                'error' => 'Server misconfigured: VIKUNJA_BASE_URL is not set.',
            ], 500);
        }

        $header = $request->header('Authorization', '');

        if (! str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'error' => 'Missing or malformed Authorization header. Expected: Authorization: Bearer <token>',
            ], 401);
        }

        // Bind the client. We do not ping the Vikunja API here because API tokens
        // may be scoped and restricted from hitting endpoints like /api/v1/user.
        // If the token is invalid, the individual tools will catch the error and
        // report it gracefully back to Claude.
        app()->instance(VikunjaClient::class, new VikunjaClient($header));

        return $next($request);
    }
}
