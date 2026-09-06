<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Remove a relationship between two tasks')]
class RemoveTaskRelationTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'task_id' => $schema->integer()->description('The ID of the base task')->required(),
            'other_task_id' => $schema->integer()->description('The ID of the other related task')->required(),
            'relation_kind' => $schema->string()->description('The kind of relation to remove (e.g., subtask, blocking, related, etc.)')->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        try {
            $taskId = $request->get('task_id');
            $otherTaskId = $request->get('other_task_id');
            $relationKind = $request->get('relation_kind');

            $result = $this->client->delete("tasks/{$taskId}/relations/{$relationKind}/{$otherTaskId}");
            return Response::text(json_encode(['success' => true, 'message' => 'Relation removed']));
        } catch (\Throwable $e) {
            return Response::text("ERROR: " . $e->getMessage());
        }
    }
}
