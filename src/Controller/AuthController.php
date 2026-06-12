<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\UnauthorizedException;
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
    ) {}

    public function register(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $input = $this->registerInputMapper->map($data);
        
        $this->authService->register($input);
        
        return JsonResponder::respond($response, ['message' => 'User registered successfully'], 201);
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $input = $this->loginInputMapper->map($data);
        
        $result = $this->authService->login($input);
        
        return JsonResponder::respond($response, [
            'token' => $result->token
        ]);
    }

    public function me(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!str_starts_with($authHeader, 'Bearer ')) {
            throw new UnauthorizedException('Missing or invalid Authorization header');
        }
        
        $token = substr($authHeader, 7);
        $userId = $this->authService->validateToken($token);
        
        $user = $this->userRepository->findById($userId);
        
        return JsonResponder::respond($response, [
            'id' => $user['id'],
            'email' => $user['email']
        ]);
    }
}
