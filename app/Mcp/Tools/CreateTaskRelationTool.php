<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Add a relationship between two tasks (e.g. subtask, related, blocking)')]
class CreateTaskRelationTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'task_id' => $schema->integer()->description('The ID of the base task')->required(),
            'other_task_id' => $schema->integer()->description('The ID of the other task to relate to')->required(),
            'relation_kind' => $schema->string()->description('The kind of relation (subtask, parenttask, related, duplicateof, duplicates, blocking, blocked, precedes, follows, copiedfrom, copiedto)')->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        try {
            $taskId = $request->get('task_id');
            $data = [
                'other_task_id' => $request->get('other_task_id'),
                'relation_kind' => $request->get('relation_kind'),
            ];

            $relation = $this->client->put("tasks/{$taskId}/relations", $data);
            return Response::text(json_encode($relation));
        } catch (\Throwable $e) {
            return Response::text("ERROR: " . $e->getMessage());
        }
    }
}
