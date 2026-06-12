<?php

declare(strict_types=1);

namespace App\Mapper\Auth;

use App\Dto\Auth\LoginInput;
use App\Exception\ValidationException;

final readonly class LoginInputMapper
{
    public function map(array $data): LoginInput
    {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (!is_string($email) || trim($email) === '') {
            throw new ValidationException('Email is required');
        }

        if (!is_string($password) || trim($password) === '') {
            throw new ValidationException('Password is required');
        }

        return new LoginInput(trim($email), $password);
    }
}
