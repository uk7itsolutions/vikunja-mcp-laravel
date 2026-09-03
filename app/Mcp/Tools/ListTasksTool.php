<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('List tasks for a specific project')]
class ListTasksTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'project_id' => $schema->integer()->description('The ID of the project to list tasks for')->required(),
            'page' => $schema->integer()->description('Page number')->default(1),
        ];
    }

    protected function execute(Request $request): Response
    {
        $projectId = $request->get('project_id');
        $page = $request->get('page', 1);

        $tasks = $this->client->get('tasks', [
            'filter' => 'project_id = ' . $projectId,
            'page' => $page
        ]);
        return Response::text(json_encode($tasks));
    }
}
