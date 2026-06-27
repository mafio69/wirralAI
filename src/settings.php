<?php

declare(strict_types=1);

use DI\ContainerBuilder;

$env = null;

/** @var string $env */
return function (ContainerBuilder $containerBuilder) use ($env) {

    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => $_ENV['APP_ENV'] === 'dev',
            'db' => [
                'path' => __DIR__.'/../var/database/app.sqlite',
            ],
            'ovh_ai' => [
                'token' => $_ENV['OVH_AI_ENDPOINTS_ACCESS_TOKEN'] ?? 'no-token-set',
                'url' => $_ENV['OVH_AI_BASE_URI'] ??  'https://oai.endpoints.kepler.ai.cloud.ovh.net/v1', //'https://qwen3-coder-30b-a3b-instruct.endpoints.kepler.ai.cloud.ovh.net/api/openai_compat/v1',
                'model' => $_ENV['OVH_AI_MODEL'] ?? 'Qwen3-Coder-30B-A3B-Instruct',
                'timeout' => (int)($_ENV['OVH_AI_TIMEOUT'] ?? 60),
            ],
            'logger' => [
                'log_dir' => __DIR__.'/../var/logs',
                'min_level' => $_ENV['LOG_LEVEL'] ?? 'warning',
                'date_format' => 'Y-m-d H:i:s',
                'timezone' => 'Europe/Warsaw',
            ],
            'app_url' => $_ENV['APP_URL'] ?? 'http://localhost:8181',
            'mail' => [
                'dsn' => $_ENV['MAIL_DSN'] ?? 'smtp://localhost:1025',
            ],
        ],
    ]);
};
