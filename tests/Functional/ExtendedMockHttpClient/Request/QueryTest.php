<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\Request;

use ExtendedMockHttpClient\Excpetion\NotFountSuitableFixtureException;
use ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\AbstractExtendedMockHttpClientTestCase;

class QueryTest extends AbstractExtendedMockHttpClientTestCase
{
    public function testString(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->query('foo=bar'))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test?foo=bar');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testArray(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->query(['foo' => 'bar']))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test?foo=bar');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testNestedRequestItem(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->query($builder->stringRegex('/foo/')))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test?foo=bar');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testQueryToArray(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->query($builder->queryToArray(
                $builder->arrayContain([
                    'foo' => 'bar'
                ])
            )))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test?foo=bar&baz=123');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testCallback(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->query($builder->callback(function (string $query): bool {
                return $query === 'foo=bar&baz=123';
            })))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test?foo=bar&baz=123');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testFail(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->query('foo=bar'))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $this->expectException(NotFountSuitableFixtureException::class);

        $this->client->request('GET', 'https://test.test?baz=123');
    }
}
