<?php

namespace App\Tests\ExtendedMockHttpClient;

class FixtureCollection
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

    public function findSuitableFixture(string $method, string $url): ?HttpFixture
    {
        foreach ($this->fixtures as $fixture) {
            if ($fixture->isSuitable($method, $url)) {
                return $fixture;
            }
        }

        return null;
    }
}
