# ExtendedMockHttpClient

| Version | Build Status | Code Coverage |
|:---------:|:-------------:|:-----:|
| `master`| [![CI][master Build Status Image]][master Build Status] | [![Coverage Status][master Code Coverage Image]][master Code Coverage] |

## Install
```shell script
composer require macpaw/extended_mock_http_client
```

## How to use

In config file `config/services_test.yaml` replace current HTTP client service
```yaml
imports:
    - { resource: services.yaml }

services:
    _defaults:
        autowire: true
        autoconfigure: true
        public: true
    http_client_service_name:
        class: ExtendedMockHttpClient\ExtendedMockHttpClient
        arguments:
            - 'https://test.host'
```

That's all, you can use it in PHPUnit tests

## Examples

#### Simple examples

```php
abstract class AbstractFunctionalTest extends KernelTestCase
{
    private ExtendedMockHttpClient $httpClient

    protected function setUp(): void
    {
        /** @var ExtendedMockHttpClient $mockHttpClient */
        $this->httpClient = self::getContainer()->get('http_client_service_name');
    }
}

class MyTest extends AbstractFunctionalTest
{
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
}
```

#### Using builder examples

```php
class MyTest extends AbstractFunctionalTest
{
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
}
```

#### Using callbacks in request and response examples

```php
class MyTest extends AbstractFunctionalTest
{
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
}
```

#### Hot to register custom Comparator

Create comparator class, it should implement `ComparatorInterface`

```php
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\ComparatorInterface;

class CustomComparator implements ComparatorInterface
{
    /**
     * @var string
     */
    private $stringPart1;

    /**
     * @var string
     */
    private $stringPart2;

    public static function getName(): string
    {
        return 'custom';
    }

    public function __construct(string $stringPart1, string $stringPart2)
    {
        $this->stringPart1 = $stringPart1;
        $this->stringPart2 = $stringPart2;
    }

    public function __invoke($value): bool
    {
        return $value === "$this->stringPart1.$this->stringPart2";
    }
}
```

Overwrite `HttpFixtureFactory` for adding where you can use the new comparator

```yaml
services:
    ExtendedMockHttpClient\Factory\HttpFixtureFactory:
        arguments:
            - '%allowed_nested_keys%'
        calls:
            - add: ['body', 'custom']
            - add: ['method', 'custom']
            - add: ['query', 'custom']
            ...
```

Use the new comparator in test

```php
class MyTest extends AbstractFunctionalTest
{
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
}
```

#### Hot to overwrite HttpFixtureBuilderFactory for using more useful builder method

Create custom builder class which based on original builder

```php
use ExtendedMockHttpClient\Builder\HttpFixtureBuilder as BaseHttpFixtureBuilder;
use ExtendedMockHttpClient\Tests\Fixture\Application\HttpFixture\Request\Comparator\CustomComparator;

class HttpFixtureBuilder extends BaseHttpFixtureBuilder
{
    public function custom(string $stringPart1, string $stringPart2): CustomComparator
    {
        return new CustomComparator($stringPart1, $stringPart2);
    }
}
```

Create custom builder factory class which based on original builder factory

```php
use ExtendedMockHttpClient\Factory\HttpFixtureBuilderFactory as BaseHttpFixtureBuilderFactory;
use ExtendedMockHttpClient\Builder\HttpFixtureBuilder as BaseHttpFixtureBuilder;
use ExtendedMockHttpClient\Tests\Fixture\Application\Builder\HttpFixtureBuilder;

class HttpFixtureBuilderFactory extends BaseHttpFixtureBuilderFactory
{
    public function create(): BaseHttpFixtureBuilder
    {
        return new HttpFixtureBuilder($this->httpFixtureFactory);
    }
}
```

Overwrite builder factory service
```yaml
services:
    ExtendedMockHttpClient\Factory\HttpFixtureBuilderFactory:
        class: ExtendedMockHttpClient\Tests\Fixture\Application\Factory\HttpFixtureBuilderFactory
```

Use updated builder in tests

```php
class MyTest extends AbstractFunctionalTest
{
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
```

## Todo list
* Add support jms serializer 
* Add history function
  * Get last request/response (or by index)
  * Some kind of assert, it should check that history contain some request
* Add possibility to load fixtures from array/yaml
* Add logger and log every steps for easiest debug

[master Build Status]: https://github.com/macpaw/ExtendedMockHttpClient/actions?query=workflow%3ACI+branch%3Amaster
[master Build Status Image]: https://github.com/macpaw/ExtendedMockHttpClient/workflows/CI/badge.svg?branch=master
[master Code Coverage]: https://codecov.io/gh/macpaw/ExtendedMockHttpClient/branch/master
[master Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/ExtendedMockHttpClient/master?logo=codecov
