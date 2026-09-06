<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('List available labels in Vikunja')]
class ListLabelsTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Optional search term to filter labels'),
        ];
    }

    public function handle(Request $request): Response
    {
        try {
            $query = $request->get('query');
            $params = [];
            if ($query) {
                $params['s'] = $query;
            }

            $labels = $this->client->get('labels', $params);
            
            // Simplify response to save context window
            $simplified = [];
            if (is_array($labels)) {
                $simplified = array_map(function($l) {
                    return [
                        'id' => $l['id'] ?? null,
                        'title' => $l['title'] ?? null,
                        'hex_color' => $l['hex_color'] ?? null,
                    ];
                }, $labels);
            }

            return Response::text(json_encode($simplified));
        } catch (\Throwable $e) {
            return Response::text("ERROR: " . $e->getMessage());
        }
    }
}
