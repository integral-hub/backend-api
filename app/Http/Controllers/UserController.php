<?php

namespace App\Http\Controllers;

use App\Enums\Http;
use App\Interfaces\UserInterface;
use App\Responses\ApiResponse;
use App\Responses\SuccessResponse;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct(
        private readonly UserInterface $userService,
    ){}

    /**
     * Show user info and cat fact.
     */
    public function show(): ApiResponse
    {
        $payload = $this->userService->getUserWithFact();

        Log::info('response generated', [
            'email' => $payload['user']['email'],
            'fact'  => $payload['fact'],
        ]);

        return SuccessResponse::make($payload);
    }
}
