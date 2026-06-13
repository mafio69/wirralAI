<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\UnauthorizedException;
use App\Infrastructure\Http\JsonResponder;
use App\Mapper\Task\CreateTaskInputMapper;
use App\Mapper\Task\UpdateTaskInputMapper;
use App\Service\AuthService;
use App\Service\TaskService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class TaskController
{
    public function __construct(
        private readonly CreateTaskInputMapper $createTaskInputMapper,
        private readonly UpdateTaskInputMapper $updateTaskInputMapper,
        private readonly TaskService $taskService,
        private readonly AuthService $authService,
    ) {
    }

    public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = $this->getUserId($request);
        $tasks = $this->taskService->getUserTasks($userId);

        return JsonResponder::respond($response, $tasks);
    }

    private function getUserId(ServerRequestInterface $request): int
    {
        $token = $this->resolveToken($request);

        return $this->authService->validateToken($token);
    }

    private function resolveToken(ServerRequestInterface $request): string
    {
        $header = $request->getHeaderLine('Authorization');
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        $cookies = $request->getCookieParams();
        if (!empty($cookies['token'])) {
            return $cookies['token'];
        }

        throw new UnauthorizedException('Missing or invalid Authorization header');
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = $this->getUserId($request);
        $data = (array)$request->getParsedBody();
        $input = $this->createTaskInputMapper->map($data, $userId);
        $result = $this->taskService->createTask($input);

        return JsonResponder::respond($response, $result, 201);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $userId = $this->getUserId($request);
        $data = (array)$request->getParsedBody();
        $input = $this->updateTaskInputMapper->map($data, $id, $userId);
        $result = $this->taskService->updateTask($input);

        return JsonResponder::respond($response, $result);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $userId = $this->getUserId($request);
        $this->taskService->deleteTask($id, $userId);

        return JsonResponder::respond($response, ['message' => 'Task deleted successfully']);
    }
}
