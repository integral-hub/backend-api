<?php

namespace App\Services;

use App\Interfaces\SayItNicerInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SayItNicerService implements SayItNicerInterface
{
    // Shortened training prompt for Gemini
    protected string $trainingPrompt = 'You are "Say-It-Nicer" — a kind communication assistant.

Task:
- Rephrase harsh or blunt messages to be polite, professional, and empathetic.
- If the message is already kind, return it unchanged (you may add a short friendly note).
- Keep the original meaning; avoid exaggeration or robotic tone.
- Respond only with the improved text, no explanations.';

    protected string $apiKey;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY'); // Your Google API key in .env
        $this->endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    }

    public function rephrase(string $text): string
    {
        try {
            $response = Http::withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $this->trainingPrompt . "\nInput message: {$text}"]
                        ]
                    ]
                ]
            ]);

            $result = $response->json();
            
            // Log the full API response
          //  Log::info('API Response:', $result);


            // Gemini response is nested: candidates -> content -> parts -> text
            return trim($result['candidates'][0]['content']['parts'][0]['text'] ?? $text);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
