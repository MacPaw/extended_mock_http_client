<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Excpetion;

class NotFountSuitableFixtureException extends ExtendedMockHttpClientParameterizedException
{
    public static function fromRequestParameters(
        string $method,
        string $url,
        string $query,
        string $body,
        array $headers
    ): self {
        return new self('Not found suitable fixture', [
            'method' => $method,
            'url' => $url,
            'query' => $query,
            'body' => $body,
            'headers' => $headers,
        ], 500);
    }
}
