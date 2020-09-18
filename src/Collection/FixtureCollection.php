<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Collection;

use ExtendedMockHttpClient\Model\HttpFixture;
use Symfony\Component\Cache\ResettableInterface;

class FixtureCollection implements ResettableInterface
{
    /**
     * @var HttpFixture[]
     */
    private $fixtures = [];

    public function __construct(array $fixtures = [])
    {
        foreach ($fixtures as $fixture) {
            $this->addFixture($fixture);
        }
    }

    public function addFixture(HttpFixture $fixture): void
    {
        $this->fixtures[] = $fixture;
    }

    public function findSuitableFixture(string $method, string $url, string $body): ?HttpFixture
    {
        foreach ($this->fixtures as $fixture) {
            if ($fixture->isSuitable($method, $url, $body)) {
                return $fixture;
            }
        }

        return null;
    }

    public function reset(): void
    {
        $this->fixtures = [];
    }
}
