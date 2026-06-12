<?php

declare(strict_types=1);

namespace App\Dto\Task;

final readonly class TaskResult
{
    public function __construct(
        public int $id,
        public int $userId,
        public string $title,
        public ?string $description,
        public string $status,
        public string $createdAt,
        public string $updatedAt,
    ) {}
}
