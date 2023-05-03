<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Builder;

use ExtendedMockHttpClient\Enum\RequestKey;
use ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder\InvalidMethodArgumentException;
use ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder\InvalidRequestKeyException;
use ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder\InvalidResponseArgumentTypeException;
use ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder\InvalidResponseMethodArgumentCountException;
use ExtendedMockHttpClient\Factory\HttpFixtureFactory;
use ExtendedMockHttpClient\HttpFixture\HttpFixture;
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\AndComparator;
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\ArrayContainComparator;
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\ArrayCountComparator;
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\CallbackComparator;
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\InArrayComparator;
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\OrComparator;
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\StringEqualsComparator;
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\StringRegexComparator;
use ExtendedMockHttpClient\HttpFixture\Request\DataTransformer\FormDataToArrayDataTransformer;
use ExtendedMockHttpClient\HttpFixture\Request\DataTransformer\JsonToArrayDataTransformer;
use ExtendedMockHttpClient\HttpFixture\Request\DataTransformer\QueryToArrayDataTransformer;
use ExtendedMockHttpClient\HttpFixture\Request\DataTransformer\UrlToArrayDataTransformer;
use ExtendedMockHttpClient\HttpFixture\Request\HttpFixtureRequestItemInterface;
use Symfony\Component\HttpClient\Response\MockResponse;

class HttpFixtureBuilder
{
    /**
     * @var HttpFixtureFactory
     */
    private $httpFixtureFactory;

    /**
     * @var array<string, mixed>
     */
    private $httpFixtureRequestItems;

    public function __construct(HttpFixtureFactory $httpFixtureFactory)
    {
        $this->httpFixtureFactory = $httpFixtureFactory;
        $this->httpFixtureRequestItems = [
            'request' => [],
            'response' => new MockResponse('', [ 'http_code' => 204 ])
        ];
    }

    /**
     * @example $builder->request(
     *     $builder->method('GET'),
     *     $builder->body('test'),
     *     ...
     * )
     *
     * @throws InvalidRequestKeyException
     */
    public function request(...$arguments): self
    {
        foreach ($arguments as $argument) {
            foreach ($argument as $key => $item) {
                if (RequestKey::tryFrom($key) === null) {
                    throw new InvalidRequestKeyException(RequestKey::values(), $key);
                }

                $this->httpFixtureRequestItems['request'] = array_merge(
                    $this->httpFixtureRequestItems['request'],
                    [ $key => $item ]
                );
            }
        }

        return $this;
    }

    /**
     * @param HttpFixtureRequestItemInterface|string|array ...$arguments
     * @example $builder->method('GET')
     * @example $builder->method(['GET', 'POST'])
     * @example $builder->method($builder->stringRegex('/(GET|POST)/'))
     *
     * @return array<string, HttpFixtureRequestItemInterface[]>
     *
     * @throws InvalidMethodArgumentException
     */
    public function method(...$arguments): array
    {
        $result = [];

        foreach ($arguments as $argument) {
            if ($argument instanceof HttpFixtureRequestItemInterface) {
                $result[] = $argument;
            } elseif (is_array($argument)) {
                $result[] = $this->inArray($argument);
            } elseif (is_string($argument)) {
                $result[] = $this->stringEquals($argument);
            } else {
                throw new InvalidMethodArgumentException(
                    'method',
                    [HttpFixtureRequestItemInterface::class, 'string', 'array'],
                    $argument
                );
            }
        }

        return ['method' => $result];
    }

    /**
     * @param HttpFixtureRequestItemInterface|string|array ...$arguments
     * @example $builder->url('https://test.test/url')
     * @example $builder->url(['https://test.test/url1', 'https://test.test/url1'])
     * @example $builder->url($builder->stringRegex('/^https/'))
     * @example $builder->url($builder->stringRegex('/^https/'), $builder->stringRegex('/test.test/'))
     *
     * @return array<string, HttpFixtureRequestItemInterface[]>
     *
     * @throws InvalidMethodArgumentException
     */
    public function url(...$arguments): array
    {
        $result = [];

        foreach ($arguments as $argument) {
            if ($argument instanceof HttpFixtureRequestItemInterface) {
                $result[] = $argument;
            } elseif (is_array($argument)) {
                $result[] = new InArrayComparator($argument);
            } elseif (is_string($argument)) {
                $result[] = new StringEqualsComparator($argument);
            } else {
                throw new InvalidMethodArgumentException(
                    'url',
                    [HttpFixtureRequestItemInterface::class, 'string', 'array'],
                    $argument
                );
            }
        }

        return ['url' => $result];
    }

    /**
     * @param HttpFixtureRequestItemInterface|string|array ...$arguments
     * @example $builder->query('foo=bar&baz=123')
     * @example $builder->query([ 'foo' => 'bar', 'baz' => '123' ])
     * @example $builder->query($builder->queryToArray($this->arrayContain([ 'foo' => 'bar', 'baz' => '123' ])))
     * @example $builder->query($builder->stringRegex('/^foo/'))
     *
     * @return array<string, HttpFixtureRequestItemInterface[]>
     *
     * @throws InvalidMethodArgumentException
     */
    public function query(...$arguments): array
    {
        $result = [];

        foreach ($arguments as $argument) {
            if ($argument instanceof HttpFixtureRequestItemInterface) {
                $result[] = $argument;
            } elseif (is_array($argument)) {
                $result[] = $this->queryToArray($this->arrayContain($argument));
            } elseif (is_string($argument)) {
                $result[] = $this->stringEquals($argument);
            } else {
                throw new InvalidMethodArgumentException(
                    'url',
                    [HttpFixtureRequestItemInterface::class, 'string', 'array'],
                    $argument
                );
            }
        }

        return ['query' => $result];
    }

    /**
     * @param HttpFixtureRequestItemInterface|string|array ...$arguments
     * @example $builder->body('ok')
     * @example $builder->body([ 'ok', '{"result": "ok"}' ])
     * @example $builder->body($builder->stringRegex('/ok/'))
     * @example $builder->body($builder->jsonToArray($this->arrayContain([ 'result' => 'ok' ])))
     *
     * @return array<string, HttpFixtureRequestItemInterface[]>
     *
     * @throws InvalidMethodArgumentException
     */
    public function body(...$arguments): array
    {
        $result = [];

        foreach ($arguments as $argument) {
            if ($argument instanceof HttpFixtureRequestItemInterface) {
                $result[] = $argument;
            } elseif (is_array($argument)) {
                $result[] = $this->inArray($argument);
            } elseif (is_string($argument)) {
                $result[] = $this->stringEquals($argument);
            } else {
                throw new InvalidMethodArgumentException(
                    'body',
                    [HttpFixtureRequestItemInterface::class, 'string', 'array'],
                    $argument
                );
            }
        }

        return ['body' => $result];
    }

    /**
     * @param HttpFixtureRequestItemInterface|array ...$arguments
     * @example $builder->headers([ 'x-header' => 'x-value' ])
     * @example $builder->headers($this->arrayContain([ 'x-header' => 'x-value' ]))
     *
     * @return array<string, HttpFixtureRequestItemInterface[]>
     *
     * @throws InvalidMethodArgumentException
     */
    public function headers(...$arguments): array
    {
        $result = [];

        foreach ($arguments as $argument) {
            if ($argument instanceof HttpFixtureRequestItemInterface) {
                $result[] = $argument;
            } elseif (is_array($argument)) {
                $result[] = $this->arrayContain($argument);
            } else {
                throw new InvalidMethodArgumentException(
                    'headers',
                    [HttpFixtureRequestItemInterface::class, 'array'],
                    $argument
                );
            }
        }

        return ['headers' => $result];
    }

    /**
     * @param HttpFixtureRequestItemInterface ...$items
     */
    public function and(...$items): AndComparator
    {
        return new AndComparator(...$items);
    }

    /**
     * @param HttpFixtureRequestItemInterface ...$items
     */
    public function or(...$items): OrComparator
    {
        return new OrComparator(...$items);
    }

    public function stringEquals(string $value): StringEqualsComparator
    {
        return new StringEqualsComparator($value);
    }

    public function stringRegex(string $value): StringRegexComparator
    {
        return new StringRegexComparator($value);
    }

    public function arrayContain(array $value): ArrayContainComparator
    {
        return new ArrayContainComparator($value);
    }

    public function inArray(array $value): InArrayComparator
    {
        return new InArrayComparator($value);
    }

    public function arrayCount(int $count): ArrayCountComparator
    {
        return new ArrayCountComparator($count);
    }

    public function callback(callable $callback): CallbackComparator
    {
        return new CallbackComparator($callback);
    }

    /**
     * @param HttpFixtureRequestItemInterface ...$items
     */
    public function jsonToArray(...$items): JsonToArrayDataTransformer
    {
        return new JsonToArrayDataTransformer(...$items);
    }

    /**
     * @param HttpFixtureRequestItemInterface ...$items
     */
    public function formDataToArray(...$items): FormDataToArrayDataTransformer
    {
        return new FormDataToArrayDataTransformer(...$items);
    }

    /**
     * @param HttpFixtureRequestItemInterface ...$items
     */
    public function queryToArray(...$items): QueryToArrayDataTransformer
    {
        return new QueryToArrayDataTransformer(...$items);
    }

    /**
     * @param HttpFixtureRequestItemInterface ...$items
     */
    public function urlToArray(...$items): UrlToArrayDataTransformer
    {
        return new UrlToArrayDataTransformer(...$items);
    }

    /**
     * @param MockResponse|int|string|array|callable $arguments
     * @example $builder->response(new MockResponse(...))
     * @example $builder->response(204)
     * @example $builder->response(200, 'ok')
     * @example $builder->response(200, 'ok', [ 'my_header' => 'test' ])
     * @example $builder->response(function(MockRequest $request): MockResponse {
     *     ...
     * })
     *
     * @throws InvalidResponseMethodArgumentCountException
     * @throws InvalidResponseArgumentTypeException
     */
    public function response(...$arguments): self
    {
        $argumentsCount = count($arguments);

        if ($argumentsCount < 1 || $argumentsCount > 3) {
            throw new InvalidResponseMethodArgumentCountException(1, 3, $argumentsCount);
        }

        if ($argumentsCount === 1) {
            $argument = array_shift($arguments);

            if ($argument instanceof MockResponse) {
                $response = $argument;
            } elseif (is_int($argument)) {
                $response = new MockResponse('', ['http_code' => $argument]);
            } elseif (is_callable($argument)) {
                $response = $argument;
            } else {
                throw new InvalidResponseArgumentTypeException(1, [
                    MockResponse::class, 'int', 'callable'
                ], $argument);
            }
        } else {
            $statusCode = array_shift($arguments);
            $body = array_shift($arguments);
            $headers = $argumentsCount === 3 ? array_shift($arguments) : [];

            if (!is_int($statusCode)) {
                throw new InvalidResponseArgumentTypeException(1, ['int'], $statusCode);
            }

            if (!is_string($body)) {
                throw new InvalidResponseArgumentTypeException(2, ['string'], $body);
            }

            if (!is_array($headers)) {
                throw new InvalidResponseArgumentTypeException(3, ['array'], $headers);
            }

            $response = new MockResponse(
                $body,
                [
                    'http_code' => $statusCode,
                    'response_headers' => $headers,
                ]
            );
        }

        $this->httpFixtureRequestItems['response'] = $response;

        return $this;
    }

    public function build(): HttpFixture
    {
        return $this->httpFixtureFactory->createFromArray($this->httpFixtureRequestItems);
    }
}
