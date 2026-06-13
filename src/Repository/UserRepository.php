<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final readonly class UserRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function createUser(string $email, string $passwordHash): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (email, password_hash, created_at) VALUES (:email, :password_hash, :created_at)'
        );
        $stmt->execute([
            'email' => $email,
            'password_hash' => $passwordHash,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function createSession(int $userId, string $token, string $expiresAt): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO sessions (user_id, token, created_at, expires_at) VALUES (:user_id, :token, :created_at, :expires_at)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => $expiresAt,
        ]);
    }

    public function findSessionByToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM sessions WHERE token = :token AND expires_at > :now');
        $stmt->execute([
            'token' => $token,
            'now' => date('Y-m-d H:i:s'),
        ]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function setVerificationToken(int $userId, string $token): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET email_verification_token = :token WHERE id = :id');
        $stmt->execute(['token' => $token, 'id' => $userId]);
    }

    public function findByVerificationToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email_verification_token = :token');
        $stmt->execute(['token' => $token]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function verifyEmail(int $userId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users SET email_confirmed_at = :now, email_verification_token = NULL WHERE id = :id'
        );
        $stmt->execute(['now' => date('Y-m-d H:i:s'), 'id' => $userId]);
    }
}
