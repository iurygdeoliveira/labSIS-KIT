<?php

declare(strict_types=1);

return [
    'app_name' => null,

    'github' => [
        'repository' => env('GITHUB_REPOSITORY'),
        'token' => null,
        'cache_ttl' => 3600,
    ],
];
