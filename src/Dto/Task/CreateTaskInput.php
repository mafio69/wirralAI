<?php

declare(strict_types=1);

namespace App\Dto\Task;

final readonly class CreateTaskInput
{
    public function __construct(
        public int $userId,
        public string $title,
        public ?string $description,
        public string $status
    ) {
    }
}
