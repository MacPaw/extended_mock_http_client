<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\Builder\HttpFixtureBuilder;

use ExtendedMockHttpClient\Builder\HttpFixtureBuilder;
use ExtendedMockHttpClient\ExtendedMockHttpClient;
use ExtendedMockHttpClient\Tests\Functional\AbstractFunctionalTestCase;

abstract class AbstractHttpFixtureBuilderTest extends AbstractFunctionalTestCase
{
    /**
     * @var ExtendedMockHttpClient
     */
    protected $extendedMockHttpClient;

    public function setUp(): void
    {
        parent::setUp();

        $this->extendedMockHttpClient = self::getContainer()->get(ExtendedMockHttpClient::class);
    }

    protected function getHttpFixtureBuilder(): HttpFixtureBuilder
    {
        return $this->extendedMockHttpClient->getHttpFixtureBuilder();
    }
}
