<?php

declare(strict_types=1);

use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Response;

return function (App $app) {
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();

    // Custom Error Handling Middleware
    $app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));

            return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
        } catch (UnauthorizedException $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));

            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (NotFoundException|HttpNotFoundException $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));

            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $e) {
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
