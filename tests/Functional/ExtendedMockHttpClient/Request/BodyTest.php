<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\Request;

use ExtendedMockHttpClient\Excpetion\NotFountSuitableFixtureException;
use ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\AbstractExtendedMockHttpClientTestCase;

class BodyTest extends AbstractExtendedMockHttpClientTestCase
{
    public function testString(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->body('test body'))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('POST', 'https://test.test', [
            'body' => 'test body'
        ]);

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testArray(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->body(['test body 1', 'test body 2']))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('POST', 'https://test.test', [
            'body' => 'test body 1'
        ]);

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testNestedRequestItem(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->body($builder->stringRegex('/body/')))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('POST', 'https://test.test', [
            'body' => 'test body regex'
        ]);

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testJson(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->body($builder->jsonToArray(
                $builder->arrayContain([
                    'foo' => 'bar'
                ])
            )))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('POST', 'https://test.test', [
            'body' => '{"foo": "bar", "baz": 123}'
        ]);

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testCallback(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->body($builder->callback(function (string $body): bool {
                return $body === 'test body';
            })))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('POST', 'https://test.test', [
            'body' => 'test body'
        ]);

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testFail(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();
        $httpFixture = $builder
            ->request($builder->body('test body'))
            ->response(200)
            ->build();
        $this->client->addFixture($httpFixture);

        $this->expectException(NotFountSuitableFixtureException::class);

        $this->client->request('POST', 'https://test.test', [
            'body' => 'invalid body'
        ]);
    }
}
