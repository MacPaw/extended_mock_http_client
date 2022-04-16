<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\Factory;

use ExtendedMockHttpClient\Excpetion\Factory\HttpFixtureFactory\NotFoundKeyInAllowedNestedKeysException;
use ExtendedMockHttpClient\Excpetion\Factory\HttpFixtureFactory\RequiredKeyNotFoundException;
use ExtendedMockHttpClient\Factory\HttpFixtureFactory;
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\AndComparator;
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\ArrayCountComparator;
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\InArrayComparator;
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\StringEqualsComparator;
use ExtendedMockHttpClient\Tests\Functional\AbstractFunctionalTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class HttpFixtureFactoryTest extends AbstractFunctionalTestCase
{
    /**
     * @param array<mixed> $array
     *
     * @dataProvider validDataProvider
     */
    public function testSuccess(array $array): void
    {
        $factory = self::getContainer()->get(HttpFixtureFactory::class);

        $factory->createFromArray($array);

        $this->expectNotToPerformAssertions();
    }

    public function validDataProvider(): array
    {
        return [
            [[
                'request' => [],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [],
                'response' => new MockResponse('ok', [
                    'http_code' => 200
                ])
            ]],
            [[
                'request' => [],
                'response' => new MockResponse('ok', [
                    'http_code' => 200,
                    'response_headers' => [
                        'x-header' => 'x-value',
                    ],
                ])
            ]],
            [[
                'request' => [
                    'method' => new StringEqualsComparator('GET'),
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'method' => new AndComparator(
                        new StringEqualsComparator('GET')
                    ),
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'url' => new StringEqualsComparator('https://test.test'),
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'url' => new AndComparator(
                        new StringEqualsComparator('https://test.test')
                    ),
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'query' => new StringEqualsComparator('foo=bar&baz=123'),
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'query' => new AndComparator(
                        new StringEqualsComparator('foo=bar&baz=123')
                    ),
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'body' => new StringEqualsComparator('body'),
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'body' => new AndComparator(
                        new StringEqualsComparator('body')
                    ),
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'headers' => new ArrayCountComparator(1),
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'headers' => new AndComparator(
                        new ArrayCountComparator(1)
                    ),
                ],
                'response' => new MockResponse('')
            ]],
        ];
    }

    /**
     * @param array<mixed> $array
     *
     * @dataProvider requiredKeyNotFoundExceptionDataProvider
     */
    public function testRequiredKeyNotFoundException(array $array): void
    {
        $factory = self::getContainer()->get(HttpFixtureFactory::class);

        $this->expectException(RequiredKeyNotFoundException::class);
        $factory->createFromArray($array);
    }

    public function requiredKeyNotFoundExceptionDataProvider(): array
    {
        return [
            [[
            ]],
            [[
                'request' => []
            ]],
            [[
                'response' => new MockResponse('')
            ]],
        ];
    }

    /**
     * @param array<mixed> $array
     *
     * @dataProvider notFoundKeyInAllowedNestedKeysExceptionDataProvider
     */
    public function testNotFoundKeyInAllowedNestedKeysException(array $array): void
    {
        $factory = self::getContainer()->get(HttpFixtureFactory::class);

        $this->expectException(NotFoundKeyInAllowedNestedKeysException::class);
        $factory->createFromArray($array);
    }

    public function notFoundKeyInAllowedNestedKeysExceptionDataProvider(): array
    {
        return [
            [[
                'request' => [
                    'request' => []
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'response' => []
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'response' => new MockResponse('')
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'method' => [
                        new ArrayCountComparator(1)
                    ]
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'method' => [
                        new ArrayCountComparator(1)
                    ]
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    'headers' => [
                        new InArrayComparator(['test'])
                    ]
                ],
                'response' => new MockResponse('')
            ]],
            [[
                'request' => [
                    new InArrayComparator(['test'])
                ],
                'response' => new MockResponse('')
            ]],
        ];
    }
}
