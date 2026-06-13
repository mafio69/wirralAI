<?php

declare(strict_types=1);

use App\Infrastructure\AI\OvhAiClient;
use App\Infrastructure\Database\ConnectionFactory;
use App\Infrastructure\Database\MigrationRunner;
use App\Infrastructure\Mail\MailService;
use App\Repository\ChatRepository;
use App\Repository\UserRepository;
use App\Service\AuthService;
use App\Service\ChatService;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        PDO::class => function (ContainerInterface $c) {
            $settings = $c->get('settings')['db'];
            $factory = new ConnectionFactory($settings['path']);

            return $factory->create();
        },
        MigrationRunner::class => function (ContainerInterface $c) {
            return new MigrationRunner($c->get(PDO::class));
        },
        OvhAiClient::class => function (ContainerInterface $c) {
            $config = $c->get('settings')['ovh_ai'];

            return new OvhAiClient(
                url: $config['url'],
                token: $config['token'],
                model: $config['model'],
            );
        },
        MailService::class => function (ContainerInterface $c) {
            $dsn = $c->get('settings')['mail']['dsn'];

            return new MailService($dsn);
        },
        AuthService::class => function (ContainerInterface $c) {
            return new AuthService(
                userRepository: $c->get(UserRepository::class),
                mailService: $c->get(MailService::class),
                appUrl: $c->get('settings')['app_url'] ?? 'http://localhost:8181',
            );
        },
        ChatService::class => function (ContainerInterface $c) {
            return new ChatService(
                chatRepository: $c->get(ChatRepository::class),
                ovhAiClient: $c->get(OvhAiClient::class),
            );
        },
    ]);
};
