<?php

declare(strict_types=1);

namespace App\Responses;

use App\Enums\Http;

class SuccessResponse extends ApiResponse
{
    public function __construct(
        private array $data
    ) {}

    public static function make(
        array $data,
        Http $status = Http::OK,
        array $headers = []
    ): self {
        return (new self($data))
            ->withStatus($status)
            ->withHeaders($headers);
    }

    protected function payload(): array
    {
        // Return raw data as-is
        return $this->data;
    }
}
