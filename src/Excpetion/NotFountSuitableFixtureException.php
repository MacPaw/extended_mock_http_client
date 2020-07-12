<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Excpetion;

class NotFountSuitableFixtureException extends ExtendedMockHttpClientParameterizedException
{
    public static function fromRequestParameters(string $method, string $url, array $options): self
    {
        return new self('Not found suitable fixture', [
            'method' => $method,
            'url' => $url,
            'body' => $options['body'],
        ], 500);
    }
}
