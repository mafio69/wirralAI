<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Task\CreateTaskInput;
use App\Dto\Task\TaskResult;
use App\Dto\Task\UpdateTaskInput;
use App\Exception\NotFoundException;
use App\Repository\TaskRepository;

final readonly class TaskService
{
    public function __construct(private readonly TaskRepository $taskRepository)
    {
    }

    public function createTask(CreateTaskInput $input): TaskResult
    {
        $id = $this->taskRepository->create(
            $input->userId,
            $input->title,
            $input->description,
            $input->status,
        );

        return new TaskResult(
            id: $id,
            userId: $input->userId,
            title: $input->title,
            description: $input->description,
            status: $input->status,
            createdAt: date('Y-m-d H:i:s'),
            updatedAt: date('Y-m-d H:i:s'),
        );
    }

    public function getUserTasks(int $userId): array
    {
        $rows = $this->taskRepository->findByUserId($userId);

        return array_map(
            fn (array $row) => new TaskResult(
                id: (int) $row['id'],
                userId: (int) $row['user_id'],
                title: $row['title'],
                description: $row['description'],
                status: $row['status'],
                createdAt: $row['created_at'],
                updatedAt: $row['updated_at'],
            ),
            $rows
        );
    }

    public function updateTask(UpdateTaskInput $input): TaskResult
    {
        $task = $this->taskRepository->findById($input->taskId);

        if (!$task) {
            throw new NotFoundException('Task not found');
        }

        if ((int) $task['user_id'] !== $input->userId) {
            throw new NotFoundException('Task not found');
        }

        $this->taskRepository->update(
            $input->taskId,
            $input->title,
            $input->description,
            $input->status,
        );

        return new TaskResult(
            id: $input->taskId,
            userId: $input->userId,
            title: $input->title,
            description: $input->description,
            status: $input->status,
            createdAt: $task['created_at'],
            updatedAt: date('Y-m-d H:i:s'),
        );
    }

    public function deleteTask(int $taskId, int $userId): void
    {
        $task = $this->taskRepository->findById($taskId);

        if (!$task) {
            throw new NotFoundException('Task not found');
        }

        if ((int) $task['user_id'] !== $userId) {
            throw new NotFoundException('Task not found');
        }

        $this->taskRepository->delete($taskId);
    }
}
