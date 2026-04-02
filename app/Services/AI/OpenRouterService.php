<?php

namespace App\Services\AI;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class OpenRouterService
{
    /**
     * @throws RequestException
     */
    public function ask(string $prompt): array
    {
        $baseUrl = rtrim((string) config('services.openrouter.base_url', ''), '/');
        $apiKey = (string) config('services.openrouter.api_key', '');
        $model = (string) config('services.openrouter.model', 'google/gemini-2.5-flash');

        if ($baseUrl === '' || $apiKey === '') {
            return [
                'answer' => 'OpenRouter is not configured yet.',
                'raw' => null,
            ];
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->post("{$baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a BaZi consultant.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ])
            ->throw()
            ->json();

        $content = data_get($response, 'choices.0.message.content', '');

        return [
            'answer' => is_string($content) ? $content : json_encode($content),
            'raw' => $response,
        ];
    }
}
