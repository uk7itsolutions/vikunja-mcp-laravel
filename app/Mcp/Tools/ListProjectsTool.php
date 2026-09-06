<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('List projects in Vikunja')]
class ListProjectsTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response
    {
        try {
            $projects = $this->client->get('projects');
            return Response::text(json_encode($projects));
        } catch (\Throwable $e) {
            return Response::text("ERROR: " . $e->getMessage());
        }
    }
}
