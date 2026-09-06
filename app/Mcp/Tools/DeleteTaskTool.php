<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Delete a task')]
class DeleteTaskTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'task_id' => $schema->integer()->description('The ID of the task to delete')->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        try {
            $taskId = $request->get('task_id');
            $this->client->delete("tasks/{$taskId}");
            
            return Response::text(json_encode(['success' => true, 'message' => 'Task deleted successfully']));
        } catch (\Throwable $e) {
            return Response::text("ERROR: " . $e->getMessage());
        }
    }
}
