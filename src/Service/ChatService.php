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
        $id = $this->chatRepository->create($input->userId, $input->title, $input->model);

        $this->logger->info('Chat created', ['chat_id' => $id, 'user_id' => $input->userId]);

        return new ChatResult(
            id: $id,
            userId: $input->userId,
            title: $input->title,
            createdAt: date('Y-m-d H:i:s'),
            model: $input->model,
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
                model: $row['model'] ?? null,
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
            model: $chat['model'] ?? null,
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

        $history = $this->chatRepository->findMessagesByChatId($input->chatId);

        $messageHistory = [];
        foreach ($history as $msg) {
            $messageHistory[] = [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        // Add user message to history for AI
        $messageHistory[] = [
            'role' => 'user',
            'content' => $input->content,
        ];

        $userMessageId = $this->chatRepository->addMessage(
            $input->chatId,
            'user',
            $input->content,
        );
        
        $userMessage = [
            'id' => $userMessageId,
            'chat_id' => $input->chatId,
            'role' => 'user',
            'content' => $input->content,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Add assistant message
        try {
            $aiContent = $this->ovhAiClient->generate($messageHistory, $chat['model'] ?? null);
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

        // Return both messages
        return [$userMessage, $assistantMessage];
    }

    public function getAvailableModels(): array
    {
        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => $this->ovhAiClient->getBaseUrl(),
                'timeout' => 30,
            ]);
            $response = $client->get('models');
            $data = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            
            // Filter and format models
            $models = [];
            foreach ($data['data'] as $model) {
                // Skip non-text models (embeddings, image generation, etc.)
                if (str_contains($model['id'], 'Embedding') || 
                    str_contains($model['id'], 'stable-diffusion') ||
                    str_contains($model['id'], 'bge-') ||
                    $model['id'] === 'ppl') {
                    continue;
                }
                
                $models[] = [
                    'id' => $model['id'],
                    'name' => $model['id'],
                    'description' => $this->getModelDescription($model['id']),
                    'category' => $this->getModelCategory($model['id']),
                    'context_length' => $model['context_length'] ?? 0,
                    'max_completion_tokens' => $model['max_completion_tokens'] ?? 0,
                ];
            }
            
            return $models;
        } catch (\Throwable $e) {
            $this->logger->error('Failed to fetch models from OVH AI API', ['exception' => $e]);
            throw $e;
        }
    }

    private function getModelDescription(string $modelId): string
    {
        $descriptions = [
            'Qwen3-Coder-30B-A3B-Instruct' => 'Advanced coding model optimized for code generation and debugging',
            'Qwen3-32B' => 'General purpose large language model',
            'Qwen3.5-397B-A17B' => 'Large reasoning model for complex tasks',
            'Qwen3.6-27B' => 'Visual language model with multimodal capabilities',
            'Qwen3.5-9B' => 'Efficient model for general tasks',
            'Mistral-7B-Instruct-v0.3' => 'Balanced model for general tasks',
            'Mistral-Small-3.2-24B-Instruct-2506' => 'Latest Mistral model with improved performance',
            'Mistral-Nemo-Instruct-2407' => 'Mistral Nemo model for general tasks',
            'Meta-Llama-3_3-70B-Instruct' => 'Meta Llama 3.3 70B model for general tasks',
            'Llama-3.1-8B-Instruct' => 'Efficient Llama model for general tasks',
            'gpt-oss-20b' => 'OpenAI-style 20B model for general tasks',
            'gpt-oss-120b' => 'Large OpenAI-style 120B model for complex tasks',
            'Qwen2.5-VL-72B-Instruct' => 'Visual language model with image understanding',
            'whisper-large-v3' => 'Speech to text model',
            'whisper-large-v3-turbo' => 'Fast speech to text model',
        ];
        
        return $descriptions[$modelId] ?? 'AI model for various tasks';
    }

    private function getModelCategory(string $modelId): string
    {
        if (str_contains($modelId, 'Coder')) {
            return 'code';
        }
        if (str_contains($modelId, 'VL') || str_contains($modelId, 'Visual')) {
            return 'visual';
        }
        if (str_contains($modelId, 'whisper')) {
            return 'audio';
        }
        if (str_contains($modelId, 'Guard')) {
            return 'guard';
        }
        return 'general';
    }
}
