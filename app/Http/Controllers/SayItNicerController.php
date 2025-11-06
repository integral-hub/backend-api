<?php

namespace App\Http\Controllers;

use App\Interfaces\SayItNicerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SayItNicerController extends Controller
{

    public function __construct(
        private readonly SayItNicerInterface $sinService
    ){}

/**
 * Rephrase a given message into a nicer tone.
 *
 * @group SayItNicer
 * 
 * @bodyParam params.message.parts.*.text string required The text to rephrase. Example: "This is too harsh!"
 * 
 * @response 200 {
 *   "jsonrpc": "2.0",
 *   "id": "1",
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
 *   "id": "1",
 *   "error": {
 *     "code": -32602,
 *     "message": "Invalid params: text missing"
 *   }
 * }
 */

public function rephrase(Request $request): JsonResponse
{
    $rpcId = $request->input('id');
    $messageParts = $request->input('params.message.parts', []);

    if (empty($messageParts) || empty($messageParts[0]['text'])) {
        return response()->json([
            "jsonrpc" => "2.0",
            "id" => $rpcId,
            "error" => [
                "code" => -32602,
                "message" => "Invalid params: text missing"
            ]
        ], 400);
    }

    $text = $messageParts[0]['text'];
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
            "message_id" => uniqid() // simple unique ID
        ]
    ]);
}

    /**
     * Return the agent card for Telex (.well-known/agent.json)
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
            "defaultOutputModes" => ["text/plain"],
            "skills" => [
                [
                    "id" => uniqid(),
                    "name" => "Polish Text Tone",
                    "description" => "Detects harsh or blunt messages and rephrases them politely.",
                    "inputModes" => ["text/plain"],
                    "outputModes" => ["text/plain"],
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
