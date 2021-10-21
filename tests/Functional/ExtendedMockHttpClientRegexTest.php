<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional;

use ExtendedMockHttpClient\Builder\RequestMockBuilder;
use ExtendedMockHttpClient\Excpetion\NotFountSuitableFixtureException;
use ExtendedMockHttpClient\ExtendedMockHttpClient;
use ExtendedMockHttpClient\Model\HttpFixture;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class ExtendedMockHttpClientRegexTest extends TestCase
{
    public function testSuccessMethodRegexMethod(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->methodRegex('/(GET|POST)/')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('GET', 'http://test.test/foo/bar?qwe=rty');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function testFailMethodRegexMethod(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->methodRegex('/POST/')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $this->expectException(NotFountSuitableFixtureException::class);

        $client->request('GET', 'http://test.test/foo/bar?qwe=rty');
    }

    public function testSuccessUrlRegexMethod(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->urlRegex('/test.test\/foo\/bar/')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('GET', 'http://test.test/foo/bar?qwe=rty');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function testFailUrlRegexMethod(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->urlRegex('/qwerty/')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $this->expectException(NotFountSuitableFixtureException::class);

        $client->request('GET', 'http://test.test/foo/bar?qwe=rty');
    }

    public function testSuccessBodyRegexMethod(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->bodyRegex('/foobar/')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'body' => 'foobar body text',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function testSuccessBodyAsJsonRegexMethod(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->bodyRegex('/"foo":.*/')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'json' => [
                'foo' => 'bar',
            ],
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function testFailBodyRegexMethod(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->bodyRegex('/wrong/')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $this->expectException(NotFountSuitableFixtureException::class);

        $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'body' => 'foobar body text',
        ]);
    }
}
