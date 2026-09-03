<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Get a specific task by its ID')]
class GetTaskTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'task_id' => $schema->integer()->description('The ID of the task to retrieve')->required(),
        ];
    }

    protected function execute(Request $request): Response
    {
        $taskId = $request->get('task_id');
        $task = $this->client->get("tasks/{$taskId}");
        
        return Response::text(json_encode($task));
    }
}
