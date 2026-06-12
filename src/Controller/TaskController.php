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
    ) {}

    public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = $this->getUserId($request);
        $tasks = $this->taskService->getUserTasks($userId);

        return JsonResponder::respond($response, $tasks);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = $this->getUserId($request);
        $data = (array) $request->getParsedBody();
        $input = $this->createTaskInputMapper->map($data, $userId);
        $result = $this->taskService->createTask($input);

        return JsonResponder::respond($response, $result, 201);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $userId = $this->getUserId($request);
        $taskId = (int) ($args['id'] ?? 0);
        $data = (array) $request->getParsedBody();
        $input = $this->updateTaskInputMapper->map($data, $taskId, $userId);
        $result = $this->taskService->updateTask($input);

        return JsonResponder::respond($response, $result);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $userId = $this->getUserId($request);
        $taskId = (int) ($args['id'] ?? 0);
        $this->taskService->deleteTask($taskId, $userId);

        return JsonResponder::respond($response, ['message' => 'Task deleted successfully']);
    }

    private function getUserId(ServerRequestInterface $request): int
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!str_starts_with($authHeader, 'Bearer ')) {
            throw new UnauthorizedException('Missing or invalid Authorization header');
        }

        $token = substr($authHeader, 7);

        return $this->authService->validateToken($token);
    }
}
