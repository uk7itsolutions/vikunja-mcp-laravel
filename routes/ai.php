<?php

use App\Mcp\Servers\VikunjaServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp', VikunjaServer::class)
    ->middleware(['validate.vikunja.key']);
