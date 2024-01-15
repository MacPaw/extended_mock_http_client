<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\ExtendedMockHttpClient\Request;

use ExtendedMockHttpClient\ExtendedMockHttpClient;
use ExtendedMockHttpClient\Tests\Fixture\Application\Builder\HttpFixtureBuilder;
use ExtendedMockHttpClient\Tests\Fixture\Application\HttpFixture\Request\Comparator\CustomComparator;
use ExtendedMockHttpClient\Tests\Functional\AbstractFunctionalTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class ReadmeExamplesTest extends AbstractFunctionalTestCase
{
    /**
     * @var ExtendedMockHttpClient
     */
    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = self::getContainerService(ExtendedMockHttpClient::class);
    }

    /**
     * Create simple request using createFixture
     * Request with almost empty parameters
     * Check response and check called times
     */
    public function testSimpleExample1(): void
    {
        $httpFixture = $this->client->createFixture(
            'POST',
            'https://test.test/foo?foo=bar',
            null,
            null,
            200,
            'ok'
        );
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('POST', 'https://test.test/foo?foo=bar');

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('ok', $response->getContent());
        self::assertEquals(1, $httpFixture->getCalledTimes());
    }

    /**
     * Make simple fixture using createFixture
     * Request using json
     * Check response
     */
    public function testSimpleExample2(): void
    {
        $httpFixture = $this->client->createFixture(
            'POST',
            'https://test.test/foo?foo=bar',
            '{"foo":"bar","baz":123}',
            [
                'x-header' => 'x-value',
            ],
            200,
            'ok'
        );
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('POST', 'https://test.test/foo?foo=bar', [
            'json' => [
                'foo' => 'bar',
                'baz' => 123
            ],
            'headers' => [
                'x-header' => 'x-value',
            ]
        ]);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('ok', $response->getContent());
    }

    /**
     * Make fixture using builder
     * Request using json
     * Check response
     */
    public function testBuilderExample1(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();

        $httpFixture = $builder
            ->request(
                $builder->method(['PUT', 'POST']),
                $builder->url('https://test.test/foo'),
                $builder->query([
                    'foo' => 'bar',
                ]),
                $builder->body($builder->jsonToArray(
                    $builder->arrayContain([
                        'foo' => 'bar',
                    ])
                )),
                $builder->headers([
                    'x-header' => 'x-value',
                ])
            )
            ->response(200, 'ok')
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('POST', 'https://test.test/foo?foo=bar', [
            'json' => [
                'foo' => 'bar',
                'baz' => 123
            ],
            'headers' => [
                'x-header' => 'x-value',
            ]
        ]);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('ok', $response->getContent());
    }

    /**
     * Make fixture using builder with MockResponse
     * Request using json
     * Check response
     */
    public function testBuilderExample2(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();

        $httpFixture = $builder
            ->request(
                $builder->method('POST'),
                $builder->url('https://test.test/foo'),
                $builder->query($builder->queryToArray($builder->arrayContain([
                    'foo' => 'bar',
                ]))),
                $builder->body($builder->stringRegex('/"foo":"bar"/')),
                $builder->headers([
                    'x-header' => 'x-value',
                ])
            )
            ->response(new MockResponse('ok', ['http_code' => 200]))
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('POST', 'https://test.test/foo?foo=bar', [
            'json' => [
                'foo' => 'bar',
                'baz' => 123
            ],
            'headers' => [
                'x-header' => 'x-value',
            ]
        ]);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('ok', $response->getContent());
    }

    /**
     * Make fixture using builder with callbacks in request and response
     * Request using json
     * Check response
     */
    public function testCallbackExample(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();

        $httpFixture = $builder
            ->request(
                $builder->method($builder->callback(function (string $method): bool {
                    return $method === 'POST';
                })),
                $builder->url($builder->callback(function (string $url): bool {
                    return $url === 'https://test.test/foo';
                })),
                $builder->query(
                    $builder->callback(function (string $query): bool {
                        return $query === 'foo=bar';
                    }),
                    $builder->queryToArray(
                        $builder->callback(function (array $arrayQuery): bool {
                            return array_key_exists('foo', $arrayQuery);
                        })
                    )
                ),
                $builder->body($builder->callback(function (string $jsonBody): bool {
                    $arrayBody = json_decode($jsonBody, true);

                    return isset($arrayBody['foo']);
                })),
                $builder->headers($builder->callback(function (array $headers): bool {
                    return array_key_exists('x-header', $headers);
                }))
            )
            ->response(
                function (string $method, string $url, string $query, string $body, array $headers): MockResponse {
                    $stringHeaders = [];
                    foreach ($headers as $key => $value) {
                        $stringHeaders[] = "$key: $value";
                    }

                    return new MockResponse(json_encode([
                        'method' => $method,
                        'url' => $url,
                        'query' => $query,
                        'body' => $body,
                        'headers' => $headers,
                    ]));
                }
            )
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('POST', 'https://test.test/foo?foo=bar', [
            'json' => [
                'foo' => 'bar',
                'baz' => 123
            ],
            'headers' => [
                'x-header' => 'x-value',
            ]
        ]);

        self::assertEquals(200, $response->getStatusCode());

        $responseArray = json_decode($response->getContent(), true);
        self::assertEquals('POST', $responseArray['method']);
        self::assertEquals('https://test.test/foo', $responseArray['url']);
        self::assertEquals('foo=bar', $responseArray['query']);
        self::assertEquals('{"foo":"bar","baz":123}', $responseArray['body']);
        self::assertArrayHasKey('x-header', $responseArray['headers']);
    }

    /**
     * Make fixture using builder with custom comparator
     * Request using string body
     * Check response
     */
    public function testCustomComparator(): void
    {
        $builder = $this->client->getHttpFixtureBuilder();

        $httpFixture = $builder
            ->request(
                $builder->body(new CustomComparator('foo', 'bar'))
            )
            ->response(200, 'ok')
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test', [
            'body' => 'foo.bar'
        ]);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('ok', $response->getContent());
    }

    /**
     * Make fixture using overwrote builder with custom comparator
     * Request using string body
     * Check response
     */
    public function testBuilderOverwrote(): void
    {
        /** @var HttpFixtureBuilder $builder */
        $builder = $this->client->getHttpFixtureBuilder();

        $httpFixture = $builder
            ->request(
                $builder->body($builder->custom('foo', 'bar'))
            )
            ->response(200, 'ok')
            ->build();
        $this->client->addFixture($httpFixture);

        $response = $this->client->request('GET', 'https://test.test', [
            'body' => 'foo.bar'
        ]);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('ok', $response->getContent());
    }
}
