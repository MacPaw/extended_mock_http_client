<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\Request;

use ExtendedMockHttpClient\Excpetion\NotFountSuitableFixtureException;
use ExtendedMockHttpClient\HttpFixture\MockRequest;
use ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\AbstractExtendedMockHttpClientTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class ResponseTest extends AbstractExtendedMockHttpClientTestCase
{
    public function testCode(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test');

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testCodeAndBody(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->response(200, 'test body')
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test');

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('test body', $response->getContent());
    }

    public function testCodeAndBodyAndHeaders(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->response(200, 'test body', [
                'x-header' => 'x-value'
            ])
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test');

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('test body', $response->getContent());
        self::assertEquals([ 'x-header' => [ 'x-value' ]], $response->getHeaders());
    }

    public function testMockResponse(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->response(new MockResponse('test body', [
                'http_code' => 200,
                'response_headers' => [
                    'x-header' => 'x-value',
                ]
            ]))
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test');


        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('test body', $response->getContent());
        self::assertEquals([ 'x-header' => [ 'x-value' ]], $response->getHeaders());
    }

    public function testCallback(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->response(function (
                string $method,
                string $url,
                string $query,
                string $body,
                array $headers
            ): MockResponse {
                return new MockResponse('test body', [
                    'http_code' => 200,
                    'response_headers' => [
                        'x-header' => 'x-value',
                        'x-method' => $method,
                    ]
                ]);
            })
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test');

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('test body', $response->getContent());
        self::assertEquals([
            'x-header' => [ 'x-value' ],
            'x-method' => [ 'GET' ],
        ], $response->getHeaders());
    }
}
