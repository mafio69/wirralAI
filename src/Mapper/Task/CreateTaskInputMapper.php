<?php

declare(strict_types=1);

namespace App\Mapper\Task;

use App\Dto\Task\CreateTaskInput;
use App\Exception\ValidationException;

final readonly class CreateTaskInputMapper
{
    public function map(array $data, int $userId): CreateTaskInput
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

        return new CreateTaskInput(
            userId: $userId,
            title: trim($title),
            description: $description,
            status: $status,
        );
    }
}
