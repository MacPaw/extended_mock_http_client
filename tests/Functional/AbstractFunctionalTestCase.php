<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional;

use ExtendedMockHttpClient\ExtendedMockHttpClient;
use ExtendedMockHttpClient\Factory\HttpFixtureFactory;

abstract class AbstractFunctionalTestCase extends AbstractKernelTestCase
{
    /**
     * @var HttpFixtureFactory
     */
    protected $httpFixtureFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpFixtureFactory = self::getContainerService(HttpFixtureFactory::class);
    }

    public function createHttpClient(string $baseUri): ExtendedMockHttpClient
    {
        return new ExtendedMockHttpClient($baseUri, $this->httpFixtureFactory);
    }
}
