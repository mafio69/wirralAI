<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Auth\LoginInput;
use App\Dto\Auth\LoginResult;
use App\Dto\Auth\RegisterInput;
use App\Exception\UnauthorizedException;
use App\Exception\ValidationException;
use App\Repository\UserRepository;

final readonly class AuthService
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function register(RegisterInput $input): void
    {
        $existingUser = $this->userRepository->findByEmail($input->email);
        if ($existingUser) {
            throw new ValidationException('Email is already taken');
        }

        $passwordHash = password_hash($input->password, PASSWORD_DEFAULT);
        $this->userRepository->createUser($input->email, $passwordHash);
    }

    public function login(LoginInput $input): LoginResult
    {
        $user = $this->userRepository->findByEmail($input->email);
        
        if (!$user || !password_verify($input->password, $user['password_hash'])) {
            throw new UnauthorizedException('Invalid credentials');
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 day'));
        
        $this->userRepository->createSession((int) $user['id'], $token, $expiresAt);

        return new LoginResult((int) $user['id'], $token);
    }
    
    public function validateToken(string $token): int
    {
        $session = $this->userRepository->findSessionByToken($token);
        if (!$session) {
            throw new UnauthorizedException('Invalid or expired token');
        }
        
        return (int) $session['user_id'];
    }
}
