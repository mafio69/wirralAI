<?php

declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => $_ENV['APP_ENV'] === 'dev',
            'db' => [
                'path' => __DIR__.'/../var/database/app.sqlite',
            ],
            'ovh_ai' => [
                'token' => $_ENV['OVH_AI_ENDPOINTS_ACCESS_TOKEN'] ?? '',
                'url' => $_ENV['OVH_AI_BASE_URI'] ?? 'https://qwen3-coder-30b-a3b-instruct.endpoints.kepler.ai.cloud.ovh.net/api/openai_compat/v1',
                'model' => $_ENV['OVH_AI_MODEL'] ?? 'Qwen3-Coder-30B-A3B-Instruct',
            ],
            'app_url' => $_ENV['APP_URL'] ?? 'http://localhost:8181',
            'mail' => [
                'dsn' => $_ENV['MAIL_DSN'] ?? 'smtp://localhost:1025',
            ],
        ],
    ]);
};
