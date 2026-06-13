<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Chat\AddChatMessageInput;
use App\Dto\Chat\ChatResult;
use App\Dto\Chat\CreateChatInput;
use App\Exception\NotFoundException;
use App\Infrastructure\AI\OvhAiClient;
use App\Repository\ChatRepository;
use Psr\Log\LoggerInterface;

final readonly class ChatService
{
    public function __construct(
        private readonly ChatRepository $chatRepository,
        private readonly OvhAiClient $ovhAiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function createChat(CreateChatInput $input): ChatResult
    {
        $id = $this->chatRepository->create($input->userId, $input->title);

        $this->logger->info('Chat created', ['chat_id' => $id, 'user_id' => $input->userId]);

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
            fn(array $row) => new ChatResult(
                id: (int)$row['id'],
                userId: (int)$row['user_id'],
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

        if ((int)$chat['user_id'] !== $userId) {
            throw new NotFoundException('Chat not found');
        }

        $messages = $this->chatRepository->findMessagesByChatId($chatId);

        return new ChatResult(
            id: (int)$chat['id'],
            userId: (int)$chat['user_id'],
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

        if ((int)$chat['user_id'] !== $input->userId) {
            throw new NotFoundException('Chat not found');
        }

        $messageId = $this->chatRepository->addMessage(
            $input->chatId,
            'user',
            $input->content,
        );

        $userMessage = [
            'id' => $messageId,
            'chat_id' => $input->chatId,
            'role' => 'user',
            'content' => $input->content,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $history = $this->chatRepository->findMessagesByChatId($input->chatId);

        $messages = [];
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        try {
            $aiContent = $this->ovhAiClient->generate($messages);
        } catch (\Throwable $e) {
            $this->logger->error('AI call failed', ['chat_id' => $input->chatId, 'exception' => $e]);
            throw $e;
        }

        $aiMessageId = $this->chatRepository->addMessage(
            $input->chatId,
            'assistant',
            $aiContent,
        );

        $assistantMessage = [
            'id' => $aiMessageId,
            'chat_id' => $input->chatId,
            'role' => 'assistant',
            'content' => $aiContent,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return [$userMessage, $assistantMessage];
    }
}
