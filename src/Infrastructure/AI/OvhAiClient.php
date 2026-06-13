<?php

declare(strict_types=1);

namespace App\Infrastructure\AI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

final readonly class OvhAiClient
{
    private Client $httpClient;

    public function __construct(
        private string $url,
        private string $token,
        private string $model,
    ) {
        $this->httpClient = new Client([
            'base_uri' => $url,
            'timeout' => 120,
        ]);
    }

    public function generate(array $messages): string
    {
        try {
            $response = $this->httpClient->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => $messages,
                ],
            ]);

            $body = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);

            return $body['choices'][0]['message']['content'] ?? '';
        } catch (GuzzleException $e) {
            throw new RuntimeException('AI request failed: '.$e->getMessage(), 0, $e);
        }
    }
}
