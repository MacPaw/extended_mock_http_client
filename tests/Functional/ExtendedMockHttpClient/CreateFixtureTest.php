<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\Request;

use ExtendedMockHttpClient\Excpetion\NotFountSuitableFixtureException;
use ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\AbstractExtendedMockHttpClientTestCase;

class CreateFixtureTest extends AbstractExtendedMockHttpClientTestCase
{
    public function testEmpty(): void
    {
        $httpFixture = $this->client->createFixture();
        $this->client->addFixture($httpFixture);

        $this->client->request('GET', 'https://test.test/foo?foo=bar', [
            'headers' => [
                'x-header' => 'x-value',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->expectNotToPerformAssertions();
    }

    public function testWithAllArguments(): void
    {
        $httpFixture = $this->client->createFixture(
            'POST',
            'https://test.test/foo',
            '{"foo":"bar"}',
            [
                'x-header' => 'x-value',
            ],
            200,
            'ok'
        );
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('POST', 'https://test.test/foo?foo=bar', [
            'json' => [
                'foo' => 'bar'
            ],
            'headers' => [
                'x-header' => 'x-value',
                'Content-Type' => 'application/json'
            ]
        ]);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('ok', $response->getContent());
    }

    public function testFail(): void
    {
        $httpFixture = $this->client->createFixture(
            'POST',
            'https://test.test/foo',
            '{"foo":"bar"}',
            [
                'x-header' => 'x-value',
            ],
            200,
            'ok'
        );
        $this->client->addFixture($httpFixture);

        $this->expectException(NotFountSuitableFixtureException::class);

        $this->client->request('GET', 'https://test.test');
    }
}
