<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient;

use ExtendedMockHttpClient\ExtendedMockHttpClient;
use ExtendedMockHttpClient\Tests\Functional\AbstractFunctionalTestCase;

class AbstractExtendedMockHttpClientTestCase extends AbstractFunctionalTestCase
{
    /**
     * @var ExtendedMockHttpClient
     */
    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = self::getContainerService(ExtendedMockHttpClient::class);
    }
}
