<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional;

use ExtendedMockHttpClient\Builder\RequestMockBuilder;
use ExtendedMockHttpClient\Comparators\ArrayCountComparator;
use ExtendedMockHttpClient\Comparators\JsonComparator;
use ExtendedMockHttpClient\Excpetion\NotFountSuitableFixtureException;
use ExtendedMockHttpClient\ExtendedMockHttpClient;
use ExtendedMockHttpClient\Model\HttpFixture;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class ExtendedMockHttpClientArrayCountTest extends TestCase
{
    public function testSuccess(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->addBodyComparator(new JsonComparator([
                    new ArrayCountComparator(3),
                ]))
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'json' => ['value1', 'value2', 'value3'],
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function testFail(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->addBodyComparator(new JsonComparator(new ArrayCountComparator(1)))
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $this->expectException(NotFountSuitableFixtureException::class);

        $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'json' => ['value1', 'value2', 'value3'],
        ]);
    }

    public function testSuccessAssociativeArray(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->addBodyComparator(new JsonComparator(new ArrayCountComparator(3)))
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'json' => [
                'test1' => 'value1',
                'test2' => 'value2',
                'test3' => 'value3',
            ],
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function testFailAssociativeArray(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->addBodyComparator(new JsonComparator(new ArrayCountComparator(1)))
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $this->expectException(NotFountSuitableFixtureException::class);

        $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'json' => [
                'test1' => 'value1',
                'test2' => 'value2',
                'test3' => 'value3',
            ],
        ]);
    }
}
