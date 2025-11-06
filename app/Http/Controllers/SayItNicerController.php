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

    public function rephrase(Request $request)
    {
        $text = $request->input('text') ?? '';
        if (!$text) return;

        $result = $this->sinService->rephrase($text['text']);

        return response()->json([
            'active' => true,
            'category' => 'utilities',
            'name' => 'say_it_nicer_agent',
            'description' => 'Improves message tone to be kind and professional.',
            'short_description' => 'Polishes text tone',
            'data' => [
                'input' => $result['original'],
                'output' => $result['message'],
            ],
        ]);
    }
}
