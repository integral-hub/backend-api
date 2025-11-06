<?php

namespace App\Services;

use App\Interfaces\SayItNicerInterface;
use OpenAI\Laravel\Facades\OpenAI;

class SayItNicerService implements SayItNicerInterface
{
    /**
     * System instructions (training data) that define how the agent behaves.
     * You can expand this as your “training personality”.
     */
    protected string $trainingPrompt = <<<PROMPT
You are "Say-It-Nicer" — a kind, emotionally intelligent communication assistant.

Your job:
- Detect if a message sounds harsh, rude, or overly direct.
- If it does, rephrase it into a polite, professional, and empathetic tone.
- If the message is already polite, return the exact same message unchanged.
- You may add a brief, friendly acknowledgment like:
  "😊 That already sounds kind and well-written!" — but only if it’s clearly already nice.

Tone rules:
1. Maintain the user's intent and factual meaning.
2. Never exaggerate praise or change facts.
3. Avoid robotic or overly formal phrasing.
4. When rephrasing, balance warmth with clarity.
5. Output only the final rewritten text (no explanations).
PROMPT;

    /**
     * Main function to process and rephrase text.
     */
    public function rephrase(string $text): string
    {
        // Step 1: Pass the training prompt and user input
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini', // You can swap with gpt-4-turbo or another model
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->trainingPrompt
                ],
                [
                    'role' => 'user',
                    'content' => "Input message: {$text}"
                ],
            ],
            'temperature' => 0.6, // balanced creativity
        ]);

        $output = trim($response['choices'][0]['message']['content'] ?? '');

        return $output ?: '⚠️ Sorry, I couldn’t process that message.';
    }
}
