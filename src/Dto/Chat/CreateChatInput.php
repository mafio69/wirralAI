<?php

declare(strict_types=1);

namespace App\Dto\Chat;

final readonly class CreateChatInput
{
    public function __construct(
        public int $userId,
        public string $title,
    ) {
    }
}
