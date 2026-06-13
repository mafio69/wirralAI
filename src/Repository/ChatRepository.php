<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final readonly class ChatRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function create(int $userId, string $title): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO chats (user_id, title, created_at) VALUES (:user_id, :title, :created_at)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, title, created_at FROM chats WHERE user_id = :user_id ORDER BY created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, title, created_at FROM chats WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function addMessage(int $chatId, string $role, string $content): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO chat_messages (chat_id, role, content, created_at) VALUES (:chat_id, :role, :content, :created_at)'
        );
        $stmt->execute([
            'chat_id' => $chatId,
            'role' => $role,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function findMessagesByChatId(int $chatId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, role, content, created_at FROM chat_messages WHERE chat_id = :chat_id ORDER BY created_at ASC'
        );
        $stmt->execute(['chat_id' => $chatId]);

        return $stmt->fetchAll();
    }
}
