<?php

declare(strict_types=1);

use App\Infrastructure\Database\ConnectionFactory;
use App\Infrastructure\Database\MigrationRunner;
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
    ]);
};
