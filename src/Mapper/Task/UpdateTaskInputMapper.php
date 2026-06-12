<?php

declare(strict_types=1);

namespace App\Mapper\Task;

use App\Dto\Task\UpdateTaskInput;
use App\Exception\ValidationException;

final readonly class UpdateTaskInputMapper
{
    public function map(array $data, int $taskId, int $userId): UpdateTaskInput
    {
        $title = $data['title'] ?? '';
        $status = $data['status'] ?? 'todo';
        $description = isset($data['description']) && is_string($data['description'])
            ? trim($data['description']) : null;

        if (!is_string($title) || trim($title) === '') {
            throw new ValidationException('Title is required');
        }

        $allowed = ['todo', 'in_progress', 'done'];
        if (!in_array($status, $allowed, true)) {
            throw new ValidationException('Invalid status. Allowed: ' . implode(', ', $allowed));
        }

        return new UpdateTaskInput(
            taskId: $taskId,
            userId: $userId,
            title: trim($title),
            description: $description,
            status: $status,
        );
    }
}
