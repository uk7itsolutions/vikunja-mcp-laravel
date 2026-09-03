<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\ListProjectsTool;
use App\Mcp\Tools\ListTasksTool;
use App\Mcp\Tools\CreateTaskTool;
use App\Mcp\Tools\GetTaskTool;
use App\Mcp\Tools\UpdateTaskTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Vikunja')]
#[Version('1.0.0')]
#[Instructions('Manage tasks and projects in Vikunja.')]
class VikunjaServer extends Server
{
    protected array $tools = [
        ListProjectsTool::class,
        ListTasksTool::class,
        CreateTaskTool::class,
        GetTaskTool::class,
        UpdateTaskTool::class,
    ];
}
