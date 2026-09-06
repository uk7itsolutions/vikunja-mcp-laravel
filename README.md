# Vikunja MCP

A Laravel-based Model Context Protocol (MCP) server that connects AI assistants (like Claude) directly to your self-hosted [Vikunja](https://vikunja.io/) task management instance. 

By exposing a standardized set of tools, this MCP allows your AI to seamlessly read, create, update, and manage your Vikunja projects, tasks, and task relationships, all through natural language.

## Available Actions (MCP Tools)

This server currently exposes the following capabilities to the AI:

- **`list-projects`**: Fetch a list of all available projects in your Vikunja instance.
- **`list-tasks`**: Retrieve tasks belonging to a specific project.
- **`list-buckets`**: List all Kanban buckets for a project or specific view.
- **`list-labels`**: List all available labels in Vikunja (supports searching).
- **`get-task`**: Fetch complete details for a specific task.
- **`create-task`**: Create a new task in a specified project.
- **`update-task`**: Modify an existing task.
- **`delete-task`**: Permanently delete a task.
- **`manage-task-label`**: Add or remove a label from a task (automatically creates the label if it doesn't exist).
- **`create-task-relation`**: Add a relationship/dependency between two tasks.
- **`remove-task-relation`**: Remove an existing relationship between two tasks.
- **`move-task-to-bucket`**: Move a task to a specific Kanban bucket.

## Installation & Setup

1. **Clone the Repository**
   ```bash
   git clone https://github.com/uk7itsolutions/vikunja-mcp-laravel.git
   cd vikunja-mcp-laravel
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Environment Configuration**
   Copy the example environment file and configure it:
   ```bash
   cp .env.example .env
   ```
   Open `.env` and set your Vikunja instance URL:
   ```env
   VIKUNJA_BASE_URL=https://tasks.yourdomain.com
   ```

4. **Connect to Claude**
   In Claude Desktop (or Claude Code), add the remote MCP connection, passing your Vikunja API/User token as a Bearer header.
   
   *Example using npx (for Claude Code or CLI):*
   ```bash
   npx -y mcp-remote https://projects-mcp.yourdomain.com/mcp --header "Authorization: Bearer YOUR_VIKUNJA_API_TOKEN"
   ```

## Troubleshooting

If you encounter errors connecting or running tools, check the Laravel error logs on your hosting server (`storage/logs/laravel.log`). The server intercepts Vikunja API failures and logs them there, along with passing explicit error strings back to the AI.
