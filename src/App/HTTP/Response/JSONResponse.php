<?php

declare(strict_types=1);

namespace App\HTTP\Response;

use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use React\Http\Message\Response;

class JSONResponse implements ResponseInterface
{
    private array $data;
    private int $statusCode;
    private array $headers;

    public function __construct(array $data, int $statusCode = 200)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->headers = [
            'Content-Type' => 'application/json'
        ];
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function writeHeader(string $key, mixed $value): ResponseInterface
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function getContent(): string
    {
        return json_encode($this->data, JSON_THROW_ON_ERROR);
    }

    public function toHttpResponse(): Response
    {
        return new Response(
            $this->statusCode,
            $this->headers,
            $this->getContent()
        );
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getCode(): int
    {
        return $this->statusCode;
    }

    public function getHeader(): array
    {
        return $this->headers;
    }
}
