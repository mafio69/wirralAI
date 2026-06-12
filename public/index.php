<?php

declare(strict_types=1);

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

$containerBuilder = new ContainerBuilder();

$settings = require __DIR__ . '/../src/settings.php';
$settings($containerBuilder);

$dependencies = require __DIR__ . '/../src/dependencies.php';
$dependencies($containerBuilder);

$container = $containerBuilder->build();

// Run database migrations
try {
    $migrationRunner = $container->get(\App\Infrastructure\Database\MigrationRunner::class);
    $migrationRunner->run();
} catch (\DI\DependencyException $e) {
    error_log('Migration dependency error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    error_log('Trace: ' . $e->getTraceAsString());
    http_response_code(503);
    require __DIR__ . '/error-pages/503.html';
    exit(1);
} catch (\DI\NotFoundException $e) {
    error_log('Migration not found error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    error_log('Trace: ' . $e->getTraceAsString());
    http_response_code(503);
    require __DIR__ . '/error-pages/503.html';
    exit(1);
} catch (\Exception $e) {
    error_log('Migration execution error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    error_log('Trace: ' . $e->getTraceAsString());
    http_response_code(503);
    require __DIR__ . '/error-pages/503.html';
    exit(1);
}

$app = Bridge::create($container);

$middleware = require __DIR__ . '/../src/middleware.php';
$middleware($app);

$routes = require __DIR__ . '/../src/routes.php';
$routes($app);

$app->run();
