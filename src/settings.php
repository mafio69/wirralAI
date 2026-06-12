<?php

declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => $_ENV['APP_ENV'] === 'dev',
            'db' => [
                'path' => __DIR__ . '/../var/database/app.sqlite',
            ],
            'ovh_ai' => [
                'token' => $_ENV['OVH_AI_TOKEN'] ?? '',
                'url' => $_ENV['OVH_AI_URL'] ?? 'https://qwen3-coder-30b-a3b-instruct.endpoints.kepler.ai.cloud.ovh.net/api/openai_compat/v1',
                'model' => 'Qwen3-Coder-30B-A3B-Instruct',
            ]
        ],
    ]);
};
