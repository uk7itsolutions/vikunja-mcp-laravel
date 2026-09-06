<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('List all Kanban buckets for a project or specific view')]
class ListBucketsTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'project_id' => $schema->integer()->description('The ID of the project')->required(),
            'view_id' => $schema->integer()->description('Optional: The ID of a specific Kanban view'),
        ];
    }

    public function handle(Request $request): Response
    {
        try {
            $projectId = $request->get('project_id');
            $viewId = $request->get('view_id');

            // If view_id is not provided, find the first kanban view
            if (!$viewId) {
                $views = $this->client->get("projects/{$projectId}/views");
                foreach ($views as $view) {
                    if (isset($view['view_kind']) && $view['view_kind'] === 'kanban') {
                        $viewId = $view['id'];
                        break;
                    }
                }
                
                if (!$viewId) {
                    return Response::text(json_encode(['error' => 'No kanban view found for this project']));
                }
            }

            $buckets = $this->client->get("projects/{$projectId}/views/{$viewId}/buckets");
            
            // Simplify the response to return just id, title, and project_view_id to save context window
            $simplified = array_map(function($b) {
                return [
                    'id' => $b['id'] ?? null,
                    'title' => $b['title'] ?? null,
                    'project_view_id' => $b['project_view_id'] ?? null,
                ];
            }, $buckets);

            return Response::text(json_encode($simplified));
        } catch (\Throwable $e) {
            return Response::text("ERROR: " . $e->getMessage());
        }
    }
}
