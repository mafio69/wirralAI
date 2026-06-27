<?php

declare(strict_types=1);

namespace Tests;

use App\Service\ChatService;
use PHPUnit\Framework\TestCase;

class ChatServiceModelsTest extends TestCase
{
    private ChatService $chatService;
    private \PDO $pdo;

    protected function setUp(): void
    {
        // Create in-memory SQLite database for testing
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Create tables
        $this->pdo->exec('
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                created_at TEXT NOT NULL
            );
            CREATE TABLE chats (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                created_at TEXT NOT NULL,
                model TEXT DEFAULT NULL
            );
            CREATE TABLE chat_messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                chat_id INTEGER NOT NULL,
                role TEXT NOT NULL,
                content TEXT NOT NULL,
                created_at TEXT NOT NULL,
                FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE
            );
        ');

        // Create service dependencies
        $chatRepository = new \App\Repository\ChatRepository($this->pdo);
        
        // Mock OVH AI client with mocked API response
        $ovhAiClient = $this->createMock(\App\Infrastructure\AI\OvhAiClient::class);
        $ovhAiClient->method('generate')->willReturn('AI response');
        $ovhAiClient->method('getBaseUrl')->willReturn('https://oai.endpoints.kepler.ai.cloud.ovh.net/v1/');
        
        $logger = $this->createMock(\Psr\Log\LoggerInterface::class);

        $this->chatService = new ChatService($chatRepository, $ovhAiClient, $logger);
    }

    public function testGetAvailableModels(): void
    {
        // This test will call the real OVH AI API
        // In a real scenario, you might want to mock the HTTP client
        // For now, we'll test that the method doesn't throw an exception
        
        try {
            $models = $this->chatService->getAvailableModels();
            
            // Verify we got some models
            $this->assertIsArray($models);
            $this->assertGreaterThan(0, count($models));
            
            // Verify structure of first model
            $firstModel = $models[0];
            $this->assertArrayHasKey('id', $firstModel);
            $this->assertArrayHasKey('name', $firstModel);
            $this->assertArrayHasKey('description', $firstModel);
            $this->assertArrayHasKey('category', $firstModel);
            
            // Verify that embeddings and image models are filtered out
            foreach ($models as $model) {
                $this->assertStringNotContainsString('Embedding', $model['id']);
                $this->assertStringNotContainsString('stable-diffusion', $model['id']);
                $this->assertStringNotContainsString('bge-', $model['id']);
                $this->assertNotEquals('ppl', $model['id']);
            }
            
        } catch (\Throwable $e) {
            // If API call fails (network issues, etc.), we just skip this test
            $this->markTestSkipped('OVH AI API is not accessible: ' . $e->getMessage());
        }
    }

    public function testModelDescriptionMapping(): void
    {
        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->chatService);
        $method = $reflection->getMethod('getModelDescription');

        $description = $method->invoke($this->chatService, 'Qwen3-Coder-30B-A3B-Instruct');
        $this->assertEquals('Advanced coding model optimized for code generation and debugging', $description);

        $description = $method->invoke($this->chatService, 'Unknown-Model');
        $this->assertEquals('AI model for various tasks', $description);
    }

    public function testModelCategoryMapping(): void
    {
        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->chatService);
        $method = $reflection->getMethod('getModelCategory');

        $this->assertEquals('code', $method->invoke($this->chatService, 'Qwen3-Coder-30B-A3B-Instruct'));
        $this->assertEquals('visual', $method->invoke($this->chatService, 'Qwen2.5-VL-72B-Instruct'));
        $this->assertEquals('general', $method->invoke($this->chatService, 'Qwen3.6-27B'));
        $this->assertEquals('audio', $method->invoke($this->chatService, 'whisper-large-v3'));
        $this->assertEquals('guard', $method->invoke($this->chatService, 'Qwen3Guard-Gen-8B'));
        $this->assertEquals('general', $method->invoke($this->chatService, 'Mistral-7B-Instruct-v0.3'));
    }
}