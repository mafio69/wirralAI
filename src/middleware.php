<?php

declare(strict_types=1);

use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Response;

return function (App $app) {
    $logger = $app->getContainer()->get(LoggerInterface::class);

    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();

    // Custom Error Handling Middleware
    $app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($logger): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            $logger->info($e->getMessage());

            $response = new Response();
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));

            return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
        } catch (UnauthorizedException $e) {
            $logger->warning($e->getMessage());

            $response = new Response();
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));

            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (NotFoundException|HttpNotFoundException $e) {
            $logger->warning($e->getMessage());

            $response = new Response();
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));

            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $e) {
            $logger->error($e->getMessage(), ['exception' => $e]);

            $response = new Response();
            $error = ['error' => 'Internal Server Error'];
            if ($_ENV['APP_ENV'] === 'dev') {
                $error['message'] = $e->getMessage();
                $error['trace'] = $e->getTraceAsString();
            }
            $response->getBody()->write(json_encode($error));

            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->addErrorMiddleware(
        $app->getContainer()->get('settings')['displayErrorDetails'],
        true,
        true
    );
};
