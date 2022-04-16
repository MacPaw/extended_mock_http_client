<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\Request;

use ExtendedMockHttpClient\Excpetion\NotFountSuitableFixtureException;
use ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\AbstractExtendedMockHttpClientTestCase;

class HeadersTest extends AbstractExtendedMockHttpClientTestCase
{
    public function testArray(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->headers(['x-header' => 'x-value']))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test/foo?foo=bar', [
            'headers' => [
                'x-header' => 'x-value',
                'Content-Type' => 'application/json'
            ]
        ]);

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testNestedRequestItem(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->headers($builder->arrayContain(['x-header' => 'x-value'])))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test/foo?foo=bar', [
            'headers' => [
                'x-header' => 'x-value',
                'Content-Type' => 'application/json'
            ]
        ]);

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testCallback(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->headers($builder->callback(function (array $headers): bool {
                return isset($headers['x-header']);
            })))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test/foo?foo=bar', [
            'headers' => [
                'x-header' => 'x-value',
                'Content-Type' => 'application/json'
            ]
        ]);

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testFail(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->headers(['x-header' => 'x-value']))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $this->expectException(NotFountSuitableFixtureException::class);

        $this->client->request('GET', 'https://test.test/foo?foo=bar', [
            'headers' => [
                'y-header' => 'y-value',
                'Content-Type' => 'application/json'
            ]
        ]);
    }
}
