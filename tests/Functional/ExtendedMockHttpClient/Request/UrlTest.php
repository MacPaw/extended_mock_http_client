<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\Request;

use ExtendedMockHttpClient\Excpetion\NotFountSuitableFixtureException;
use ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\AbstractExtendedMockHttpClientTestCase;

class UrlTest extends AbstractExtendedMockHttpClientTestCase
{
    public function testString(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->url('https://test.test/test'))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test/test?foo=bar');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testArray(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->url(['https://test.test/foo', 'https://test.test/bar']))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test/foo?foo=bar');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testNestedRequestItem(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->url($builder->stringRegex('/test\.test/')))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test/test?foo=bar');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testCallback(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->url($builder->callback(function (string $url): bool {
                return $url === 'https://test.test/test';
            })))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test/test?foo=bar');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testUrlToArray(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->url(
                $builder->urlToArray(
                    $builder->arrayContain([
                        'scheme' => 'https',
                        'host' => 'test.test',
                        'path' => '/foo'
                    ])
                )
            ))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test/foo?foo=bar');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testFail(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->url('https://test.test/test'))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $this->expectException(NotFountSuitableFixtureException::class);

        $this->client->request('GET', 'https://test.test/wrong');
    }
}
