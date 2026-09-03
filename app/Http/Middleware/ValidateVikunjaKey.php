<?php

namespace App\Http\Middleware;

use App\Services\VikunjaClient;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

        try {
            $check = Http::withHeaders(['Authorization' => $header])
                ->acceptJson()
                ->timeout(5)
                ->get(rtrim($baseUrl, '/').'/api/v1/user');
        } catch (ConnectionException $e) {
            Log::error('Could not reach Vikunja API', [
                'base_url' => $baseUrl,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => "Could not reach Vikunja at {$baseUrl}.",
            ], 502);
        }

        if ($check->status() === 401) {
            return response()->json([
                'error' => 'Vikunja rejected the credentials.',
            ], 401);
        }

        if ($check->failed()) {
            return response()->json([
                'error' => "Vikunja API returned HTTP {$check->status()}",
            ], 502);
        }

        app()->instance(VikunjaClient::class, new VikunjaClient($header));

        return $next($request);
    }
}
