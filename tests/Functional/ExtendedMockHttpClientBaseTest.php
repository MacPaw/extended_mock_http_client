<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional;

use ExtendedMockHttpClient\Builder\RequestMockBuilder;
use ExtendedMockHttpClient\Comparators\ArrayComparator;
use ExtendedMockHttpClient\Comparators\CallbackComparator;
use ExtendedMockHttpClient\Comparators\JsonComparator;
use ExtendedMockHttpClient\Comparators\OrComparator;
use ExtendedMockHttpClient\Comparators\RegexComparator;
use ExtendedMockHttpClient\Comparators\StringEqualsComparator;
use ExtendedMockHttpClient\Comparators\UrlComparator;
use ExtendedMockHttpClient\Excpetion\NotFountSuitableFixtureException;
use ExtendedMockHttpClient\ExtendedMockHttpClient;
use ExtendedMockHttpClient\Model\HttpFixture;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class ExtendedMockHttpClientBaseTest extends TestCase
{
    public function testSuccess(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->addMethodComparator(new OrComparator([
                    new StringEqualsComparator('GET'),
                    new StringEqualsComparator('POST'),
                ]))
                ->addUrlComparator(new UrlComparator([
                    new RegexComparator('/test.test\/foo\/bar/')
                ]))
                ->addBodyComparator(new JsonComparator([
                    new CallbackComparator(function (array $data): bool {
                        return isset($data['foo']) && $data['foo'] === 'bar';
                    })
                ]))
                ->addHeadersComparator(new ArrayComparator([
                    'x-header-name' => 'Qwerty',
                    'content-type' => 'application/json',
                ]))
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'headers' => [
                'X-header-name' => 'Qwerty',
            ],
            'json' => [
                'foo' => 'bar',
                'int' => 1,
            ],
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function testSuccessUrlEquals(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->urlEquals('http://test.test/foo/bar')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'body' => '{"foo": "bar", "int": 1}'
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function testSuccessBodyEquals(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->bodyEquals('{"foo": "bar", "int": 1}')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'body' => '{"foo": "bar", "int": 1}'
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function testSuccessMethodEquals(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');

        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->methodEquals('GET')
                ->urlEquals('http://test.test/api/v1/list')
                ->queryShouldContain('page', '1')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->methodEquals('POST')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'body' => '{"foo": "bar", "int": 1}'
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function testSuccessQueryShouldContain(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->queryShouldContain('qwe', 'rty')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'body' => '{"foo": "bar", "int": 1}'
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function testSuccessHeadersShouldContain(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->headersShouldContain('X-header-name', 'Qwerty')
                ->headersShouldContain('Content-type', 'application/json')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'headers' => [
                'X-header-name' => 'Qwerty',
            ],
            'json' => [
                'foo' => 'bar',
                'int' => 1
            ]
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function testSuccessWithoutAnyRequestComparators(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('GET', 'http://test.test/foo/bar');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function testErrorNotFountSuitableFixtureException(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->methodEquals('POST')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $this->expectException(NotFountSuitableFixtureException::class);

        $client->request('GET', 'http://test.test/foo/bar');
    }
}
