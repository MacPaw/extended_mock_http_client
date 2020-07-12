<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional;

use ExtendedMockHttpClient\Builder\RequestMockBuilder;
use ExtendedMockHttpClient\ExtendedMockHttpClient;
use ExtendedMockHttpClient\Model\HttpFixture;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class ExtendedMockHttpClientBaseTest extends TestCase
{
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
}
