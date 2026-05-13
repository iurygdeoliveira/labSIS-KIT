<?php

declare(strict_types=1);

/**
 * Estatísticas da landing (quando não há models Project/Developer no app).
 *
 * .env: LANDING_STATS_PROJECTS_EVALUATION, LANDING_STATS_DEVELOPERS
 * Models opcionais: LANDING_PROJECT_MODEL, LANDING_DEVELOPER_MODEL
 */
return [
    'eloquent_models' => [
        'project' => env('LANDING_PROJECT_MODEL', 'App\Models\Project'),
        'developer' => env('LANDING_DEVELOPER_MODEL', 'App\Models\Developer'),
    ],

    'stats' => [
        'projects_in_evaluation' => env('LANDING_STATS_PROJECTS_EVALUATION'),
        'developers_involved' => env('LANDING_STATS_DEVELOPERS'),
    ],
];
