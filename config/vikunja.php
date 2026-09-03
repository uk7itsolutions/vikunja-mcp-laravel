<?php

return [
    // Base URL of your Vikunja instance, no trailing slash.
    // Example: https://tasks.yourdomain.com
    'base_url' => rtrim(env('VIKUNJA_BASE_URL', ''), '/'),

    // When true, every Vikunja API request and response is logged at debug
    // level (method, URL, payload, status, body). Failures are always logged at
    // error level regardless of this flag. To see the debug lines, also set
    // LOG_LEVEL=debug in .env. Turn this off in normal production use.
    'debug' => (bool) env('VIKUNJA_DEBUG', false),
];
