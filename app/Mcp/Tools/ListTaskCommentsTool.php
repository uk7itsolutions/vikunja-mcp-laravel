<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('List all comments for a specific task')]
class ListTaskCommentsTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'task_id' => $schema->integer()->description('The ID of the task')->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        try {
            $taskId = $request->get('task_id');
            $comments = $this->client->get("tasks/{$taskId}/comments");
            
            return Response::text(json_encode($comments));
        } catch (\Throwable $e) {
            return Response::text("ERROR: " . $e->getMessage());
        }
    }
}
