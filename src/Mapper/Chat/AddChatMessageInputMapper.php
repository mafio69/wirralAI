<?php

declare(strict_types=1);

namespace App\Mapper\Chat;

use App\Dto\Chat\AddChatMessageInput;
use App\Exception\ValidationException;

final readonly class AddChatMessageInputMapper
{
    public function map(array $data, int $chatId, int $userId): AddChatMessageInput
    {
        $content = $data['content'] ?? '';

        if (!is_string($content) || trim($content) === '') {
            throw new ValidationException('Message content is required');
        }

        return new AddChatMessageInput($chatId, $userId, trim($content));
    }
}
