<?php

namespace App\Services;

use App\Interfaces\UserInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserService implements UserInterface
{
    /**
     * Fetch user data and cat fact.
     */
    public function getUserWithFact(): array
    {
        // Try to get user data from DB
        $user = $this->getUserData();

        // Fetch cat fact with graceful fallback
        $fact = $this->getCatFact();

        // Add timestamp
        $timestamp = Carbon::now('UTC')->toIso8601String();

        return [
            'status' => 'success',
            'user' => $user,
            'timestamp' => $timestamp,
            'fact' => $fact,
        ];
    }

    /**
     * Try to get user data from DB; fallback to static.
     */
    private function getUserData(): User|array
    {
        $user = User::first();

        if ($user) {
            return $user;
        }

        // Fallback data
        return [
            'email' => 'aeadeosun@yahoo.com',
            'name'  => 'Abiodun Adeosun',
            'stack' => 'LAMP',
        ];
    }

    /**
     * Handle Cat Facts API with timeout and fallback.
     */
    private function getCatFact(): string
    {
        $url = env('CAT_FACTS_URL', 'https://catfact.ninja/fact');
        $timeout = intval(env('CAT_FACTS_TIMEOUT', 10));

        try {
            $response = Http::timeout($timeout)->get($url);

            if ($response->successful() && isset($response->json()['fact'])) {
                return $response->json()['fact'];
            }

            Log::warning('Unexpected Cat Facts response', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);
        } catch (\Throwable $e) {
            Log::error('Cat Facts API request failed', [
                'error' => $e->getMessage(),
            ]);
        }

        return 'Could not fetch a cat fact at this time.';
    }
}
