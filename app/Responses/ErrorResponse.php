<?php

declare(strict_types=1);

namespace App\Responses;

use App\Enums\Http;

class ErrorResponse extends ApiResponse
{
    public function __construct(
        private string $message,
        private ?string $errorCode = null
    ) {}

    public static function make(
        string $message,
        Http $status = Http::BAD_REQUEST,
        ?string $errorCode = null,
        array $headers = []
    ): self {
        return (new self($message, $errorCode))
            ->withStatus($status)
            ->withHeaders($headers);
    }

    protected function payload(): array
    {
        return array_filter([
            'status' => 'error',
            'message' => $this->message,
            'error_code' => $this->errorCode,
        ]);
    }
}
