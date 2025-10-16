<?php

declare(strict_types=1);

namespace App\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Responsable;
use App\Enums\Http;

abstract class ApiResponse implements Responsable
{
    protected Http $statusCode = Http::OK;
    protected array $headers = [];

    abstract protected function payload(): array;

    public function toResponse($request): JsonResponse
    {
        return response()->json(
            data: $this->payload(),
            status: $this->statusCode->value,
            headers: $this->headers
        );
    }

    protected function withStatus(Http $status): self
    {
        $this->statusCode = $status;
        return $this;
    }

    protected function withHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }
}
