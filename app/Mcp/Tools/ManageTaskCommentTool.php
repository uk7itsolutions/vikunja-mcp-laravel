<?php

namespace App\Mcp\Tools;

use App\Services\VikunjaClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Add, update, or remove a comment on a task')]
class ManageTaskCommentTool extends VikunjaTool
{
    public function __construct(private readonly VikunjaClient $client) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'action' => $schema->string()->description('The action to perform: "add", "update", or "remove"')->required(),
            'task_id' => $schema->integer()->description('The ID of the task')->required(),
            'comment_id' => $schema->integer()->description('The ID of the comment (required for update or remove)'),
            'comment_text' => $schema->string()->description('The text of the comment (required for add or update)'),
        ];
    }

    public function handle(Request $request): Response
    {
        try {
            $action = $request->get('action');
            $taskId = $request->get('task_id');
            
            if (!in_array($action, ['add', 'update', 'remove'])) {
                return Response::text("ERROR: Action must be 'add', 'update', or 'remove'.");
            }

            if ($action === 'add') {
                $text = $request->get('comment_text');
                if (!$text) {
                    return Response::text("ERROR: comment_text is required for adding a comment.");
                }
                
                $result = $this->client->put("tasks/{$taskId}/comments", [
                    'comment' => $text
                ]);
                
                return Response::text(json_encode(['success' => true, 'message' => "Comment added to task {$taskId}", 'comment' => $result]));
            } 
            
            if ($action === 'update') {
                $commentId = $request->get('comment_id');
                $text = $request->get('comment_text');
                if (!$commentId || !$text) {
                    return Response::text("ERROR: comment_id and comment_text are required for updating a comment.");
                }
                
                $result = $this->client->post("tasks/{$taskId}/comments/{$commentId}", [
                    'comment' => $text
                ]);
                
                return Response::text(json_encode(['success' => true, 'message' => "Comment {$commentId} updated on task {$taskId}", 'comment' => $result]));
            }
            
            if ($action === 'remove') {
                $commentId = $request->get('comment_id');
                if (!$commentId) {
                    return Response::text("ERROR: comment_id is required for removing a comment.");
                }
                
                $this->client->delete("tasks/{$taskId}/comments/{$commentId}");
                
                return Response::text(json_encode(['success' => true, 'message' => "Comment {$commentId} removed from task {$taskId}"]));
            }

        } catch (\Throwable $e) {
            return Response::text("ERROR: " . $e->getMessage());
        }
    }
}
