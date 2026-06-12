<?php

declare(strict_types=1);

namespace App\Dto\Chat;

final readonly class ChatResult
{
    public function __construct(
        public int $id,
        public int $userId,
        public string $title,
        public string $createdAt,
        public ?array $messages = null,
    ) {}
}
