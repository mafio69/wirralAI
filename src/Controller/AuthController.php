<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\UnauthorizedException;
use App\Exception\ValidationException;
use App\Infrastructure\Http\JsonResponder;
use App\Mapper\Auth\LoginInputMapper;
use App\Mapper\Auth\RegisterInputMapper;
use App\Repository\UserRepository;
use App\Service\AuthService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AuthController
{
    public function __construct(
        private readonly RegisterInputMapper $registerInputMapper,
        private readonly LoginInputMapper $loginInputMapper,
        private readonly AuthService $authService,
        private readonly UserRepository $userRepository
    ) {
    }

    public function register(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $input = $this->registerInputMapper->map($data);

        $this->authService->register($input);

        return JsonResponder::respond($response, ['message' => 'User registered successfully'], 201);
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $input = $this->loginInputMapper->map($data);

        $result = $this->authService->login($input);

        setcookie('token', $result->token, [
            'expires' => strtotime($result->expiresAt),
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        return JsonResponder::respond($response, [
            'token' => $result->token,
        ]);
    }

    public function verifyEmail(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $token = $request->getQueryParams()['token'] ?? '';
        if ($token === '') {
            throw new ValidationException('Token is required');
        }

        $this->authService->verifyEmail($token);

        return JsonResponder::respond($response, ['message' => 'Email confirmed successfully']);
    }

    public function me(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $token = $this->resolveToken($request);
        $userId = $this->authService->validateToken($token);

        $user = $this->userRepository->findById($userId);

        return JsonResponder::respond($response, [
            'id' => $user['id'],
            'email' => $user['email'],
        ]);
    }

    private function resolveToken(ServerRequestInterface $request): string
    {
        $header = $request->getHeaderLine('Authorization');
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        $cookies = $request->getCookieParams();
        if (!empty($cookies['token'])) {
            return $cookies['token'];
        }

        throw new UnauthorizedException('Missing or invalid Authorization header');
    }
}
