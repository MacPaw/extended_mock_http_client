<?php

namespace ExtendedMockHttpClient\Excpetion;

class NotFountSuitableFixtureException extends ExtendedMockHttpClientException
{
    public static function fromRequestParameters(string $method, string $url, array $options): self
    {
        return new self(sprintf("Not found suitable fixture with parameters: \n%s", self::arrayParametersToString([
            $method,
            $url,
            $options['body'],
        ])), 500);
    }
}
