<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Move a task to a specific Kanban bucket')]
class MoveTaskToBucketTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'task_id' => $schema->integer()->description('The ID of the task to move')->required(),
            'bucket_id' => $schema->integer()->description('The ID of the destination bucket')->required(),
            'project_id' => $schema->integer()->description('Optional: The ID of the project. Auto-fetched if omitted.'),
            'view_id' => $schema->integer()->description('Optional: The ID of the Kanban view. Auto-fetched if omitted.'),
        ];
    }

    public function handle(Request $request): Response
    {
        try {
            $taskId = $request->get('task_id');
            $bucketId = $request->get('bucket_id');
            $projectId = $request->get('project_id');
            $viewId = $request->get('view_id');

            // If project_id is missing, fetch the task to find it
            if (!$projectId) {
                $task = $this->client->get("tasks/{$taskId}");
                $projectId = $task['project_id'] ?? null;
                
                if (!$projectId) {
                    return Response::text("ERROR: Could not resolve project_id for task {$taskId}");
                }
            }

            // If view_id is missing, find the kanban view for this project
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

            $payload = [
                'task_id' => $taskId
            ];

            // POST /projects/{project}/views/{view}/buckets/{bucket}/tasks
            $result = $this->client->post("projects/{$projectId}/views/{$viewId}/buckets/{$bucketId}/tasks", $payload);
            
            return Response::text(json_encode(['success' => true, 'message' => 'Task moved to bucket successfully']));
        } catch (\Throwable $e) {
            return Response::text("ERROR: " . $e->getMessage());
        }
    }
}
