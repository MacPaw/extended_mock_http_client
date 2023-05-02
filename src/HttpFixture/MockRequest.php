<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture;

use ExtendedMockHttpClient\Enum\RequestKey;
use ExtendedMockHttpClient\Excpetion\MockRequest\InvalidFixtureItemKeyException;
use ExtendedMockHttpClient\HttpFixture\Request\HttpFixtureRequestItemInterface;

class MockRequest
{
    /**
     * @var array<string, HttpFixtureRequestItemInterface[]>
     */
    private $fixtureItems;

    public function __construct(array $fixtureItems)
    {
        $this->fixtureItems = $fixtureItems;
    }

    /**
     * @param array<string, string> $headers
     *
     * @throws InvalidFixtureItemKeyException
     */
    public function isSuitable(string $method, string $url, string $query, string $body, array $headers): bool
    {
        foreach ($this->fixtureItems as $key => $items) {
            if (RequestKey::tryFrom($key) === null) {
                throw new InvalidFixtureItemKeyException(RequestKey::values(), $key);
            }

            $requestKey = RequestKey::tryFrom($key);

            if ($requestKey->isMethod()) {
                $value = $method;
            } elseif ($requestKey->isUrl()) {
                $value = $url;
            } elseif ($requestKey->isQuery()) {
                $value = $query;
            } elseif ($requestKey->isBody()) {
                $value = $body;
            } else {
                $value = $headers;
            }

            foreach ($items as $item) {
                if (!$item->__invoke($value)) {
                    return false;
                }
            }
        }

        return true;
    }
}
