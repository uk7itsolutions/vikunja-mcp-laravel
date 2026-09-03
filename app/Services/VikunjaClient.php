<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VikunjaClient
{
    private readonly string $baseUrl;
    private readonly bool $debug;

    public function __construct(private readonly string $authHeader)
    {
        $this->baseUrl = config('vikunja.base_url');
        $this->debug = config('vikunja.debug');
    }

    /**
     * @throws RequestException
     */
    public function get(string $endpoint, array $query = []): mixed
    {
        return $this->request('get', $endpoint, $query);
    }

    /**
     * @throws RequestException
     */
    public function post(string $endpoint, array $data = []): mixed
    {
        return $this->request('post', $endpoint, $data);
    }

    /**
     * @throws RequestException
     */
    public function put(string $endpoint, array $data = []): mixed
    {
        return $this->request('put', $endpoint, $data);
    }

    /**
     * @throws RequestException
     */
    public function delete(string $endpoint): mixed
    {
        return $this->request('delete', $endpoint);
    }

    /**
     * @throws RequestException
     */
    private function request(string $method, string $endpoint, array $data = []): mixed
    {
        $url = $this->baseUrl . '/api/v1/' . ltrim($endpoint, '/');

        if ($this->debug) {
            Log::debug("Vikunja API Request: {$method} {$url}", ['payload' => $data]);
        }

        $response = $this->http()->$method($url, $data);

        if ($this->debug) {
            Log::debug("Vikunja API Response: {$response->status()}", ['body' => $response->body()]);
        }

        if ($response->failed()) {
            Log::error("Vikunja API Request Failed: {$method} {$url} returned {$response->status()}", [
                'payload' => $data,
                'body' => $response->body(),
            ]);
            $response->throw();
        }

        return $response->json();
    }

    private function http(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => $this->authHeader,
        ])->acceptJson();
    }
}
