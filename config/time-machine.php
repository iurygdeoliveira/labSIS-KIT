<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Master switch. When false, Time Machine records nothing and adds
    | virtually zero overhead. By default it is only active outside of
    | production so profiling never leaks into a live environment.
    |
    */

    'enabled' => env('TIME_MACHINE_ENABLED', env('APP_DEBUG', false)),

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    |
    | The web UI where recorded request profiles are listed and visualized.
    | "path" is the URI prefix, "middleware" guards access, and "enabled"
    | can hide the UI entirely (e.g. record via API only).
    |
    */

    'dashboard' => [
        'enabled' => env('TIME_MACHINE_DASHBOARD', true),
        'path' => env('TIME_MACHINE_PATH', 'time-machine'),
        'middleware' => ['web'],
        'per_page' => (int) env('TIME_MACHINE_PER_PAGE', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    |
    | Where recorded request profiles are persisted. The "file" driver writes
    | one JSON file per request under the given directory. "max_records" caps
    | how many profiles are retained (oldest are pruned first).
    |
    */

    'storage' => [
        'driver' => env('TIME_MACHINE_DRIVER', 'file'),
        'path' => storage_path('time-machine'),
        'max_records' => (int) env('TIME_MACHINE_MAX_RECORDS', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Collectors
    |--------------------------------------------------------------------------
    |
    | Toggle the individual data collectors. Lifecycle timing is always on;
    | the rest can be disabled to reduce overhead or noise.
    |
    */

    'collectors' => [
        'queries' => env('TIME_MACHINE_COLLECT_QUERIES', true),
        'memory' => env('TIME_MACHINE_COLLECT_MEMORY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignored Paths
    |--------------------------------------------------------------------------
    |
    | Request paths that should never be profiled. The dashboard's own paths
    | are always excluded automatically. Supports "*" wildcards.
    |
    */

    'ignore_paths' => [
        'telescope*',
        'horizon*',
        '_debugbar*',
        '*.js',
        '*.css',
        '*.ico',
        '*.png',
        '*.jpg',
        '*.svg',
    ],

    /*
    |--------------------------------------------------------------------------
    | Slow Thresholds (ms)
    |--------------------------------------------------------------------------
    |
    | Used purely for UI highlighting: requests / queries slower than these
    | are flagged in the dashboard.
    |
    */

    'thresholds' => [
        'slow_request' => (int) env('TIME_MACHINE_SLOW_REQUEST', 500),
        'slow_query' => (int) env('TIME_MACHINE_SLOW_QUERY', 50),
    ],

];
