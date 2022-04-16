<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional;

use ExtendedMockHttpClient\ExtendedMockHttpClient;
use ExtendedMockHttpClient\Factory\HttpFixtureFactory;

abstract class AbstractFunctionalTestCase extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function createHttpClient(string $baseUri): ExtendedMockHttpClient
    {
        return new ExtendedMockHttpClient($baseUri, self::getContainer()->get(HttpFixtureFactory::class));
    }
}
