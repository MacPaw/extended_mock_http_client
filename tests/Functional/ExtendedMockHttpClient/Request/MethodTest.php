<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\Request;

use ExtendedMockHttpClient\Excpetion\NotFountSuitableFixtureException;
use ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\AbstractExtendedMockHttpClientTestCase;

class MethodTest extends AbstractExtendedMockHttpClientTestCase
{
    public function testString(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->method('GET'))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testArray(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->method(['GET', 'POST']))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testNestedRequestItem(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->method($builder->stringRegex('/GET/')))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testFail(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->method('GET'))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $this->expectException(NotFountSuitableFixtureException::class);

        $this->client->request('POST', 'https://test.test');
    }
}
