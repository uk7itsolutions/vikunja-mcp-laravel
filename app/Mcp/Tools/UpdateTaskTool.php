<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Update an existing task')]
class UpdateTaskTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'task_id' => $schema->integer()->description('The ID of the task to update')->required(),
            'title' => $schema->string()->description('The title of the task'),
            'description' => $schema->string()->description('The description of the task'),
            'done' => $schema->boolean()->description('Whether the task is completed'),
            'priority' => $schema->integer()->description('The priority of the task (1-5)'),
            'due_date' => $schema->string()->description('The due date of the task (ISO 8601 format)'),
        ];
    }

    protected function execute(Request $request): Response
    {
        $taskId = $request->get('task_id');
        $data = [];

        if ($request->has('title')) {
            $data['title'] = $request->get('title');
        }
        if ($request->has('description')) {
            $data['description'] = $request->get('description');
        }
        if ($request->has('done')) {
            $data['done'] = $request->get('done');
        }
        if ($request->has('priority')) {
            $data['priority'] = $request->get('priority');
        }
        if ($request->has('due_date')) {
            $data['due_date'] = $request->get('due_date');
        }

        $task = $this->client->post("tasks/{$taskId}", $data);
        return Response::text(json_encode($task));
    }
}
