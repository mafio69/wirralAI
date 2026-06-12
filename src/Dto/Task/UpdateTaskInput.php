<?php

declare(strict_types=1);

namespace App\Dto\Task;

final readonly class UpdateTaskInput
{
    public function __construct(
        public int $taskId,
        public int $userId,
        public string $title,
        public ?string $description,
        public string $status,
    ) {}
}
