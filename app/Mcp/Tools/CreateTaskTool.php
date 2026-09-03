<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Create a new task in a project')]
class CreateTaskTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'project_id' => $schema->integer()->description('The ID of the project to create the task in')->required(),
            'title' => $schema->string()->description('The title of the task')->required(),
            'description' => $schema->string()->description('The description of the task'),
            'priority' => $schema->integer()->description('The priority of the task (1-5)'),
            'due_date' => $schema->string()->description('The due date of the task (ISO 8601 format)'),
        ];
    }

    protected function execute(Request $request): Response
    {
        $projectId = $request->get('project_id');
        
        $data = ['title' => $request->get('title')];
        
        if ($request->has('description')) {
            $data['description'] = $request->get('description');
        }
        if ($request->has('priority')) {
            $data['priority'] = $request->get('priority');
        }
        if ($request->has('due_date')) {
            $data['due_date'] = $request->get('due_date');
        }

        $task = $this->client->put("projects/{$projectId}/tasks", $data);
        return Response::text(json_encode($task));
    }
}
