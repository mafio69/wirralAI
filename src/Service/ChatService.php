<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Chat\AddChatMessageInput;
use App\Dto\Chat\ChatResult;
use App\Dto\Chat\CreateChatInput;
use App\Exception\NotFoundException;
use App\Repository\ChatRepository;

final readonly class ChatService
{
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    public function createChat(CreateChatInput $input): ChatResult
    {
        $id = $this->chatRepository->create($input->userId, $input->title);

        return new ChatResult(
            id: $id,
            userId: $input->userId,
            title: $input->title,
            createdAt: date('Y-m-d H:i:s'),
        );
    }

    public function getUserChats(int $userId): array
    {
        $rows = $this->chatRepository->findByUserId($userId);

        return array_map(
            fn (array $row) => new ChatResult(
                id: (int) $row['id'],
                userId: (int) $row['user_id'],
                title: $row['title'],
                createdAt: $row['created_at'],
            ),
            $rows
        );
    }

    public function getChatWithMessages(int $chatId, int $userId): ChatResult
    {
        $chat = $this->chatRepository->findById($chatId);

        if (!$chat) {
            throw new NotFoundException('Chat not found');
        }

        if ((int) $chat['user_id'] !== $userId) {
            throw new NotFoundException('Chat not found');
        }

        $messages = $this->chatRepository->findMessagesByChatId($chatId);

        return new ChatResult(
            id: (int) $chat['id'],
            userId: (int) $chat['user_id'],
            title: $chat['title'],
            createdAt: $chat['created_at'],
            messages: $messages,
        );
    }

    public function addMessage(AddChatMessageInput $input): array
    {
        $chat = $this->chatRepository->findById($input->chatId);

        if (!$chat) {
            throw new NotFoundException('Chat not found');
        }

        if ((int) $chat['user_id'] !== $input->userId) {
            throw new NotFoundException('Chat not found');
        }

        $messageId = $this->chatRepository->addMessage(
            $input->chatId,
            'user',
            $input->content,
        );

        return [
            'id' => $messageId,
            'chat_id' => $input->chatId,
            'role' => 'user',
            'content' => $input->content,
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }
}
