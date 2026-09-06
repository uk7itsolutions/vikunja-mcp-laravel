<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Add or remove a label from a task. Will automatically find or create the label by name.')]
class ManageTaskLabelTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'action' => $schema->string()->description('The action to perform: "add" or "remove"')->required(),
            'task_id' => $schema->integer()->description('The ID of the task')->required(),
            'label_name' => $schema->string()->description('The name of the label (e.g. "bug", "feature")')->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        try {
            $action = $request->get('action');
            $taskId = $request->get('task_id');
            $labelName = $request->get('label_name');

            if (!in_array($action, ['add', 'remove'])) {
                return Response::text("ERROR: Action must be 'add' or 'remove'.");
            }

            // 1. Search for the label by name
            $labels = $this->client->get('labels', ['s' => $labelName]);
            
            $labelId = null;
            if (is_array($labels)) {
                foreach ($labels as $lbl) {
                    if (strcasecmp($lbl['title'], $labelName) === 0) {
                        $labelId = $lbl['id'];
                        break;
                    }
                }
            }

            // 2. If it doesn't exist and action is 'add', create it!
            if (!$labelId) {
                if ($action === 'remove') {
                    return Response::text(json_encode(['success' => true, 'message' => "Label '{$labelName}' does not exist on the server, so it's already not on the task."]));
                }
                
                // Create a random hex color for the new label
                $hexColor = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                
                $newLabel = $this->client->put('labels', [
                    'title' => $labelName,
                    'hex_color' => $hexColor,
                ]);
                $labelId = $newLabel['id'];
            }

            // 3. Apply the action
            if ($action === 'add') {
                $result = $this->client->put("tasks/{$taskId}/labels", [
                    'label_id' => $labelId
                ]);
                return Response::text(json_encode(['success' => true, 'message' => "Label '{$labelName}' added to task {$taskId}"]));
            } else {
                $result = $this->client->delete("tasks/{$taskId}/labels/{$labelId}");
                return Response::text(json_encode(['success' => true, 'message' => "Label '{$labelName}' removed from task {$taskId}"]));
            }

        } catch (\Throwable $e) {
            return Response::text("ERROR: " . $e->getMessage());
        }
    }
}
