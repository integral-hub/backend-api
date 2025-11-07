<?php

namespace App\Http\Controllers;

use App\Interfaces\SayItNicerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SayItNicerController extends Controller
{
    public function __construct(
        private readonly SayItNicerInterface $sinService
    ) {}

/**
 * Rephrase a message into a nicer tone.
 *
 * Accepts a full Telex JSON-RPC A2A request.
 *
 * @group SayItNicer
 *
 * @bodyParam params.message.parts.0.type string required The type of the first message part. e.g.: "text"
 * @bodyParam params.message.parts.0.text string required The text of the first message part. Example: "This is too harsh!"
 *
 * @response 200 {
 *   "jsonrpc": "2.0",
 *   "id": "rpcid123",
 *   "result": {
 *     "role": "agent",
 *     "parts": [
 *       {
 *         "type": "text",
 *         "text": "Here’s a kinder way to phrase that: ..."
 *       }
 *     ],
 *     "kind": "message",
 *     "message_id": "5f2c1e3e7c3b5"
 *   }
 * }
 *
 * @response 400 {
 *   "jsonrpc": "2.0",
 *   "id": "rpcid123",
 *   "error": {
 *     "code": -32602,
 *     "message": "Invalid params: text missing"
 *   }
 * }
 */
public function rephrase(Request $request): JsonResponse
{
    $body = $request->json()->all();
    $rpcId = $body['id'] ?? null;

    $messageParts = $body['params']['message']['parts'] ?? [];
    $text = '';
foreach ($messageParts as $part) {
    if (($part['kind'] ?? '') === 'data' && isset($part['data']) && is_array($part['data'])) {
        foreach ($part['data'] as $dataItem) {
            if (($dataItem['kind'] ?? '') === 'text' && isset($dataItem['text'])) {
                $text = $dataItem['text']; // overwrite with last found text
            }
        }
    }
}

    if (empty($text)) {
        return response()->json([
            "jsonrpc" => "2.0",
            "id" => $rpcId,
            "error" => [
                "code" => -32602,
                "message" => "Invalid params: text missing"
            ]
        ], 400);
    }

    $rewritten = $this->sinService->rephrase($text);

    return response()->json([
        "jsonrpc" => "2.0",
        "id" => $rpcId,
        "result" => [
            "role" => "agent",
            "parts" => [
                [
                    "type" => "text",
                    "text" => $rewritten
                ]
            ],
            "kind" => "message",
            "message_id" => uniqid()
        ]
    ]);
}


    /**
     * Agent Card
     * @group SayItNicer
     * Agent card for Telex.
     */
    public function agentCard(): JsonResponse
    {
        $url = config('app.url');

        return response()->json([
            "name" => "SayItNicer",
            "description" => "Polishes messages into a kind and professional tone.",
            "url" => $url. '/api/agent',
            "provider" => [
                "organization" => "Techtrovelab",
                "url" => $url
            ],
            "version" => "1.0.0",
            "documentationUrl" => $url . '/docs',
            "capabilities" => [
                "streaming" => false,
                "pushNotifications" => false,
                "stateTransitionHistory" => false
            ],
            "defaultInputModes" => ["text/plain"],
            "defaultOutputModes" => ["text/plain", "application/json"],
            "skills" => [
                [
                    "id" => uniqid(),
                    "name" => "Polish Text Tone",
                    "description" => "Detects harsh or blunt messages and rephrases them politely.",
                    "inputModes" => ["text/plain"],
                    "outputModes" => ["text/plain", "application/json"],
                    "examples" => [
                        [
                            "input" => "This is too harsh!",
                            "output" => "Here’s a kinder way to phrase that: ..."
                        ],
                        [
                            "input" => "Hello, how are you?",
                            "output" => "That already sounds kind and well-written!"
                        ]
                    ]
                ]
            ],
            "supportsAuthenticatedExtendedCard" => false
        ]);
    }
}
