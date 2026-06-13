<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final readonly class TaskRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function create(int $userId, string $title, ?string $description, string $status): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO tasks (user_id, title, description, status, created_at, updated_at)
              VALUES (:user_id, :title, :description, :status, :created_at, :updated_at)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, title, description, status, created_at, updated_at
              FROM tasks WHERE user_id = :user_id ORDER BY created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, title, description, status, created_at, updated_at
              FROM tasks WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function update(int $id, string $title, ?string $description, string $status): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE tasks SET title = :title, description = :description, status = :status, updated_at = :updated_at
              WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Batch insert for tasks
     */
    public function createBatch(array $tasks): array
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO tasks (user_id, title, description, status, created_at, updated_at)
                  VALUES (:user_id, :title, :description, :status, :created_at, :updated_at)'
            );
            
            $ids = [];
            foreach ($tasks as $task) {
                $stmt->execute([
                    'user_id' => $task['user_id'],
                    'title' => $task['title'],
                    'description' => $task['description'],
                    'status' => $task['status'],
                    'created_at' => $task['created_at'],
                    'updated_at' => $task['updated_at'],
                ]);
                
                $ids[] = (int)$this->pdo->lastInsertId();
            }
            
            $this->pdo->commit();
            return $ids;
        } catch (\Throwable $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
}
