<?php

declare(strict_types=1);

namespace App\Dto\Auth;

final readonly class LoginResult
{
    public function __construct(
        public int $userId,
        public string $token
    ) {}
}
