<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\UnauthorizedException;
use App\Infrastructure\Http\JsonResponder;
use App\Mapper\Chat\AddChatMessageInputMapper;
use App\Mapper\Chat\CreateChatInputMapper;
use App\Service\AuthService;
use App\Service\ChatService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class ChatController
{
    public function __construct(
        private readonly CreateChatInputMapper $createChatInputMapper,
        private readonly AddChatMessageInputMapper $addChatMessageInputMapper,
        private readonly ChatService $chatService,
        private readonly AuthService $authService,
    ) {
    }

    public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = $this->getUserId($request);
        $chats = $this->chatService->getUserChats($userId);

        return JsonResponder::respond($response, $chats);
    }

    private function getUserId(ServerRequestInterface $request): int
    {
        $token = $this->resolveToken($request);

        return $this->authService->validateToken($token);
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

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = $this->getUserId($request);
        $data = (array)$request->getParsedBody();
        $input = $this->createChatInputMapper->map($data, $userId);
        $result = $this->chatService->createChat($input);

        return JsonResponder::respond($response, $result, 201);
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $userId = $this->getUserId($request);
        $result = $this->chatService->getChatWithMessages($id, $userId);

        return JsonResponder::respond($response, $result);
    }

    public function addMessage(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $userId = $this->getUserId($request);
        $data = (array)$request->getParsedBody();
        $input = $this->addChatMessageInputMapper->map($data, $id, $userId);
        $result = $this->chatService->addMessage($input);

        return JsonResponder::respond($response, $result, 201);
    }
    
    public function listModels(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Fetch available models from OVH AI API
        try {
            $models = $this->chatService->getAvailableModels();
            return JsonResponder::respond($response, $models);
        } catch (\Throwable $e) {
            // Fallback to static list if API fails
            $models = [
                [
                    'id' => 'Qwen3-Coder-30B-A3B-Instruct',
                    'name' => 'Qwen3-Coder-30B-A3B-Instruct',
                    'description' => 'Advanced coding model optimized for code generation and debugging',
                    'category' => 'code',
                ],
                [
                    'id' => 'Qwen3-32B',
                    'name' => 'Qwen3-32B',
                    'description' => 'General purpose large language model',
                    'category' => 'general',
                ],
                [
                    'id' => 'Mistral-7B-Instruct-v0.3',
                    'name' => 'Mistral-7B-Instruct-v0.3',
                    'description' => 'Balanced model for general tasks',
                    'category' => 'general',
                ],
            ];
            return JsonResponder::respond($response, $models);
        }
    }
}
