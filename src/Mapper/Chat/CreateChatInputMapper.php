<?php

declare(strict_types=1);

namespace App\Mapper\Chat;

use App\Dto\Chat\CreateChatInput;
use App\Exception\ValidationException;

final readonly class CreateChatInputMapper
{
    public function map(array $data, int $userId): CreateChatInput
    {
        $title = $data['title'] ?? '';

        if (!is_string($title) || trim($title) === '') {
            throw new ValidationException('Title is required');
        }

        return new CreateChatInput($userId, trim($title));
    }
}
