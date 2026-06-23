<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AiService
{
    private string $apiUrl;
    private string $apiKey;
    private string $model;
    private int $maxTokens;
    private float $temperature;

    public function __construct()
    {
        $this->apiUrl = config('ai.api_url');
        $this->apiKey = config('ai.api_key');
        $this->model = config('ai.model');
        $this->maxTokens = (int) config('ai.max_tokens');
        $this->temperature = (float) config('ai.temperature');
    }

    /**
     * Send a chat request to the LiteLLM proxy.
     */
    public function chat(array $messages, ?float $temperature = null): ?string
    {
        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'x-litellm-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => $messages,
                    'max_tokens' => $this->maxTokens,
                    'temperature' => $temperature ?? $this->temperature,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            Log::error('AI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('AI API exception', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Build an image content part from a storage file path.
     */
    public function buildImageContent(string $storagePath): ?array
    {
        try {
            $fullPath = Storage::disk('public')->path($storagePath);

            if (!file_exists($fullPath)) {
                Log::warning('AI: Image file not found', ['path' => $fullPath]);
                return null;
            }

            $content = file_get_contents($fullPath);
            $mime = mime_content_type($fullPath) ?: 'image/jpeg';
            $base64 = base64_encode($content);

            return [
                'type' => 'image_url',
                'image_url' => [
                    'url' => "data:{$mime};base64,{$base64}",
                ],
            ];
        } catch (\Exception $e) {
            Log::error('AI: Failed to build image content', [
                'path' => $storagePath,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Build a video content part from a storage file path.
     * Note: Only first few seconds are typically analyzed by the model.
     */
    public function buildVideoContent(string $storagePath): ?array
    {
        try {
            $fullPath = Storage::disk('public')->path($storagePath);

            if (!file_exists($fullPath)) {
                Log::warning('AI: Video file not found', ['path' => $fullPath]);
                return null;
            }

            // Check file size — skip if > 20MB to avoid memory issues
            $fileSize = filesize($fullPath);
            if ($fileSize > 20 * 1024 * 1024) {
                Log::info('AI: Video too large, skipping', ['size' => $fileSize]);
                return null;
            }

            $content = file_get_contents($fullPath);
            $mime = mime_content_type($fullPath) ?: 'video/mp4';
            $base64 = base64_encode($content);

            return [
                'type' => 'image_url',
                'image_url' => [
                    'url' => "data:{$mime};base64,{$base64}",
                ],
            ];
        } catch (\Exception $e) {
            Log::error('AI: Failed to build video content', [
                'path' => $storagePath,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
